<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\ShipmentItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipmentItem>
 */
class ShipmentItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'description' => fake()->randomElement([
                'Industrial Microcontrollers & Sensors',
                'High-Precision CNC Machine Tooling',
                'Textile Yarns & Finished Apparel',
                'Frozen Seafood (Shrimp & Tuna)',
                'Automotive Lithium Batteries',
                'Renewable Solar PV Inverter Modules',
            ]),
            'quantity' => fake()->numberBetween(10, 500),
            'weight_kg' => fake()->randomFloat(2, 50, 4500),
        ];
    }
}
