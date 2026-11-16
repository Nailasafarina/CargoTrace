<?php

namespace App\Http\Requests\ShipmentException;

use App\Enums\ExceptionSeverity;
use App\Enums\ExceptionType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreShipmentExceptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Allowed: Admin and Logistics Operator only.
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
            'type' => ['required', new Enum(ExceptionType::class)],
            'severity' => ['nullable', new Enum(ExceptionSeverity::class)],
            'description' => ['required', 'string', 'max:2000'],
            'detected_at' => ['nullable', 'date'],
        ];
    }
}
