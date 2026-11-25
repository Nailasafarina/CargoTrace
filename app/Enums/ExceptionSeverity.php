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
            self::Low => 'bg-amber-50 text-amber-800 border border-amber-200',
            self::Medium => 'bg-[#FCECF1] text-[#BE2B5B] border border-[#F4D3DF]',
            self::High => 'bg-rose-50 text-rose-700 border border-rose-200',
            self::Critical => 'bg-[#BE2B5B] text-white border border-[#A0224B] font-bold',
        };
    }
}
