<?php

namespace App\Http\Requests\TrackingEvent;

use App\Enums\ShipmentStatus;
use App\Enums\TrackingEventType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTrackingEventRequest extends FormRequest
{
    /**
     * Determine if user is authorized to create tracking events.
     * Allowed: Admin and Logistics Operator only. Customer is forbidden.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user && ($user->isAdmin() || $user->isOperator());
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(ShipmentStatus::class)],
            'event_type' => ['nullable', new Enum(TrackingEventType::class)],
            'location_id' => ['nullable', 'exists:locations,id'],
            'occurred_at' => ['required', 'date'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'description' => ['required', 'string', 'max:1000'],
            'is_exception' => ['nullable', 'boolean'],
        ];
    }
}
