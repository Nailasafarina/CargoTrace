<?php

namespace App\Enums;

enum ExceptionType: string
{
    case CustomsHold = 'CUSTOMS_HOLD';
    case WeatherDelay = 'WEATHER_DELAY';
    case PortCongestion = 'PORT_CONGESTION';
    case Damaged = 'DAMAGED';
    case Misrouted = 'MISROUTED';
    case DocumentationIssue = 'DOCUMENTATION_ISSUE';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::CustomsHold => __('exceptions.types.customs_hold'),
            self::WeatherDelay => __('exceptions.types.weather_delay'),
            self::PortCongestion => __('exceptions.types.port_congestion'),
            self::Damaged => __('exceptions.types.damaged'),
            self::Misrouted => __('exceptions.types.misrouted'),
            self::DocumentationIssue => __('exceptions.types.documentation_issue'),
        };
    }
}
