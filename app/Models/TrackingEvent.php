<?php

namespace App\Models;

use App\Enums\TrackingEventType;
use Database\Factories\TrackingEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrackingEvent extends Model
{
    /** @use HasFactory<TrackingEventFactory> */
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'location_id',
        'event_type',
        'is_exception',
        'occurred_at',
        'latitude',
        'longitude',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_type' => TrackingEventType::class,
            'is_exception' => 'boolean',
            'occurred_at' => 'datetime',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Tracking Event to Shipment relationship.
     * Cardinality: Many Tracking Events belong to 1 Shipment (N:1).
     *
     * @return BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    /**
     * Tracking Event to Location relationship.
     * Cardinality: Many Tracking Events belong to 1 Location (N:1).
     *
     * @return BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Tracking Event to Creator User relationship.
     * Cardinality: Many Tracking Events created by 1 User (N:1).
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
