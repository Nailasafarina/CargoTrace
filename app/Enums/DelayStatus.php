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
            self::Low => 'bg-amber-50 text-amber-800 border border-amber-200',
            self::Medium => 'bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]',
            self::High, self::CriticalDelay => 'bg-red-50 text-red-700 border border-red-200',
            self::Delayed => 'bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]',
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
