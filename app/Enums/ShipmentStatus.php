<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case Created = 'CREATED';
    case Booked = 'BOOKED';
    case PickedUp = 'PICKED_UP';
    case InTransit = 'IN_TRANSIT';
    case Delivered = 'DELIVERED';
    case Cancelled = 'CANCELLED';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::Booked => 'Booked',
            self::PickedUp => 'Picked Up',
            self::InTransit => 'In Transit',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    /**
     * Get valid target transitions from current status.
     *
     * @return array<ShipmentStatus>
     */
    public function validTransitions(): array
    {
        return match ($this) {
            self::Created => [self::Booked, self::Cancelled],
            self::Booked => [self::PickedUp, self::Cancelled],
            self::PickedUp => [self::InTransit, self::Cancelled],
            self::InTransit => [self::InTransit, self::Delivered, self::Cancelled],
            self::Delivered => [], // Terminal: absolutely cannot transition to any status
            self::Cancelled => [], // Terminal
        };
    }

    /**
     * Determine whether the shipment can transition to the given target status.
     */
    public function canTransitionTo(self|string $target): bool
    {
        $targetEnum = is_string($target) ? self::tryFrom($target) : $target;

        if (! $targetEnum) {
            return false;
        }

        // DELIVERED is strictly a terminal state
        if ($this === self::Delivered) {
            return false;
        }

        return in_array($targetEnum, $this->validTransitions(), true);
    }

    /**
     * Tailwind CSS badge classes for consistent presentation across views.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Created => 'bg-slate-800 text-slate-300 border border-slate-700',
            self::Booked => 'bg-blue-500/10 text-blue-400 border border-blue-500/30',
            self::PickedUp => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
            self::InTransit => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/30',
            self::Delivered => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
            self::Cancelled => 'bg-rose-500/10 text-rose-400 border border-rose-500/30',
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
