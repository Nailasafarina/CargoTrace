<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use App\Services\ShipmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShipmentController extends Controller
{
    /**
     * Display a paginated listing of shipments with server-side filters & sorting.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Shipment::class);

        $user = $request->user();

        $query = Shipment::query()
            ->with(['customer', 'carrier', 'originLocation', 'destinationLocation', 'items', 'exceptions']);

        // Scope strictly for customer: only their own consignments
        if ($user->isCustomer()) {
            $query->where('customer_id', $user->id);
        } elseif ($request->filled('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        // Search Filter (shipment number or cargo description)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('cargo_type', 'like', "%{$search}%");
            });
        }

        // Lifecycle Status Filter
        if ($request->filled('status')) {
            $query->where('current_status', $request->input('status'));
        }

        // Delay Status Filter
        if ($request->filled('delay_status')) {
            $query->where('delay_status', $request->input('delay_status'));
        }

        // Carrier Filter
        if ($request->filled('carrier_id')) {
            $query->where('carrier_id', $request->input('carrier_id'));
        }

        // Server-Side Sorting
        $allowedSorts = [
            'created_at',
            'expected_departure_at',
            'expected_arrival_at',
            'estimated_arrival_at',
            'weight_kg',
            'current_status',
            'shipment_number',
        ];
        $sortBy = in_array($request->input('sort_by'), $allowedSorts, true)
            ? $request->input('sort_by')
            : 'created_at';
        $sortOrder = strtolower($request->input('sort_order')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortOrder);

        // Server-Side Pagination
        $perPage = min((int) $request->input('per_page', 15), 100);
        $shipments = $query->paginate($perPage);

        return ShipmentResource::collection($shipments);
    }

    /**
     * Store a newly created shipment via ShipmentService.
     */
    public function store(StoreShipmentRequest $request, ShipmentService $shipmentService): JsonResponse
    {
        $this->authorize('create', Shipment::class);

        $shipment = $shipmentService->createShipment($request->validated(), $request->user());

        return (new ShipmentResource($shipment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified shipment.
     */
    public function show(Request $request, Shipment $shipment): ShipmentResource
    {
        $this->authorize('view', $shipment);

        $shipment->load([
            'customer',
            'carrier',
            'originLocation',
            'destinationLocation',
            'items',
            'trackingEvents.location',
            'exceptions',
        ]);

        return new ShipmentResource($shipment);
    }

    /**
     * Update the specified shipment via ShipmentService.
     */
    public function update(UpdateShipmentRequest $request, Shipment $shipment, ShipmentService $shipmentService): ShipmentResource
    {
        $this->authorize('update', $shipment);

        $updated = $shipmentService->updateShipment($shipment, $request->validated(), $request->user());

        return new ShipmentResource($updated);
    }

    /**
     * Remove the specified shipment via ShipmentService.
     */
    public function destroy(Request $request, Shipment $shipment, ShipmentService $shipmentService): JsonResponse
    {
        $this->authorize('delete', $shipment);

        $shipmentNumber = $shipment->shipment_number;
        $shipmentService->deleteShipment($shipment, $request->user());

        return response()->json([
            'message' => "Shipment {$shipmentNumber} was deleted successfully.",
        ], 200);
    }
}
