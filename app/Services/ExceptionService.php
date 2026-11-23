<?php

namespace App\Services;

use App\Enums\ExceptionSeverity;
use App\Enums\ExceptionStatus;
use App\Enums\ExceptionType;
use App\Exceptions\InvalidExceptionTransitionException;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExceptionService
{
    /**
     * Create a new shipment exception, automatically set detected_at,
     * persist audit log, and notify both customer and logistics operators.
     *
     * @param  array<string, mixed>  $data
     */
    public function createException(Shipment $shipment, array $data, User $actor): ShipmentException
    {
        $type = is_string($data['type'])
            ? ExceptionType::from($data['type'])
            : $data['type'];

        $severity = ! empty($data['severity'])
            ? (is_string($data['severity']) ? ExceptionSeverity::from($data['severity']) : $data['severity'])
            : ExceptionSeverity::Medium;

        $detectedAt = ! empty($data['detected_at'])
            ? Carbon::parse($data['detected_at'])
            : Carbon::now();

        return DB::transaction(function () use ($shipment, $data, $actor, $type, $severity, $detectedAt) {
            $exception = ShipmentException::create([
                'shipment_id' => $shipment->id,
                'type' => $type,
                'severity' => $severity,
                'status' => ExceptionStatus::Open,
                'detected_at' => $detectedAt,
                'resolved_at' => null,
                'description' => $data['description'],
                'created_by' => $actor->id,
            ]);

            // 1. Notify Consignee Customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'type' => 'SHIPMENT_EXCEPTION_ALERT',
                'message' => json_encode([
                    'key' => 'notifications.messages.shipment_exception_alert',
                    'params' => [
                        'number' => $shipment->shipment_number,
                        'type' => $type->value,
                        'description' => $exception->description,
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
                    'type' => 'SHIPMENT_EXCEPTION_INTERNAL',
                    'message' => json_encode([
                        'key' => 'notifications.messages.shipment_exception_internal',
                        'params' => [
                            'number' => $shipment->shipment_number,
                            'type' => $type->value,
                            'severity' => $severity->value,
                        ],
                    ]),
                    'read_at' => null,
                ]);
            }

            // 3. Create Audit Log
            AuditLog::create([
                'user_id' => $actor->id,
                'action' => 'EXCEPTION_CREATED',
                'entity_type' => ShipmentException::class,
                'entity_id' => $exception->id,
                'old_values' => null,
                'new_values' => [
                    'shipment_id' => $shipment->id,
                    'type' => $type->value,
                    'severity' => $severity->value,
                    'status' => ExceptionStatus::Open->value,
                    'detected_at' => $detectedAt->toIso8601String(),
                    'description' => $exception->description,
                ],
                'ip_address' => request()?->ip() ?? '127.0.0.1',
            ]);

            return $exception;
        });
    }

    /**
     * Transition exception status (OPEN -> INVESTIGATING -> RESOLVED).
     * Validates transition rules and automatically populates resolved_at when RESOLVED.
     *
     * @throws InvalidExceptionTransitionException
     */
    public function updateStatus(
        ShipmentException $exception,
        ExceptionStatus|string $targetStatus,
        ?string $resolutionNotes,
        User $actor
    ): ShipmentException {
        $targetEnum = is_string($targetStatus)
            ? ExceptionStatus::tryFrom($targetStatus)
            : $targetStatus;

        if (! $targetEnum) {
            throw new InvalidExceptionTransitionException("Invalid exception status: '{$targetStatus}'.");
        }

        // Validate state machine transition
        if (! $exception->status->canTransitionTo($targetEnum)) {
            throw new InvalidExceptionTransitionException(
                "Illegal transition: Cannot change exception #{$exception->id} from '{$exception->status->value}' to '{$targetEnum->value}'."
            );
        }

        return DB::transaction(function () use ($exception, $targetEnum, $resolutionNotes, $actor) {
            $oldStatus = $exception->status;
            $exception->status = $targetEnum;

            // Automatically set resolved_at when entering RESOLVED state
            if ($targetEnum === ExceptionStatus::Resolved) {
                $exception->resolved_at = Carbon::now();
            }

            if (! empty($resolutionNotes)) {
                $timestamp = Carbon::now()->toDateTimeString();
                $exception->description .= "\n\n[{$timestamp} - {$targetEnum->label()} by {$actor->name}]: {$resolutionNotes}";
            }

            $exception->save();

            // Notify Customer if resolved
            if ($targetEnum === ExceptionStatus::Resolved) {
                Notification::create([
                    'user_id' => $exception->shipment->customer_id,
                    'shipment_id' => $exception->shipment_id,
                    'type' => 'SHIPMENT_EXCEPTION_RESOLVED',
                    'message' => json_encode([
                        'key' => 'notifications.messages.shipment_exception_resolved',
                        'params' => [
                            'number' => $exception->shipment->shipment_number,
                            'type' => $exception->type->value,
                        ],
                    ]),
                    'read_at' => null,
                ]);
            }

            // Create Audit Log
            AuditLog::create([
                'user_id' => $actor->id,
                'action' => 'EXCEPTION_STATUS_UPDATED',
                'entity_type' => ShipmentException::class,
                'entity_id' => $exception->id,
                'old_values' => ['status' => $oldStatus->value],
                'new_values' => [
                    'status' => $targetEnum->value,
                    'resolved_at' => $exception->resolved_at?->toIso8601String(),
                    'resolution_notes' => $resolutionNotes,
                ],
                'ip_address' => request()?->ip() ?? '127.0.0.1',
            ]);

            return $exception;
        });
    }
}
