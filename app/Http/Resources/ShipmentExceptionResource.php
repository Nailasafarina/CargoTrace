<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentExceptionResource extends JsonResource
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
            'type' => $this->type instanceof \BackedEnum ? $this->type->value : $this->type,
            'type_label' => method_exists($this->type, 'label') ? $this->type->label() : null,
            'severity' => $this->severity instanceof \BackedEnum ? $this->severity->value : $this->severity,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'status_label' => method_exists($this->status, 'label') ? $this->status->label() : null,
            'detected_at' => $this->detected_at?->toIso8601String(),
            'resolved_at' => $this->resolved_at?->toIso8601String(),
            'description' => $this->description,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
