<?php

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shipment_id',
        'type',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the translated message for display according to active locale.
     * If message is stored as JSON {"key": "...", "params": {...}},
     * it evaluates the translation with interpolated parameters.
     */
    public function getMessageAttribute($value): string
    {
        if (empty($value)) {
            return '';
        }

        $decoded = json_decode($value, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && isset($decoded['key'])) {
            $params = $decoded['params'] ?? [];
            foreach ($params as $k => $v) {
                if (is_array($v) && isset($v['key'])) {
                    $params[$k] = __($v['key'], $v['params'] ?? []);
                }
            }
            if (isset($params['type']) && is_string($params['type'])) {
                $typeEnum = \App\Enums\ExceptionType::tryFrom($params['type']);
                if ($typeEnum) {
                    $params['type'] = $typeEnum->label();
                }
            }
            if (isset($params['status']) && is_string($params['status'])) {
                $statusEnum = \App\Enums\ShipmentStatus::tryFrom($params['status']);
                if ($statusEnum) {
                    $params['status'] = $statusEnum->label();
                }
            }
            return __($decoded['key'], $params);
        }

        return (string) $value;
    }

    /**
     * Access the raw underlying message value.
     */
    public function getRawMessageAttribute(): string
    {
        return $this->attributes['message'] ?? '';
    }

    /**
     * Notification to User relationship.
     * Cardinality: Many Notifications belong to 1 User (N:1).
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Notification to Shipment relationship.
     * Cardinality: Many Notifications optionally reference 1 Shipment (N:1).
     *
     * @return BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }
}
