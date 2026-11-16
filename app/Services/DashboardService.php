<?php

namespace App\Services;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Models\Carrier;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Compile comprehensive operational & executive metrics for Admin and Operator dashboards.
     *
     * @return array<string, mixed>
     */
    public function getAdminOperatorMetrics(): array
    {
        // 1. Metric Cards Aggregations (Single DB pass where possible to avoid N+1)
        $totalShipments = Shipment::count();
        $inTransit = Shipment::where('current_status', ShipmentStatus::InTransit->value)->count();
        $delivered = Shipment::where('current_status', ShipmentStatus::Delivered->value)->count();
        $delayed = Shipment::where('delay_status', '!=', DelayStatus::OnTime->value)->count();
        $openExceptions = ShipmentException::whereIn('status', ['OPEN', 'INVESTIGATING'])->count();

        // 2. Chart: Shipments by Status
        $rawStatusCounts = Shipment::query()
            ->select('current_status', DB::raw('count(*) as aggregate'))
            ->groupBy('current_status')
            ->pluck('aggregate', 'current_status')
            ->toArray();

        $statusChart = [
            'labels' => [],
            'data' => [],
        ];
        foreach (ShipmentStatus::cases() as $status) {
            $statusChart['labels'][] = $status->label();
            $statusChart['data'][] = $rawStatusCounts[$status->value] ?? 0;
        }

        // 3. Chart: Shipments by Carrier
        $carrierCounts = Carrier::query()
            ->withCount('shipments')
            ->orderByDesc('shipments_count')
            ->take(8)
            ->get();

        $carrierChart = [
            'labels' => $carrierCounts->pluck('name')->toArray(),
            'data' => $carrierCounts->pluck('shipments_count')->toArray(),
        ];

        // 4. Chart: Delay Breakdown
        $rawDelayCounts = Shipment::query()
            ->select('delay_status', DB::raw('count(*) as aggregate'))
            ->groupBy('delay_status')
            ->pluck('aggregate', 'delay_status')
            ->toArray();

        $delayChart = [
            'labels' => [],
            'data' => [],
        ];
        foreach (DelayStatus::cases() as $dStatus) {
            $delayChart['labels'][] = $dStatus->label();
            $delayChart['data'][] = $rawDelayCounts[$dStatus->value] ?? 0;
        }

        // 5. Chart: Exception Types Breakdown
        $rawExceptionCounts = ShipmentException::query()
            ->select('type', DB::raw('count(*) as aggregate'))
            ->groupBy('type')
            ->pluck('aggregate', 'type')
            ->toArray();

        $exceptionChart = [
            'labels' => array_keys($rawExceptionCounts),
            'data' => array_values($rawExceptionCounts),
        ];

        // 6. KPIs: On-Time Delivery Rate, Average Delay Duration, Exception Resolution Time
        $onTimeDeliveredCount = Shipment::where('current_status', ShipmentStatus::Delivered->value)
            ->where('delay_status', DelayStatus::OnTime->value)
            ->count();

        if ($delivered > 0) {
            $onTimeDeliveryRate = round(($onTimeDeliveredCount / $delivered) * 100, 1);
        } else {
            // If no shipments delivered yet, compute on-time percentage across all active shipments
            $totalOnTime = $rawDelayCounts[DelayStatus::OnTime->value] ?? 0;
            $onTimeDeliveryRate = $totalShipments > 0 ? round(($totalOnTime / $totalShipments) * 100, 1) : 100.0;
        }

        $avgDelayDuration = round((float) (Shipment::where('delay_hours', '>', 0)->avg('delay_hours') ?? 0.0), 1);

        $resolvedExceptions = ShipmentException::where('status', 'RESOLVED')
            ->whereNotNull('resolved_at')
            ->get(['detected_at', 'resolved_at']);

        $avgResolutionHours = $resolvedExceptions->isEmpty()
            ? 0.0
            : round((float) $resolvedExceptions->avg(fn ($ex) => $ex->detected_at->diffInHours($ex->resolved_at)), 1);

        // 7. Recent Items for Feed / Tables
        $recentExceptions = ShipmentException::with(['shipment.carrier', 'creator'])
            ->whereIn('status', ['OPEN', 'INVESTIGATING'])
            ->latest('detected_at')
            ->take(5)
            ->get();

        $recentShipments = Shipment::with(['carrier', 'originLocation', 'destinationLocation', 'customer'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        return [
            'total_shipments' => $totalShipments,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'delayed' => $delayed,
            'open_exceptions' => $openExceptions,
            'status_chart' => $statusChart,
            'carrier_chart' => $carrierChart,
            'delay_chart' => $delayChart,
            'exception_chart' => $exceptionChart,
            'kpis' => [
                'on_time_delivery_rate' => $onTimeDeliveryRate,
                'avg_delay_duration_hours' => $avgDelayDuration,
                'avg_exception_resolution_hours' => $avgResolutionHours,
                'exception_rate' => $totalShipments > 0 ? round((Shipment::has('exceptions')->count() / $totalShipments) * 100, 1) : 0.0,
                'delay_rate' => $totalShipments > 0 ? round(($delayed / $totalShipments) * 100, 1) : 0.0,
            ],
            'recent_exceptions' => $recentExceptions,
            'recent_shipments' => $recentShipments,
        ];
    }

    /**
     * Compile customer-specific metrics scoped strictly to their owned shipments.
     *
     * @return array<string, mixed>
     */
    public function getCustomerMetrics(User $customer): array
    {
        $baseQuery = $customer->shipments();

        $totalShipments = (clone $baseQuery)->count();
        $inTransit = (clone $baseQuery)->where('current_status', ShipmentStatus::InTransit->value)->count();
        $delivered = (clone $baseQuery)->where('current_status', ShipmentStatus::Delivered->value)->count();
        $delayed = (clone $baseQuery)->where('delay_status', '!=', DelayStatus::OnTime->value)->count();

        $openExceptions = ShipmentException::whereHas('shipment', function ($q) use ($customer) {
            $q->where('customer_id', $customer->id);
        })->whereIn('status', ['OPEN', 'INVESTIGATING'])->count();

        // Customer Status Chart
        $rawStatusCounts = (clone $baseQuery)
            ->select('current_status', DB::raw('count(*) as aggregate'))
            ->groupBy('current_status')
            ->pluck('aggregate', 'current_status')
            ->toArray();

        $statusChart = [
            'labels' => [],
            'data' => [],
        ];
        foreach (ShipmentStatus::cases() as $status) {
            $statusChart['labels'][] = $status->label();
            $statusChart['data'][] = $rawStatusCounts[$status->value] ?? 0;
        }

        // Customer KPIs
        $onTimeDeliveredCount = (clone $baseQuery)
            ->where('current_status', ShipmentStatus::Delivered->value)
            ->where('delay_status', DelayStatus::OnTime->value)
            ->count();

        if ($delivered > 0) {
            $onTimeRate = round(($onTimeDeliveredCount / $delivered) * 100, 1);
        } else {
            $onTimeTotal = (clone $baseQuery)->where('delay_status', DelayStatus::OnTime->value)->count();
            $onTimeRate = $totalShipments > 0 ? round(($onTimeTotal / $totalShipments) * 100, 1) : 100.0;
        }

        // Recent customer shipments & notifications
        $recentShipments = (clone $baseQuery)
            ->with(['carrier', 'originLocation', 'destinationLocation', 'exceptions'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $recentNotifications = $customer->notifications()
            ->with('shipment')
            ->take(5)
            ->get();

        return [
            'total_shipments' => $totalShipments,
            'in_transit' => $inTransit,
            'delivered' => $delivered,
            'delayed' => $delayed,
            'open_exceptions' => $openExceptions,
            'status_chart' => $statusChart,
            'kpis' => [
                'on_time_delivery_rate' => $onTimeRate,
                'delay_rate' => $totalShipments > 0 ? round(($delayed / $totalShipments) * 100, 1) : 0.0,
            ],
            'recent_shipments' => $recentShipments,
            'recent_notifications' => $recentNotifications,
        ];
    }
}
