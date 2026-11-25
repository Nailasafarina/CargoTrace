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
            self::Created => 'bg-[#FAF3F6] text-[#7A5663] border border-[#EFE2E7]',
            self::Booked => 'bg-[#FCECF1] text-[#BE2B5B] border border-[#F4D3DF]',
            self::PickedUp => 'bg-[#F8DFEA] text-[#9E224B] border border-[#F2BFD2]',
            self::InTransit => 'bg-[#BE2B5B] text-white border border-[#BE2B5B]',
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
