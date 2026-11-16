<?php

namespace Database\Seeders;

use App\Enums\LocationType;
use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Seed realistic waypoint hubs along Southeast Asia -> Europe shipping route.
     */
    public function run(): void
    {
        $locations = [
            // 1. Southeast Asia Origins
            [
                'name' => 'Port of Tanjung Priok (Jakarta)',
                'type' => LocationType::Port,
                'country' => 'Indonesia',
                'city' => 'Jakarta',
                'latitude' => -6.1038140,
                'longitude' => 106.8824200,
            ],
            [
                'name' => 'Port of Singapore',
                'type' => LocationType::Port,
                'country' => 'Singapore',
                'city' => 'Singapore',
                'latitude' => 1.2644120,
                'longitude' => 103.8222180,
            ],
            [
                'name' => 'Port Klang Terminal',
                'type' => LocationType::Port,
                'country' => 'Malaysia',
                'city' => 'Klang',
                'latitude' => 3.0012050,
                'longitude' => 101.3912150,
            ],
            [
                'name' => 'Port of Tanjung Pelepas',
                'type' => LocationType::Port,
                'country' => 'Malaysia',
                'city' => 'Johor',
                'latitude' => 1.3653100,
                'longitude' => 103.5519200,
            ],
            [
                'name' => 'Soekarno-Hatta Cargo Terminal (CGK)',
                'type' => LocationType::Airport,
                'country' => 'Indonesia',
                'city' => 'Tangerang',
                'latitude' => -6.1275000,
                'longitude' => 106.6536000,
            ],

            // 2. Transshipment & Middle East Corridor
            [
                'name' => 'Port of Colombo',
                'type' => LocationType::Port,
                'country' => 'Sri Lanka',
                'city' => 'Colombo',
                'latitude' => 6.9497140,
                'longitude' => 79.8447150,
            ],
            [
                'name' => 'Port of Salalah',
                'type' => LocationType::Port,
                'country' => 'Oman',
                'city' => 'Salalah',
                'latitude' => 16.9400200,
                'longitude' => 54.0044100,
            ],
            [
                'name' => 'Port Said (Suez Canal Hub)',
                'type' => LocationType::Port,
                'country' => 'Egypt',
                'city' => 'Port Said',
                'latitude' => 31.2653200,
                'longitude' => 32.3019300,
            ],

            // 3. Mediterranean & European Destinations
            [
                'name' => 'Port of Piraeus',
                'type' => LocationType::Port,
                'country' => 'Greece',
                'city' => 'Piraeus',
                'latitude' => 37.9475100,
                'longitude' => 23.6372100,
            ],
            [
                'name' => 'Port of Valencia',
                'type' => LocationType::Port,
                'country' => 'Spain',
                'city' => 'Valencia',
                'latitude' => 39.4444200,
                'longitude' => -0.3200150,
            ],
            [
                'name' => 'Port of Rotterdam (Maasvlakte)',
                'type' => LocationType::Port,
                'country' => 'Netherlands',
                'city' => 'Rotterdam',
                'latitude' => 51.9500300,
                'longitude' => 4.1333400,
            ],
            [
                'name' => 'Port of Antwerp-Bruges',
                'type' => LocationType::Port,
                'country' => 'Belgium',
                'city' => 'Antwerp',
                'latitude' => 51.2789100,
                'longitude' => 4.3800200,
            ],
            [
                'name' => 'Port of Hamburg',
                'type' => LocationType::Port,
                'country' => 'Germany',
                'city' => 'Hamburg',
                'latitude' => 53.5320150,
                'longitude' => 9.9670100,
            ],
            [
                'name' => 'Port of Felixstowe',
                'type' => LocationType::Port,
                'country' => 'United Kingdom',
                'city' => 'Felixstowe',
                'latitude' => 51.9547100,
                'longitude' => 1.3094200,
            ],
            [
                'name' => 'Schiphol Cargo Distribution Hub (AMS)',
                'type' => LocationType::Warehouse,
                'country' => 'Netherlands',
                'city' => 'Amsterdam',
                'latitude' => 52.3105000,
                'longitude' => 4.7683000,
            ],
        ];

        foreach ($locations as $loc) {
            Location::firstOrCreate(
                ['name' => $loc['name']],
                $loc
            );
        }
    }
}
