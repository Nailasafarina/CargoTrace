<?php

namespace App\Enums;

enum LocationType: string
{
    case Port = 'port';
    case Airport = 'airport';
    case Warehouse = 'warehouse';
    case City = 'city';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Port => __('locations.types.port'),
            self::Airport => __('locations.types.airport'),
            self::Warehouse => __('locations.types.warehouse'),
            self::City => __('locations.types.city'),
        };
    }
}
