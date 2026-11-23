<?php

namespace App\Enums;

enum DelayLevel: string
{
    case OnTime = 'ON_TIME';
    case Low = 'LOW';
    case Medium = 'MEDIUM';
    case High = 'HIGH';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::OnTime => __('shipments.delay_status.on_time'),
            self::Low => __('shipments.delay_status.low'),
            self::Medium => __('shipments.delay_status.medium'),
            self::High => __('shipments.delay_status.high'),
        };
    }
}
