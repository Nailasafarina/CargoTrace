<?php

namespace Database\Factories;

use App\Enums\TrackingEventType;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\TrackingEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrackingEvent>
 */
class TrackingEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'location_id' => Location::factory(),
            'event_type' => TrackingEventType::InTransit,
            'is_exception' => false,
            'occurred_at' => now(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'description' => 'Vessel en route along planned navigation corridor.',
            'created_by' => User::factory()->operator(),
        ];
    }
}
