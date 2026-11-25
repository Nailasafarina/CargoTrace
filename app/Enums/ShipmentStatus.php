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
            self::Created => __('shipments.status.created'),
            self::Booked => __('shipments.status.booked'),
            self::PickedUp => __('shipments.status.picked_up'),
            self::InTransit => __('shipments.status.in_transit'),
            self::Delivered => __('shipments.status.delivered'),
            self::Cancelled => __('shipments.status.cancelled'),
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
            self::Created => 'bg-slate-100 text-slate-700 border border-slate-300',
            self::Booked => 'bg-blue-50 text-blue-700 border border-blue-200',
            self::PickedUp => 'bg-sky-50 text-sky-700 border border-sky-200',
            self::InTransit => 'bg-amber-50 text-amber-800 border border-amber-200',
            self::Delivered => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
            self::Cancelled => 'bg-rose-50 text-rose-700 border border-rose-200',
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
