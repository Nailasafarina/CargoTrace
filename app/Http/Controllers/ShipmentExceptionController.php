<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShipmentException\StoreShipmentExceptionRequest;
use App\Http\Requests\ShipmentException\UpdateShipmentExceptionStatusRequest;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ShipmentExceptionController extends Controller
{
    public function __construct(
        protected ExceptionService $exceptionService
    ) {}

    /**
     * Store a newly created exception for the shipment.
     */
    public function store(StoreShipmentExceptionRequest $request, Shipment $shipment): RedirectResponse|JsonResponse
    {
        $exception = $this->exceptionService->createException(
            $shipment,
            $request->validated(),
            $request->user()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Shipment exception flagged successfully.',
                'exception' => $exception,
            ], 201);
        }

        return redirect()->route('shipments.show', $shipment)
            ->with('status', 'Shipment exception successfully flagged and operational alert broadcasted.');
    }

    /**
     * Update the lifecycle status of an existing exception.
     */
    public function update(UpdateShipmentExceptionStatusRequest $request, ShipmentException $exception): RedirectResponse|JsonResponse
    {
        $updatedException = $this->exceptionService->updateStatus(
            $exception,
            $request->validated('status'),
            $request->validated('resolution_notes'),
            $request->user()
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Exception status updated successfully.',
                'exception' => $updatedException,
            ], 200);
        }

        return redirect()->route('shipments.show', $exception->shipment_id)
            ->with('status', "Exception #{$exception->id} status updated to {$updatedException->status->label()}.");
    }
}
