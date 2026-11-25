<?php

namespace App\Enums;

enum ExceptionStatus: string
{
    case Open = 'OPEN';
    case Investigating = 'INVESTIGATING';
    case Resolved = 'RESOLVED';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Open => __('exceptions.status.open'),
            self::Investigating => __('exceptions.status.investigating'),
            self::Resolved => __('exceptions.status.resolved'),
        };
    }

    /**
     * Get valid target transitions from current status.
     *
     * @return array<ExceptionStatus>
     */
    public function validTransitions(): array
    {
        return match ($this) {
            self::Open => [self::Investigating, self::Resolved],
            self::Investigating => [self::Resolved],
            self::Resolved => [], // Terminal state: cannot be reopened or transitioned
        };
    }

    /**
     * Determine whether the exception status can transition to target.
     */
    public function canTransitionTo(self|string $target): bool
    {
        $targetEnum = is_string($target) ? self::tryFrom($target) : $target;

        if (! $targetEnum) {
            return false;
        }

        if ($this === self::Resolved) {
            return false;
        }

        return in_array($targetEnum, $this->validTransitions(), true);
    }

    /**
     * Badge CSS classes for Tailwind UI.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Open => 'bg-red-50 text-red-700 border border-red-200',
            self::Investigating => 'bg-amber-50 text-amber-800 border border-amber-200',
            self::Resolved => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
        };
    }
}
