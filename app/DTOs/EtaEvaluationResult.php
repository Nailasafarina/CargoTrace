<?php

namespace App\DTOs;

use App\Enums\DelayStatus;

readonly class EtaEvaluationResult
{
    public function __construct(
        public bool $isDelayed,
        public int $delayHours,
        public int $thresholdHours,
        public DelayStatus $delayStatus,
        public string $classification,
        public string $reason,
        public ?string $reasonPayload = null
    ) {}

    /**
     * Convert result to array format.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'is_delayed' => $this->isDelayed,
            'delay_hours' => $this->delayHours,
            'threshold_hours' => $this->thresholdHours,
            'delay_status' => $this->delayStatus->value,
            'classification' => $this->classification,
            'reason' => $this->reason,
        ];
    }
}
