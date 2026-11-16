<?php

namespace Database\Factories;

use App\Enums\ExceptionSeverity;
use App\Enums\ExceptionStatus;
use App\Enums\ExceptionType;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipmentException>
 */
class ShipmentExceptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'type' => ExceptionType::CustomsHold,
            'severity' => ExceptionSeverity::High,
            'status' => ExceptionStatus::Open,
            'detected_at' => now()->subHours(8),
            'resolved_at' => null,
            'description' => 'Container flagged for phytosanitary regulatory inspection by port authorities.',
            'created_by' => User::factory()->operator(),
        ];
    }
}
