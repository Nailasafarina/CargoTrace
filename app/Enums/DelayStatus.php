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
            self::OnTime => 'On Time',
            self::Low => 'Delay: LOW (≤6h)',
            self::Medium => 'Delay: MEDIUM (6–24h)',
            self::High => 'Delay: HIGH (>24h)',
            self::Delayed => 'Delayed',
            self::CriticalDelay => 'Critical Delay',
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
            self::OnTime => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
            self::Low => 'bg-sky-500/10 text-sky-400 border border-sky-500/30',
            self::Medium => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
            self::High, self::CriticalDelay => 'bg-rose-500/10 text-rose-400 border border-rose-500/30 animate-pulse',
            self::Delayed => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
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
