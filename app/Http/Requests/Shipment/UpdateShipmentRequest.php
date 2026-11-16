<?php

namespace App\Http\Requests\Shipment;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateShipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $shipment = $this->route('shipment');

        return $this->user()?->can('update', $shipment) ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $shipment = $this->route('shipment');

        return [
            'shipment_number' => ['required', 'string', 'max:50', Rule::unique('shipments', 'shipment_number')->ignore($shipment)],
            'customer_id' => ['required', 'exists:users,id'],
            'carrier_id' => ['required', 'exists:carriers,id'],
            'origin_location_id' => ['required', 'exists:locations,id'],
            'destination_location_id' => ['required', 'exists:locations,id', 'different:origin_location_id'],
            'cargo_type' => ['required', 'string', 'max:255'],
            'weight_kg' => ['required', 'numeric', 'min:0.01'],
            'volume_cbm' => ['required', 'numeric', 'min:0.01'],
            'expected_departure_at' => ['required', 'date'],
            'expected_arrival_at' => ['required', 'date', 'after:expected_departure_at'],
            'estimated_arrival_at' => ['nullable', 'date'],
            'current_status' => ['required', new Enum(ShipmentStatus::class)],
            'delay_status' => ['required', new Enum(DelayStatus::class)],
            'delay_hours' => ['nullable', 'integer', 'min:0'],
            'items' => ['nullable', 'array'],
            'items.*.description' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.weight_kg' => ['required_with:items', 'numeric', 'min:0.01'],
        ];
    }

    /**
     * Custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'destination_location_id.different' => 'The destination port/terminal cannot be the same as the origin location.',
            'expected_arrival_at.after' => 'The expected arrival date must be scheduled after the expected departure date.',
        ];
    }
}
