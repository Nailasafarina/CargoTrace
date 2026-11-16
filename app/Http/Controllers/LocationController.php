<?php

namespace App\Http\Controllers;

use App\Enums\LocationType;
use App\Http\Requests\Location\StoreLocationRequest;
use App\Http\Requests\Location\UpdateLocationRequest;
use App\Models\Location;
use App\Services\GeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    /**
     * Resolve GPS coordinates via GeocodingService for interactive form lookup.
     */
    public function geocode(Request $request, GeocodingService $geocodingService): JsonResponse
    {
        $this->authorize('create', Location::class);

        $query = $request->input('query', '');
        $result = $geocodingService->geocode($query);

        return response()->json($result);
    }

    /**
     * Display a listing of locations.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Location::class);

        $query = Location::query()
            ->withCount(['originShipments', 'destinationShipments']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('type', $type);
        }

        $locations = $query->orderBy('country')->orderBy('city')->paginate(12)->withQueryString();
        $types = LocationType::cases();

        return view('locations.index', compact('locations', 'types'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create(): View
    {
        $this->authorize('create', Location::class);

        $types = LocationType::cases();

        return view('locations.create', compact('types'));
    }

    /**
     * Store a newly created location.
     */
    public function store(StoreLocationRequest $request): RedirectResponse
    {
        Location::create($request->validated());

        return redirect()->route('locations.index')
            ->with('status', 'Waypoint Hub / Port created successfully.');
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location): View
    {
        $this->authorize('view', $location);

        $location->loadCount(['originShipments', 'destinationShipments', 'trackingEvents']);

        return view('locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location): View
    {
        $this->authorize('update', $location);

        $types = LocationType::cases();

        return view('locations.edit', compact('location', 'types'));
    }

    /**
     * Update the specified location.
     */
    public function update(UpdateLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return redirect()->route('locations.index')
            ->with('status', "Location {$location->name} updated successfully.");
    }

    /**
     * Remove the specified location.
     */
    public function destroy(Location $location): RedirectResponse
    {
        $this->authorize('delete', $location);

        if ($location->originShipments()->exists() || $location->destinationShipments()->exists()) {
            return back()->with('error', 'Cannot delete location: Active shipments originate from or are destined to this location.');
        }

        $location->delete();

        return redirect()->route('locations.index')
            ->with('status', 'Location deleted successfully.');
    }
}
