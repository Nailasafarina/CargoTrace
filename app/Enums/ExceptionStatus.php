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
            self::Open => 'Open',
            self::Investigating => 'Under Investigation',
            self::Resolved => 'Resolved & Cleared',
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
            self::Open => 'bg-rose-500/10 text-rose-400 border border-rose-500/30',
            self::Investigating => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
            self::Resolved => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
        };
    }
}
