<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrackingEvent\StoreTrackingEventRequest;
use App\Http\Resources\LocationResource;
use App\Http\Resources\TrackingEventResource;
use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Get the chronological tracking milestone timeline and current position for a shipment.
     */
    public function index(Request $request, Shipment $shipment): JsonResponse
    {
        $this->authorize('view', $shipment);

        $events = $shipment->trackingEvents()
            ->with(['location', 'creator'])
            ->orderBy('occurred_at', 'asc')
            ->get();

        $latestEventWithLocation = $shipment->trackingEvents()
            ->whereNotNull('location_id')
            ->latest('occurred_at')
            ->first();

        $currentLocation = $latestEventWithLocation?->location ?? $shipment->originLocation;

        return response()->json([
            'shipment_number' => $shipment->shipment_number,
            'current_status' => $shipment->current_status->value,
            'delay_status' => $shipment->delay_status->value,
            'current_location' => new LocationResource($currentLocation),
            'timeline' => TrackingEventResource::collection($events),
        ], 200);
    }

    /**
     * Record a new tracking milestone event via TrackingService.
     */
    public function store(
        StoreTrackingEventRequest $request,
        Shipment $shipment,
        TrackingService $trackingService
    ): JsonResponse {
        $event = $trackingService->addEvent(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return (new TrackingEventResource($event->load(['location', 'creator'])))
            ->response()
            ->setStatusCode(201);
    }
}
