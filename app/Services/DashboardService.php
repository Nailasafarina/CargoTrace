<?php

namespace App\Services;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Models\Carrier;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Compile comprehensive operational & executive metrics for Admin and Operator dashboards.
     *
     * @return array<string, mixed>
     */
    public function getAdminOperatorMetrics(?User $user = null): array
    {
        $thresholdHours = (int) Setting::get('delay_threshold_hours', config('cargotrace.delay_threshold_hours', 24));

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

        // 7. Recent Items for Feed / Tables & Interactive Workspace
        $recentExceptions = ShipmentException::with(['shipment.carrier', 'creator'])
            ->whereIn('status', ['OPEN', 'INVESTIGATING'])
            ->latest('detected_at')
            ->take(5)
            ->get();

        $recentShipments = Shipment::with([
            'carrier',
            'originLocation',
            'destinationLocation',
            'customer',
            'creator',
            'items',
            'trackingEvents.location',
        ])
            ->latest('updated_at')
            ->take(6)
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
            'recent_shipments' => $recentShipments->take(5),
            'delay_threshold_hours' => $thresholdHours,
            'workspace_shipments' => $this->formatWorkspaceShipments($recentShipments, $user),
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
            ->with([
                'carrier',
                'originLocation',
                'destinationLocation',
                'customer',
                'creator',
                'items',
                'trackingEvents.location',
                'exceptions',
            ])
            ->latest('updated_at')
            ->take(6)
            ->get();

        $recentNotifications = $customer->notifications()
            ->with('shipment')
            ->take(5)
            ->get();

        $thresholdHours = (int) Setting::get('delay_threshold_hours', config('cargotrace.delay_threshold_hours', 24));

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
            'recent_shipments' => $recentShipments->take(5),
            'recent_notifications' => $recentNotifications,
            'delay_threshold_hours' => $thresholdHours,
            'workspace_shipments' => $this->formatWorkspaceShipments($recentShipments, $customer),
        ];
    }

    /**
     * Format shipments for the interactive cargo bay & OpenStreetMap workspace component.
     *
     * @param  Collection<int, Shipment>  $shipments
     * @return array<int, array<string, mixed>>
     */
    protected function formatWorkspaceShipments(Collection $shipments, ?User $actor = null): array
    {
        $bays = ['A9', 'A1', 'A3', 'A8', 'A10', 'A4'];

        return $shipments->values()->map(function (Shipment $sh, int $idx) use ($bays, $actor) {
            $latestEvent = $sh->trackingEvents->sortByDesc('occurred_at')->first();
            $origin = $sh->originLocation;
            $dest = $sh->destinationLocation;

            $originLat = $origin?->latitude !== null ? (float) $origin->latitude : 1.2644;
            $originLng = $origin?->longitude !== null ? (float) $origin->longitude : 103.8222;
            $destLat = $dest?->latitude !== null ? (float) $dest->latitude : 51.9500;
            $destLng = $dest?->longitude !== null ? (float) $dest->longitude : 4.1333;

            $currentLat = ($latestEvent && $latestEvent->latitude !== null)
                ? (float) $latestEvent->latitude
                : round(($originLat + $destLat) / 2, 4);
            $currentLng = ($latestEvent && $latestEvent->longitude !== null)
                ? (float) $latestEvent->longitude
                : round(($originLng + $destLng) / 2, 4);

            $currentCity = $latestEvent?->location?->city
                ?? ($sh->current_status === ShipmentStatus::Delivered ? ($dest?->city ?? 'Rotterdam') : ($origin?->city ?? 'Singapore'));
            $currentCountry = $latestEvent?->location?->country
                ?? ($sh->current_status === ShipmentStatus::Delivered ? ($dest?->country ?? 'NL') : ($origin?->country ?? 'SG'));
            $currentHub = $latestEvent?->location?->name
                ?? ($origin?->name ?? 'International Logistics Hub');

            $waypoints = $sh->trackingEvents
                ->sortBy('occurred_at')
                ->filter(fn ($ev) => $ev->latitude !== null && $ev->longitude !== null)
                ->values()
                ->map(fn ($ev) => [
                    'lat' => (float) $ev->latitude,
                    'lng' => (float) $ev->longitude,
                    'title' => $ev->location?->name ?? $ev->event_type->label(),
                    'city' => $ev->location?->city ?? '',
                    'status' => $ev->event_type->label(),
                    'time' => $ev->occurred_at?->format('d M Y, H:i') ?? '',
                    'is_exception' => (bool) $ev->is_exception,
                ])
                ->toArray();

            $digits = preg_replace('/[^0-9]/', '', $sh->shipment_number);
            $shortSuffix = strlen($digits) >= 3 ? substr($digits, -4) : str_pad((string) $sh->id, 4, '0', STR_PAD_LEFT);

            return [
                'id' => $sh->id,
                'bay' => $bays[$idx] ?? ('B'.($idx + 1)),
                'number' => $sh->shipment_number,
                'short_code' => 'SHP - '.$shortSuffix,
                'status_value' => $sh->current_status->value,
                'status_label' => $sh->current_status->label(),
                'status_short' => match ($sh->current_status) {
                    ShipmentStatus::InTransit => 'Transit',
                    ShipmentStatus::Delivered => 'Delivered',
                    ShipmentStatus::Cancelled => 'Cancelled',
                    default => 'Loading',
                },
                'is_delayed' => $sh->delay_status !== DelayStatus::OnTime,
                'delay_label' => $sh->delay_status->label(),
                'delay_hours' => (int) ($sh->delay_hours ?? 0),
                'weight_kg' => (float) ($sh->weight_kg ?? 0),
                'weight_formatted' => number_format((float) ($sh->weight_kg ?? 0), 0).'kg',
                'volume_formatted' => $sh->volume_cbm ? number_format((float) $sh->volume_cbm, 1).' m³' : '—',
                'cargo_type' => $sh->cargo_type ?? 'General Freight',
                'items_count' => max(1, $sh->items->count()),
                'carrier_name' => $sh->carrier?->name ?? 'Assigned Carrier',
                'carrier_code' => ($sh->carrier?->code ?? 'CAR').'-'.(200 + ($sh->id % 99)),
                'operator_name' => $sh->creator?->name ?? $sh->customer?->name ?? 'Operations Lead',
                'customer_name' => $sh->customer?->name ?? 'Consignee',
                'dock_label' => 'Bay '.($bays[$idx] ?? 'A9'),
                'started_time' => $sh->expected_departure_at ? $sh->expected_departure_at->format('d M, H:i') : 'Scheduled',
                'current_event_time' => $latestEvent?->occurred_at?->format('d M Y, H:i') ?? ($sh->updated_at?->format('d M Y, H:i') ?? '—'),
                'eta_formatted' => ($sh->estimated_arrival_at ?? $sh->expected_arrival_at)?->format('d M Y, H:i') ?? 'Scheduled',
                'origin_city' => $origin?->city ?? 'Origin',
                'origin_country' => $origin?->country ?? '',
                'origin_name' => $origin?->name ?? 'Origin Terminal',
                'origin_lat' => $originLat,
                'origin_lng' => $originLng,
                'dest_city' => $dest?->city ?? 'Destination',
                'dest_country' => $dest?->country ?? '',
                'dest_name' => $dest?->name ?? 'Destination Terminal',
                'dest_lat' => $destLat,
                'dest_lng' => $destLng,
                'current_city' => $currentCity,
                'current_country' => $currentCountry,
                'current_hub' => $currentHub,
                'current_lat' => $currentLat,
                'current_lng' => $currentLng,
                'waypoints' => $waypoints,
                'show_url' => route('shipments.show', $sh),
                'edit_url' => route('shipments.edit', $sh),
                'can_edit' => $actor ? $actor->can('update', $sh) : false,
            ];
        })->toArray();
    }
}
