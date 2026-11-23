<?php

namespace App\Services;

use App\Enums\ShipmentStatus;
use App\Enums\TrackingEventType;
use App\Exceptions\InvalidEventTimestampException;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrackingService
{
    /**
     * Add a tracking milestone event, validate lifecycle state transition and timeline,
     * update the shipment status in a DB transaction, and log audit trail & notifications.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws InvalidStatusTransitionException
     * @throws InvalidEventTimestampException
     */
    public function addEvent(Shipment $shipment, array $data, User $actor): TrackingEvent
    {
        // 1. Determine target status
        $targetStatusInput = $data['status'] ?? $data['current_status'] ?? $shipment->current_status;
        $targetStatus = is_string($targetStatusInput)
            ? ShipmentStatus::tryFrom($targetStatusInput) ?? $shipment->current_status
            : $targetStatusInput;

        // 2. Validate status transition
        if ($shipment->current_status === ShipmentStatus::Delivered) {
            throw new InvalidStatusTransitionException(
                "Shipment {$shipment->shipment_number} is already in the terminal state 'DELIVERED'. No further transitions or events are permitted."
            );
        }

        if ($targetStatus !== $shipment->current_status && ! $shipment->current_status->canTransitionTo($targetStatus)) {
            throw new InvalidStatusTransitionException(
                "Illegal status transition: Cannot transition shipment {$shipment->shipment_number} from '{$shipment->current_status->value}' to '{$targetStatus->value}'."
            );
        }

        // 3. Validate occurred_at timestamp
        $occurredAt = isset($data['occurred_at'])
            ? Carbon::parse($data['occurred_at'])
            : Carbon::now();

        // Reject if in the future (allowing max 5 minutes server clock drift)
        if ($occurredAt->isAfter(Carbon::now()->addMinutes(5))) {
            throw new InvalidEventTimestampException(
                'Tracking event occurrence time cannot be set in the future.'
            );
        }

        // Reject if older than the last recorded milestone
        $lastEvent = $shipment->trackingEvents()->latest('occurred_at')->first();
        if ($lastEvent && $occurredAt->lt($lastEvent->occurred_at)) {
            throw new InvalidEventTimestampException(
                "Event time ({$occurredAt->toDateTimeString()}) cannot precede the latest recorded milestone ({$lastEvent->occurred_at->toDateTimeString()})."
            );
        }

        // 4. Execute atomic database transaction
        return DB::transaction(function () use ($shipment, $data, $actor, $targetStatus, $occurredAt) {
            $oldStatus = $shipment->current_status;

            // Update shipment current status
            $shipment->current_status = $targetStatus;
            if ($targetStatus === ShipmentStatus::Delivered) {
                $shipment->estimated_arrival_at = $occurredAt;
            }
            $shipment->save();

            // Resolve Event Type Enum
            $eventType = TrackingEventType::InTransit;
            if (! empty($data['event_type'])) {
                $eventType = is_string($data['event_type'])
                    ? TrackingEventType::tryFrom($data['event_type']) ?? TrackingEventType::InTransit
                    : $data['event_type'];
            } elseif ($targetStatus === ShipmentStatus::Delivered) {
                $eventType = TrackingEventType::Delivered;
            } elseif ($targetStatus === ShipmentStatus::PickedUp) {
                $eventType = TrackingEventType::PickedUp;
            }

            // Append new tracking event (strictly append-only history)
            $event = TrackingEvent::create([
                'shipment_id' => $shipment->id,
                'location_id' => $data['location_id'] ?? null,
                'event_type' => $eventType,
                'is_exception' => (bool) ($data['is_exception'] ?? false),
                'occurred_at' => $occurredAt,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'description' => $data['description'] ?? "Milestone reached: {$targetStatus->label()}",
                'created_by' => $actor->id,
            ]);

            // Append Audit Log
            AuditLog::create([
                'user_id' => $actor->id,
                'action' => 'TRACKING_EVENT_ADDED',
                'entity_type' => Shipment::class,
                'entity_id' => $shipment->id,
                'old_values' => ['current_status' => $oldStatus->value],
                'new_values' => [
                    'current_status' => $targetStatus->value,
                    'event_id' => $event->id,
                    'event_type' => $event->event_type->value,
                    'occurred_at' => $occurredAt->toIso8601String(),
                ],
                'ip_address' => request()?->ip() ?? '127.0.0.1',
            ]);

            // Create in-app notification for consignee customer
            Notification::create([
                'user_id' => $shipment->customer_id,
                'shipment_id' => $shipment->id,
                'type' => 'SHIPMENT_MILESTONE_UPDATED',
                'message' => json_encode([
                    'key' => 'notifications.messages.shipment_milestone_updated',
                    'params' => [
                        'number' => $shipment->shipment_number,
                        'status' => $targetStatus->value,
                        'description' => $event->description,
                    ],
                ]),
                'read_at' => null,
            ]);

            // Re-evaluate ETA and delay status automatically
            app(EtaService::class)->evaluateAndSave($shipment, null, $actor);

            return $event;
        });
    }
}
