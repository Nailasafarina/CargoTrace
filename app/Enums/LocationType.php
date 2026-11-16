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
            self::Port => 'Port',
            self::Airport => 'Airport',
            self::Warehouse => 'Warehouse',
            self::City => 'City Hub',
        };
    }
}
