<?php

namespace App\Services;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Models\Shipment;
use App\Models\ShipmentItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShipmentService
{
    /**
     * Generate a unique, standard shipment tracking number.
     * Format: CT-SEA-EUR-{YEAR}-{RANDOM}
     */
    public function generateShipmentNumber(): string
    {
        do {
            $year = date('Y');
            $randomCode = strtoupper(Str::random(5));
            $number = "CT-SEA-EUR-{$year}-{$randomCode}";
        } while (Shipment::where('shipment_number', $number)->exists());

        return $number;
    }

    /**
     * Create a new shipment with its associated line items inside a DB transaction.
     *
     * @param  array<string, mixed>  $data
     */
    public function createShipment(array $data, User $actor): Shipment
    {
        return DB::transaction(function () use ($data, $actor) {
            // 1. Generate shipment number if omitted
            if (empty($data['shipment_number'])) {
                $data['shipment_number'] = $this->generateShipmentNumber();
            }

            // 2. Set default creator and estimated arrival
            $data['created_by'] = $actor->id;
            if (empty($data['estimated_arrival_at']) && ! empty($data['expected_arrival_at'])) {
                $data['estimated_arrival_at'] = $data['expected_arrival_at'];
            }

            if (empty($data['current_status'])) {
                $data['current_status'] = ShipmentStatus::Booked;
            }

            if (empty($data['delay_status'])) {
                $data['delay_status'] = DelayStatus::OnTime;
            }

            // 3. Extract items if provided
            $itemsData = $data['items'] ?? [];
            unset($data['items']);

            // Calculate weight if items are present and weight is not explicitly set
            if (! empty($itemsData) && empty($data['weight_kg'])) {
                $data['weight_kg'] = array_sum(array_column($itemsData, 'weight_kg'));
            }

            // 4. Create master shipment record
            $shipment = Shipment::create($data);

            // 5. Create shipment items
            foreach ($itemsData as $item) {
                if (! empty($item['description'])) {
                    ShipmentItem::create([
                        'shipment_id' => $shipment->id,
                        'description' => $item['description'],
                        'quantity' => (int) ($item['quantity'] ?? 1),
                        'weight_kg' => (float) ($item['weight_kg'] ?? 0),
                    ]);
                }
            }

            // Evaluate ETA and delay parameters
            app(EtaService::class)->evaluateAndSave($shipment, null, $actor);

            return $shipment->load(['items', 'carrier', 'originLocation', 'destinationLocation', 'customer']);
        });
    }

    /**
     * Update an existing shipment and sync its line items inside a DB transaction.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateShipment(Shipment $shipment, array $data, User $actor): Shipment
    {
        return DB::transaction(function () use ($shipment, $data, $actor) {
            $itemsData = $data['items'] ?? null;
            unset($data['items']);

            // Update shipment attributes
            $shipment->update($data);

            // Sync items if explicitly provided
            if (is_array($itemsData)) {
                $shipment->items()->delete();

                foreach ($itemsData as $item) {
                    if (! empty($item['description'])) {
                        ShipmentItem::create([
                            'shipment_id' => $shipment->id,
                            'description' => $item['description'],
                            'quantity' => (int) ($item['quantity'] ?? 1),
                            'weight_kg' => (float) ($item['weight_kg'] ?? 0),
                        ]);
                    }
                }
            }

            // Automatically re-evaluate ETA and delay status
            app(EtaService::class)->evaluateAndSave($shipment, null, $actor);

            return $shipment->fresh(['items', 'carrier', 'originLocation', 'destinationLocation', 'customer']);
        });
    }

    /**
     * Delete a shipment inside a DB transaction.
     */
    public function deleteShipment(Shipment $shipment, User $actor): bool
    {
        return DB::transaction(function () use ($shipment) {
            return (bool) $shipment->delete();
        });
    }
}
