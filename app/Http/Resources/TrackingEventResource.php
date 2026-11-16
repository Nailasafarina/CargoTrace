<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrackingEventResource extends JsonResource
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
            'shipment_id' => $this->shipment_id,
            'event_type' => $this->event_type instanceof \BackedEnum ? $this->event_type->value : $this->event_type,
            'event_type_label' => method_exists($this->event_type, 'label') ? $this->event_type->label() : null,
            'is_exception' => (bool) $this->is_exception,
            'occurred_at' => $this->occurred_at?->toIso8601String(),
            'latitude' => $this->latitude ? (float) $this->latitude : null,
            'longitude' => $this->longitude ? (float) $this->longitude : null,
            'description' => $this->description,
            'location' => new LocationResource($this->whenLoaded('location')),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
