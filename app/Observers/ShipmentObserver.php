<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Shipment;
use Illuminate\Support\Facades\Auth;

class ShipmentObserver
{
    /**
     * Handle the Shipment "created" event.
     */
    public function created(Shipment $shipment): void
    {
        AuditLog::create([
            'user_id' => Auth::id() ?? $shipment->created_by,
            'action' => 'SHIPMENT_CREATED',
            'entity_type' => Shipment::class,
            'entity_id' => $shipment->id,
            'old_values' => null,
            'new_values' => $shipment->getAttributes(),
            'ip_address' => request()?->ip() ?? '127.0.0.1',
        ]);
    }

    /**
     * Handle the Shipment "updated" event.
     */
    public function updated(Shipment $shipment): void
    {
        // Capture only the attributes that changed
        $changes = $shipment->getChanges();
        $original = array_intersect_key($shipment->getOriginal(), $changes);

        // Filter out updated_at if it's the only change
        if (count($changes) === 1 && isset($changes['updated_at'])) {
            return;
        }

        AuditLog::create([
            'user_id' => Auth::id() ?? $shipment->created_by,
            'action' => 'SHIPMENT_UPDATED',
            'entity_type' => Shipment::class,
            'entity_id' => $shipment->id,
            'old_values' => $original,
            'new_values' => $changes,
            'ip_address' => request()?->ip() ?? '127.0.0.1',
        ]);
    }

    /**
     * Handle the Shipment "deleted" event.
     */
    public function deleted(Shipment $shipment): void
    {
        AuditLog::create([
            'user_id' => Auth::id() ?? $shipment->created_by,
            'action' => 'SHIPMENT_DELETED',
            'entity_type' => Shipment::class,
            'entity_id' => $shipment->id,
            'old_values' => $shipment->toArray(),
            'new_values' => null,
            'ip_address' => request()?->ip() ?? '127.0.0.1',
        ]);
    }
}
