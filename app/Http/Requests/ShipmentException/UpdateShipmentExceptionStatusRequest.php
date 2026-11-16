<?php

namespace App\Http\Requests\ShipmentException;

use App\Enums\ExceptionStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateShipmentExceptionStatusRequest extends FormRequest
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
            'status' => ['required', new Enum(ExceptionStatus::class)],
            'resolution_notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
