<?php

namespace App\Models;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use Database\Factories\ShipmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    /** @use HasFactory<ShipmentFactory> */
    use HasFactory;

    protected $fillable = [
        'shipment_number',
        'customer_id',
        'carrier_id',
        'origin_location_id',
        'destination_location_id',
        'cargo_type',
        'weight_kg',
        'volume_cbm',
        'expected_departure_at',
        'expected_arrival_at',
        'estimated_arrival_at',
        'current_status',
        'delay_status',
        'delay_hours',
        'delay_reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expected_departure_at' => 'datetime',
            'expected_arrival_at' => 'datetime',
            'estimated_arrival_at' => 'datetime',
            'current_status' => ShipmentStatus::class,
            'delay_status' => DelayStatus::class,
            'weight_kg' => 'float',
            'volume_cbm' => 'float',
            'delay_hours' => 'integer',
        ];
    }

    /**
     * Get the translated delay reason.
     * Decodes JSON structure {"key": "...", "params": {...}} if present.
     */
    public function getDelayReasonAttribute($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['key'])) {
            $params = $decoded['params'] ?? [];
            return __($decoded['key'], $params);
        }

        return $value;
    }

    /**
     * Get raw underlying delay reason.
     */
    public function getRawDelayReasonAttribute(): ?string
    {
        return $this->attributes['delay_reason'] ?? null;
    }

    /**
     * Shipment to Customer relationship.
     * Cardinality: Many Shipments belong to 1 Customer User (N:1).
     *
     * @return BelongsTo<User, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Shipment to Carrier relationship.
     * Cardinality: Many Shipments belong to 1 Carrier (N:1).
     *
     * @return BelongsTo<Carrier, $this>
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id');
    }

    /**
     * Shipment to Origin Location relationship.
     * Cardinality: Many Shipments belong to 1 Origin Location (N:1).
     *
     * @return BelongsTo<Location, $this>
     */
    public function originLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'origin_location_id');
    }

    /**
     * Shipment to Destination Location relationship.
     * Cardinality: Many Shipments belong to 1 Destination Location (N:1).
     *
     * @return BelongsTo<Location, $this>
     */
    public function destinationLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_location_id');
    }

    /**
     * Shipment to Creator User relationship.
     * Cardinality: Many Shipments belong to 1 Creator User (N:1).
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Shipment to Shipment Items relationship.
     * Cardinality: 1 Shipment has Many Shipment Items (1:N).
     *
     * @return HasMany<ShipmentItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShipmentItem::class, 'shipment_id');
    }

    /**
     * Shipment to Tracking Events relationship.
     * Cardinality: 1 Shipment has Many Tracking Events (1:N).
     *
     * @return HasMany<TrackingEvent, $this>
     */
    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class, 'shipment_id')->orderBy('occurred_at', 'desc');
    }

    /**
     * Shipment to Exceptions relationship.
     * Cardinality: 1 Shipment has Many Exceptions (1:N).
     *
     * @return HasMany<ShipmentException, $this>
     */
    public function exceptions(): HasMany
    {
        return $this->hasMany(ShipmentException::class, 'shipment_id')->orderBy('detected_at', 'desc');
    }

    /**
     * Shipment to Notifications relationship.
     * Cardinality: 1 Shipment has Many Notifications (1:N).
     *
     * @return HasMany<Notification, $this>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'shipment_id');
    }
}
