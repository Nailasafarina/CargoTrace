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
            self::Medium => 'bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]',
            self::High => 'bg-red-50 text-red-700 border border-red-200',
            self::Critical => 'bg-[#1F171B] text-[#F8C9DC] border border-[#9E224B] font-bold',
        };
    }
}
