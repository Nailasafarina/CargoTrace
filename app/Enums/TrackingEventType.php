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
            self::OrderCreated => 'Booking Order Created',
            self::PickedUp => 'Cargo Picked Up',
            self::Departed => 'Vessel Departed Port',
            self::InTransit => 'In Transit / Ocean Passage',
            self::TransshipmentArrival => 'Arrived at Transshipment Hub',
            self::CustomsHold => 'Customs Hold Placed',
            self::CustomsCleared => 'Customs Cleared',
            self::ArrivedDestination => 'Arrived at Destination Port',
            self::OutForDelivery => 'Out for Final Delivery',
            self::Delivered => 'Delivered to Consignee',
            self::ExceptionAlert => 'Shipment Exception Alert',
        };
    }
}
