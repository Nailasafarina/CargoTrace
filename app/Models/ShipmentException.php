<?php

namespace App\Models;

use App\Enums\ExceptionSeverity;
use App\Enums\ExceptionStatus;
use App\Enums\ExceptionType;
use Database\Factories\ShipmentExceptionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentException extends Model
{
    /** @use HasFactory<ShipmentExceptionFactory> */
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'type',
        'severity',
        'status',
        'detected_at',
        'resolved_at',
        'description',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => ExceptionType::class,
            'severity' => ExceptionSeverity::class,
            'status' => ExceptionStatus::class,
            'detected_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * Exception to Shipment relationship.
     * Cardinality: Many Exceptions belong to 1 Shipment (N:1).
     *
     * @return BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    /**
     * Exception to Creator User relationship.
     * Cardinality: Many Exceptions created/flagged by 1 User (N:1).
     *
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
