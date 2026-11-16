<?php

namespace App\Http\Controllers;

use App\Enums\DelayStatus;
use App\Enums\ShipmentStatus;
use App\Enums\TrackingEventType;
use App\Http\Requests\Shipment\StoreShipmentRequest;
use App\Http\Requests\Shipment\UpdateShipmentRequest;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Shipment;
use App\Models\User;
use App\Services\ShipmentService;
use App\Services\WeatherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShipmentController extends Controller
{
    /**
     * Inject domain service layer.
     */
    public function __construct(
        protected ShipmentService $shipmentService,
        protected WeatherService $weatherService
    ) {}

    /**
     * Display a listing of shipments.
     * Customers only view their owned shipments; Operators and Admins see full fleet.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Shipment::class);

        $user = $request->user();

        // Customer Data Isolation
        if ($user->isCustomer()) {
            $query = $user->shipments();
        } else {
            $query = Shipment::query();
        }

        $query->with(['carrier', 'originLocation', 'destinationLocation', 'customer', 'exceptions']);

        // Filter by tracking number / cargo
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('shipment_number', 'like', "%{$search}%")
                    ->orWhere('cargo_type', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('current_status', $status);
        }

        // Filter by delay
        if ($delay = $request->input('delay_status')) {
            $query->where('delay_status', $delay);
        }

        // Filter by carrier
        if ($carrierId = $request->input('carrier_id')) {
            $query->where('carrier_id', $carrierId);
        }

        // Filter by origin location
        if ($originId = $request->input('origin_id')) {
            $query->where('origin_location_id', $originId);
        }

        // Filter by destination location
        if ($destinationId = $request->input('destination_id')) {
            $query->where('destination_location_id', $destinationId);
        }

        // Filter by date range (expected_departure_at)
        if ($dateFrom = $request->input('date_from')) {
            $query->whereDate('expected_departure_at', '>=', $dateFrom);
        }
        if ($dateTo = $request->input('date_to')) {
            $query->whereDate('expected_departure_at', '<=', $dateTo);
        }

        // Server-side dynamic sorting with whitelist protection
        $allowedSorts = ['expected_departure_at', 'expected_arrival_at', 'shipment_number', 'created_at', 'current_status', 'weight_kg'];
        $sortBy = in_array($request->input('sort_by'), $allowedSorts, true) ? $request->input('sort_by') : 'expected_departure_at';
        $sortDir = strtolower($request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortDir);

        $shipments = $query->paginate(10)->withQueryString();
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $statuses = ShipmentStatus::cases();
        $delayStatuses = DelayStatus::cases();

        return view('shipments.index', compact(
            'shipments',
            'carriers',
            'locations',
            'statuses',
            'delayStatuses',
            'sortBy',
            'sortDir'
        ));
    }

    /**
     * Show the form for creating a new shipment.
     */
    public function create(): View
    {
        $this->authorize('create', Shipment::class);

        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('country')->orderBy('name')->get();
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $statuses = ShipmentStatus::cases();
        $delayStatuses = DelayStatus::cases();

        return view('shipments.create', compact('carriers', 'locations', 'customers', 'statuses', 'delayStatuses'));
    }

    /**
     * Store a newly created shipment via ShipmentService.
     */
    public function store(StoreShipmentRequest $request): RedirectResponse
    {
        $shipment = $this->shipmentService->createShipment(
            $request->validated(),
            $request->user()
        );

        return redirect()->route('shipments.show', $shipment)
            ->with('status', "Shipment {$shipment->shipment_number} created successfully.");
    }

    /**
     * Display the specified shipment.
     * Enforces customer ownership check through ShipmentPolicy.
     */
    public function show(Shipment $shipment): View
    {
        $this->authorize('view', $shipment);

        $shipment->load([
            'carrier',
            'originLocation',
            'destinationLocation',
            'customer',
            'creator',
            'items',
            'trackingEvents' => fn ($q) => $q->orderBy('occurred_at', 'asc')->with('location'),
            'exceptions',
        ]);

        $latestEvent = $shipment->trackingEvents->last();
        $currentLocation = $latestEvent?->location ?? $shipment->originLocation;
        $locations = Location::orderBy('country')->orderBy('name')->get();
        $statuses = ShipmentStatus::cases();
        $eventTypes = TrackingEventType::cases();

        // 1. Fetch live contextual weather for current position if GPS coordinates available
        $weatherLat = $latestEvent?->latitude ?? $currentLocation?->latitude;
        $weatherLon = $latestEvent?->longitude ?? $currentLocation?->longitude;

        $weather = ($weatherLat !== null && $weatherLon !== null)
            ? $this->weatherService->getWeather((float) $weatherLat, (float) $weatherLon)
            : [
                'available' => false,
                'condition' => 'No Coordinates',
                'temperature' => null,
                'wind_speed' => null,
                'humidity' => null,
                'disclaimer' => WeatherService::DISCLAIMER,
                'message' => 'Coordinates unavailable for weather lookup.',
            ];

        // 2. Prepare structured GeoJSON/Leaflet Map Data
        $originCoords = ($shipment->originLocation && $shipment->originLocation->latitude && $shipment->originLocation->longitude)
            ? [
                'name' => $shipment->originLocation->name,
                'city' => $shipment->originLocation->city,
                'country' => $shipment->originLocation->country,
                'lat' => (float) $shipment->originLocation->latitude,
                'lng' => (float) $shipment->originLocation->longitude,
            ]
            : null;

        $destinationCoords = ($shipment->destinationLocation && $shipment->destinationLocation->latitude && $shipment->destinationLocation->longitude)
            ? [
                'name' => $shipment->destinationLocation->name,
                'city' => $shipment->destinationLocation->city,
                'country' => $shipment->destinationLocation->country,
                'lat' => (float) $shipment->destinationLocation->latitude,
                'lng' => (float) $shipment->destinationLocation->longitude,
            ]
            : null;

        $waypoints = $shipment->trackingEvents
            ->filter(fn ($e) => $e->latitude !== null && $e->longitude !== null)
            ->map(fn ($e) => [
                'id' => $e->id,
                'lat' => (float) $e->latitude,
                'lng' => (float) $e->longitude,
                'status' => $e->event_type->label(),
                'description' => $e->description,
                'occurred_at' => $e->occurred_at->format('d M Y, H:i').' UTC',
                'is_exception' => (bool) $e->is_exception,
                'location_name' => $e->location?->name ?? 'Maritime Corridor',
            ])
            ->values()
            ->all();

        $hasMapCoordinates = $originCoords !== null || $destinationCoords !== null || ! empty($waypoints);

        $mapData = [
            'has_coordinates' => $hasMapCoordinates,
            'origin' => $originCoords,
            'destination' => $destinationCoords,
            'waypoints' => $waypoints,
        ];

        return view('shipments.show', compact(
            'shipment',
            'latestEvent',
            'currentLocation',
            'locations',
            'statuses',
            'eventTypes',
            'weather',
            'mapData'
        ));
    }

    /**
     * Show the form for editing the specified shipment.
     */
    public function edit(Shipment $shipment): View
    {
        $this->authorize('update', $shipment);

        $shipment->load('items');
        $carriers = Carrier::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('country')->orderBy('name')->get();
        $customers = User::where('role', 'customer')->orderBy('name')->get();
        $statuses = ShipmentStatus::cases();
        $delayStatuses = DelayStatus::cases();

        return view('shipments.edit', compact('shipment', 'carriers', 'locations', 'customers', 'statuses', 'delayStatuses'));
    }

    /**
     * Update the specified shipment via ShipmentService.
     */
    public function update(UpdateShipmentRequest $request, Shipment $shipment): RedirectResponse
    {
        $shipment = $this->shipmentService->updateShipment(
            $shipment,
            $request->validated(),
            $request->user()
        );

        return redirect()->route('shipments.show', $shipment)
            ->with('status', "Shipment {$shipment->shipment_number} updated successfully.");
    }

    /**
     * Remove the specified shipment via ShipmentService.
     * Allowed for Administrator only.
     */
    public function destroy(Shipment $shipment, Request $request): RedirectResponse
    {
        $this->authorize('delete', $shipment);

        $number = $shipment->shipment_number;
        $this->shipmentService->deleteShipment($shipment, $request->user());

        return redirect()->route('shipments.index')
            ->with('status', "Shipment {$number} deleted successfully.");
    }
}
