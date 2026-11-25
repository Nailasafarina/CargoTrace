<?php

namespace Tests\Unit;

use App\DTOs\EtaEvaluationResult;
use App\Enums\DelayStatus;
use App\Enums\ExceptionStatus;
use App\Enums\ShipmentStatus;
use PHPUnit\Framework\TestCase;

class DomainStateTransitionTest extends TestCase
{
    public function test_shipment_status_enforces_forward_state_machine_and_terminal_delivered(): void
    {
        $this->assertTrue(ShipmentStatus::Created->canTransitionTo(ShipmentStatus::Booked));
        $this->assertTrue(ShipmentStatus::Booked->canTransitionTo(ShipmentStatus::PickedUp));
        $this->assertTrue(ShipmentStatus::PickedUp->canTransitionTo(ShipmentStatus::InTransit));
        $this->assertTrue(ShipmentStatus::InTransit->canTransitionTo(ShipmentStatus::Delivered));

        // Terminal state cannot transition anywhere
        $this->assertFalse(ShipmentStatus::Delivered->canTransitionTo(ShipmentStatus::InTransit));
        $this->assertFalse(ShipmentStatus::Delivered->canTransitionTo(ShipmentStatus::Cancelled));
        $this->assertFalse(ShipmentStatus::Cancelled->canTransitionTo(ShipmentStatus::Booked));

        // Backward transition rejected
        $this->assertFalse(ShipmentStatus::InTransit->canTransitionTo(ShipmentStatus::Booked));
    }

    public function test_exception_status_enforces_lifecycle_and_terminal_resolved(): void
    {
        $this->assertTrue(ExceptionStatus::Open->canTransitionTo(ExceptionStatus::Investigating));
        $this->assertTrue(ExceptionStatus::Open->canTransitionTo(ExceptionStatus::Resolved));
        $this->assertTrue(ExceptionStatus::Investigating->canTransitionTo(ExceptionStatus::Resolved));

        $this->assertFalse(ExceptionStatus::Resolved->canTransitionTo(ExceptionStatus::Open));
        $this->assertFalse(ExceptionStatus::Resolved->canTransitionTo(ExceptionStatus::Investigating));
    }

    public function test_eta_evaluation_result_dto_serializes_deterministically(): void
    {
        $dto = new EtaEvaluationResult(
            isDelayed: true,
            delayHours: 6,
            thresholdHours: 4,
            delayStatus: DelayStatus::Delayed,
            classification: 'MODERATE_DELAY',
            reason: 'Corridor congestion exceeds threshold',
        );

        $serialized = $dto->toArray();

        $this->assertTrue($serialized['is_delayed']);
        $this->assertSame(6, $serialized['delay_hours']);
        $this->assertSame(4, $serialized['threshold_hours']);
        $this->assertSame('DELAYED', $serialized['delay_status']);
        $this->assertSame('MODERATE_DELAY', $serialized['classification']);
    }
}
