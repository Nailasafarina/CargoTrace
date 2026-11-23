<?php

namespace App\Enums;

enum ExceptionSeverity: string
{
    case Low = 'LOW';
    case Medium = 'MEDIUM';
    case High = 'HIGH';
    case Critical = 'CRITICAL';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Low => __('exceptions.severity.low'),
            self::Medium => __('exceptions.severity.medium'),
            self::High => __('exceptions.severity.high'),
            self::Critical => __('exceptions.severity.critical'),
        };
    }

    /**
     * Tailwind CSS badge classes for light theme.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Low => 'bg-yellow-50 text-yellow-800 border border-yellow-300',
            self::Medium => 'bg-orange-50 text-orange-800 border border-orange-200',
            self::High => 'bg-rose-50 text-rose-700 border border-rose-200',
            self::Critical => 'bg-red-100 text-red-800 border border-red-300 font-bold',
        };
    }
}
