<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shipment_number' => $this->shipment_number,
            'cargo_type' => $this->cargo_type,
            'weight_kg' => (float) $this->weight_kg,
            'volume_cbm' => (float) $this->volume_cbm,
            'expected_departure_at' => $this->expected_departure_at?->toIso8601String(),
            'expected_arrival_at' => $this->expected_arrival_at?->toIso8601String(),
            'estimated_arrival_at' => $this->estimated_arrival_at?->toIso8601String(),
            'current_status' => $this->current_status instanceof \BackedEnum ? $this->current_status->value : $this->current_status,
            'current_status_label' => method_exists($this->current_status, 'label') ? $this->current_status->label() : null,
            'delay_status' => $this->delay_status instanceof \BackedEnum ? $this->delay_status->value : $this->delay_status,
            'delay_status_label' => method_exists($this->delay_status, 'label') ? $this->delay_status->label() : null,
            'delay_hours' => $this->delay_hours ? (int) $this->delay_hours : 0,
            'delay_reason' => $this->delay_reason,
            'customer' => new UserResource($this->whenLoaded('customer')),
            'carrier' => new CarrierResource($this->whenLoaded('carrier')),
            'origin' => new LocationResource($this->whenLoaded('originLocation')),
            'destination' => new LocationResource($this->whenLoaded('destinationLocation')),
            'items' => ShipmentItemResource::collection($this->whenLoaded('items')),
            'tracking_events' => TrackingEventResource::collection($this->whenLoaded('trackingEvents')),
            'exceptions' => ShipmentExceptionResource::collection($this->whenLoaded('exceptions')),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
