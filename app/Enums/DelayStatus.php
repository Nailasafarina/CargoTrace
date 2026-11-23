<?php

namespace App\Enums;

enum DelayStatus: string
{
    case OnTime = 'ON_TIME';
    case Low = 'LOW';
    case Medium = 'MEDIUM';
    case High = 'HIGH';

    // Backwards-compatibility aliases
    case Delayed = 'DELAYED';
    case CriticalDelay = 'CRITICAL_DELAY';

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
            self::Delayed => __('shipments.delay_status.delayed'),
            self::CriticalDelay => __('shipments.delay_status.critical_delay'),
        };
    }

    /**
     * Determine whether this status represents an active delay.
     */
    public function isDelayed(): bool
    {
        return $this !== self::OnTime;
    }

    /**
     * Tailwind CSS badge classes for consistent presentation.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::OnTime => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            self::Low => 'bg-yellow-50 text-yellow-800 border border-yellow-300',
            self::Medium => 'bg-orange-50 text-orange-800 border border-orange-200',
            self::High, self::CriticalDelay => 'bg-rose-50 text-rose-700 border border-rose-200 animate-pulse',
            self::Delayed => 'bg-orange-50 text-orange-800 border border-orange-200',
        };
    }

    /**
     * Alias for badgeClasses().
     */
    public function badgeClass(): string
    {
        return $this->badgeClasses();
    }
}
