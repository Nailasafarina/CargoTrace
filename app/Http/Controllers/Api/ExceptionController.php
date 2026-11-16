<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShipmentException\StoreShipmentExceptionRequest;
use App\Http\Requests\ShipmentException\UpdateShipmentExceptionStatusRequest;
use App\Http\Resources\ShipmentExceptionResource;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Services\ExceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExceptionController extends Controller
{
    /**
     * List all exceptions recorded for a shipment.
     */
    public function index(Request $request, Shipment $shipment): AnonymousResourceCollection
    {
        $this->authorize('view', $shipment);

        return ShipmentExceptionResource::collection($shipment->exceptions);
    }

    /**
     * Store a newly created exception for the shipment via ExceptionService.
     */
    public function store(
        StoreShipmentExceptionRequest $request,
        Shipment $shipment,
        ExceptionService $exceptionService
    ): JsonResponse {
        $exception = $exceptionService->createException(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return (new ShipmentExceptionResource($exception))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Update the lifecycle state of an exception via ExceptionService.
     */
    public function update(
        UpdateShipmentExceptionStatusRequest $request,
        ShipmentException $exception,
        ExceptionService $exceptionService
    ): ShipmentExceptionResource {
        $updated = $exceptionService->updateStatus(
            $exception,
            $request->validated('status'),
            $request->validated('resolution_notes'),
            $request->user()
        );

        return new ShipmentExceptionResource($updated);
    }
}
