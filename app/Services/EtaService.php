<?php

namespace App\Services;

use App\DTOs\EtaEvaluationResult;
use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Shipment;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class EtaService
{
    /**
     * Get the active delay threshold in hours (from settings table or fallback to config).
     */
    public function getThresholdHours(): int
    {
        return (int) Setting::get('delay_threshold_hours', config('cargotrace.delay_threshold_hours', 24));
    }

    /**
     * Deterministic ETA evaluation method.
     * Computes difference in hours, evaluates against classification rules and threshold.
     */
    public function evaluate(
        Shipment $shipment,
        ?CarbonInterface $estimatedArrival = null,
        ?CarbonInterface $expectedArrival = null,
        ?int $thresholdOverride = null
    ): EtaEvaluationResult {
        $expected = $expectedArrival ?? $shipment->expected_arrival_at;
        $estimated = $estimatedArrival ?? $shipment->estimated_arrival_at ?? $expected;
        $threshold = $thresholdOverride ?? $this->getThresholdHours();

        // 1. Check if estimated arrival is past expected arrival
        if ($estimated->lessThanOrEqualTo($expected)) {
            $reasonKey = 'shipments.delay_reasons.on_schedule';
            $reasonParams = [
                'estimated' => $estimated->format('d M H:i'),
                'expected' => $expected->format('d M H:i'),
            ];
            $reasonPayload = json_encode(['key' => $reasonKey, 'params' => $reasonParams]);
            $reasonText = __($reasonKey, $reasonParams);

            return new EtaEvaluationResult(
                isDelayed: false,
                delayHours: 0,
                thresholdHours: $threshold,
                delayStatus: DelayStatus::OnTime,
                classification: 'ON_TIME',
                reason: $reasonText,
                reasonPayload: $reasonPayload
            );
        }

        // 2. Calculate delay in hours deterministically
        $delayHours = (int) $expected->diffInHours($estimated);

        // 3. Determine severity classification (0-6h LOW, >6-24h MEDIUM, >24h HIGH)
        $classification = match (true) {
            $delayHours <= 6 => 'LOW',
            $delayHours <= 24 => 'MEDIUM',
            default => 'HIGH',
        };

        // 4. Compare against configurable threshold
        if ($delayHours >= $threshold) {
            $delayStatus = match ($classification) {
                'LOW' => DelayStatus::Low,
                'MEDIUM' => DelayStatus::Medium,
                'HIGH' => DelayStatus::High,
                default => DelayStatus::Delayed,
            };

            $reasonKey = 'shipments.delay_reasons.exceeded_threshold';
            $reasonParams = [
                'estimated' => $estimated->format('d M H:i'),
                'expected' => $expected->format('d M H:i'),
                'hours' => $delayHours,
                'threshold' => $threshold,
                'classification' => $classification,
            ];
            $reasonPayload = json_encode(['key' => $reasonKey, 'params' => $reasonParams]);
            $reasonText = __($reasonKey, $reasonParams);

            return new EtaEvaluationResult(
                isDelayed: true,
                delayHours: $delayHours,
                thresholdHours: $threshold,
                delayStatus: $delayStatus,
                classification: $classification,
                reason: $reasonText,
                reasonPayload: $reasonPayload
            );
        }

        // Delay exists but is below threshold tolerance
        $reasonKey = 'shipments.delay_reasons.below_threshold';
        $reasonParams = [
            'estimated' => $estimated->format('d M H:i'),
            'expected' => $expected->format('d M H:i'),
            'hours' => $delayHours,
            'threshold' => $threshold,
        ];
        $reasonPayload = json_encode(['key' => $reasonKey, 'params' => $reasonParams]);
        $reasonText = __($reasonKey, $reasonParams);

        return new EtaEvaluationResult(
            isDelayed: false,
            delayHours: $delayHours,
            thresholdHours: $threshold,
            delayStatus: DelayStatus::OnTime,
            classification: 'ON_TIME',
            reason: $reasonText,
            reasonPayload: $reasonPayload
        );
    }

    /**
     * Evaluate and persist ETA delay evaluation to the shipment.
     * Generates in-app notifications and audit logs if the shipment becomes delayed.
     */
    public function evaluateAndSave(
        Shipment $shipment,
        ?int $thresholdOverride = null,
        ?User $actor = null
    ): EtaEvaluationResult {
        $result = $this->evaluate($shipment, null, null, $thresholdOverride);

        return DB::transaction(function () use ($shipment, $result, $actor) {
            $previousStatus = $shipment->delay_status;
            $previousHours = $shipment->delay_hours;

            $shipment->delay_status = $result->delayStatus;
            $shipment->delay_hours = $result->delayHours;
            $shipment->delay_reason = $result->reasonPayload ?? $result->reason;
            $shipment->save();

            // Trigger notification & audit log if shipment transitioned into an active delay
            $becameDelayed = $result->isDelayed && (! $previousStatus || ! $previousStatus->isDelayed());
            $escalatedDelay = $result->isDelayed && $previousStatus && $previousStatus->isDelayed() && $result->delayHours > ($previousHours ?? 0);

            if ($becameDelayed || $escalatedDelay) {
                // 1. Notify Consignee Customer
                Notification::create([
                    'user_id' => $shipment->customer_id,
                    'shipment_id' => $shipment->id,
                    'type' => 'SHIPMENT_DELAY_ALERT',
                    'message' => json_encode([
                        'key' => 'notifications.messages.shipment_delay_alert',
                        'params' => [
                            'number' => $shipment->shipment_number,
                            'classification' => $result->classification,
                            'delay_hours' => $result->delayHours,
                            'reason' => $result->reason,
                        ],
                    ]),
                    'read_at' => null,
                ]);

                // 2. Notify Logistics Operators
                $operators = User::where('role', 'operator')->get();
                foreach ($operators as $operator) {
                    Notification::create([
                        'user_id' => $operator->id,
                        'shipment_id' => $shipment->id,
                        'type' => 'SHIPMENT_DELAY_INTERNAL',
                        'message' => json_encode([
                            'key' => 'notifications.messages.shipment_delay_internal',
                            'params' => [
                                'number' => $shipment->shipment_number,
                                'classification' => $result->classification,
                                'delay_hours' => $result->delayHours,
                            ],
                        ]),
                        'read_at' => null,
                    ]);
                }

                // 3. Record Audit Log
                AuditLog::create([
                    'user_id' => $actor?->id ?? $shipment->created_by,
                    'action' => 'SHIPMENT_DELAY_DETECTED',
                    'entity_type' => Shipment::class,
                    'entity_id' => $shipment->id,
                    'old_values' => [
                        'delay_status' => $previousStatus?->value ?? 'ON_TIME',
                        'delay_hours' => $previousHours,
                    ],
                    'new_values' => [
                        'delay_status' => $result->delayStatus->value,
                        'delay_hours' => $result->delayHours,
                        'reason' => $result->reason,
                    ],
                    'ip_address' => request()?->ip() ?? '127.0.0.1',
                ]);
            }

            return $result;
        });
    }

    /**
     * Batch evaluate all active (non-terminal) shipments.
     */
    public function evaluateBatch(?int $thresholdOverride = null): int
    {
        $activeShipments = Shipment::whereNotIn('current_status', [
            ShipmentStatus::Delivered->value,
            ShipmentStatus::Cancelled->value,
        ])->get();

        $count = 0;
        foreach ($activeShipments as $shipment) {
            $this->evaluateAndSave($shipment, $thresholdOverride);
            $count++;
        }

        return $count;
    }
}
