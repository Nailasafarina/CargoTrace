<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackingEvent\StoreTrackingEventRequest;
use App\Models\Shipment;
use App\Services\TrackingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class TrackingEventController extends Controller
{
    public function __construct(
        protected TrackingService $trackingService
    ) {}

    /**
     * Store a new tracking event for the specified shipment.
     */
    public function store(StoreTrackingEventRequest $request, Shipment $shipment): RedirectResponse|JsonResponse
    {
        $event = $this->trackingService->addEvent(
            $shipment,
            $request->validated(),
            $request->user()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Tracking event logged successfully.',
                'event' => $event,
                'shipment_status' => $shipment->fresh()->current_status->value,
            ], 201);
        }

        return redirect()->route('shipments.show', $shipment)
            ->with('status', 'Tracking milestone and shipment status updated successfully.');
    }
}
