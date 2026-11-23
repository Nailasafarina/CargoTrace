<?php

namespace App\Enums;

enum TrackingEventType: string
{
    case OrderCreated = 'ORDER_CREATED';
    case PickedUp = 'PICKED_UP';
    case Departed = 'DEPARTED';
    case InTransit = 'IN_TRANSIT';
    case TransshipmentArrival = 'TRANSSHIPMENT_ARRIVAL';
    case CustomsHold = 'CUSTOMS_HOLD';
    case CustomsCleared = 'CUSTOMS_CLEARED';
    case ArrivedDestination = 'ARRIVED_DESTINATION';
    case OutForDelivery = 'OUT_FOR_DELIVERY';
    case Delivered = 'DELIVERED';
    case ExceptionAlert = 'EXCEPTION_ALERT';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::OrderCreated => __('tracking.event_types.order_created'),
            self::PickedUp => __('tracking.event_types.picked_up'),
            self::Departed => __('tracking.event_types.departed'),
            self::InTransit => __('tracking.event_types.in_transit'),
            self::TransshipmentArrival => __('tracking.event_types.transshipment_arrival'),
            self::CustomsHold => __('tracking.event_types.customs_hold'),
            self::CustomsCleared => __('tracking.event_types.customs_cleared'),
            self::ArrivedDestination => __('tracking.event_types.arrived_destination'),
            self::OutForDelivery => __('tracking.event_types.out_for_delivery'),
            self::Delivered => __('tracking.event_types.delivered'),
            self::ExceptionAlert => __('tracking.event_types.exception_alert'),
        };
    }
}
