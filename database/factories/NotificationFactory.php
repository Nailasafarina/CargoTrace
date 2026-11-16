<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->customer(),
            'shipment_id' => Shipment::factory(),
            'type' => 'SHIPMENT_STATUS_UPDATE',
            'message' => 'Shipment milestone updated to departed port.',
            'read_at' => null,
        ];
    }
}
