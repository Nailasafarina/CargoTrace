<?php

namespace Database\Factories;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    public function definition(): array
    {
        $departure = fake()->dateTimeBetween('-10 days', '+2 days');
        $arrival = (clone $departure)->modify('+14 days');

        return [
            'shipment_number' => 'CT-'.strtoupper(fake()->bothify('???-####')),
            'customer_id' => User::factory()->customer(),
            'carrier_id' => Carrier::factory(),
            'origin_location_id' => Location::factory(),
            'destination_location_id' => Location::factory(),
            'cargo_type' => fake()->randomElement(['Dry Container (FCL)', 'Refrigerated Cargo (Reefer)', 'Electronics & Machinery', 'Textiles & Garments', 'Automotive Parts']),
            'weight_kg' => fake()->randomFloat(2, 1000, 28000),
            'volume_cbm' => fake()->randomFloat(2, 15, 68),
            'expected_departure_at' => $departure,
            'expected_arrival_at' => $arrival,
            'estimated_arrival_at' => $arrival,
            'current_status' => ShipmentStatus::InTransit,
            'delay_status' => DelayStatus::OnTime,
            'delay_hours' => 0,
            'created_by' => User::factory()->operator(),
        ];
    }
}
