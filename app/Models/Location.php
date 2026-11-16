<?php

namespace App\Models;

use App\Enums\LocationType;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'country',
        'city',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'type' => LocationType::class,
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Location to Origin Shipments relationship.
     * Cardinality: 1 Location has Many Origin Shipments (1:N).
     *
     * @return HasMany<Shipment, $this>
     */
    public function originShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_location_id');
    }

    /**
     * Location to Destination Shipments relationship.
     * Cardinality: 1 Location has Many Destination Shipments (1:N).
     *
     * @return HasMany<Shipment, $this>
     */
    public function destinationShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_location_id');
    }

    /**
     * Location to Tracking Events relationship.
     * Cardinality: 1 Location has Many Tracking Events (1:N).
     *
     * @return HasMany<TrackingEvent, $this>
     */
    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class, 'location_id');
    }
}
