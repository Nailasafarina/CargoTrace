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
            self::CustomsHold => 'Customs Regulatory Hold',
            self::WeatherDelay => 'Adverse Weather Delay',
            self::PortCongestion => 'Port Berth Congestion',
            self::Damaged => 'Cargo Packaging Damage',
            self::Misrouted => 'Carrier Transshipment Misroute',
            self::DocumentationIssue => 'Missing Documentation Issue',
        };
    }
}
