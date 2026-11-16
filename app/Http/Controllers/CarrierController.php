<?php

namespace App\Http\Controllers;

use App\Http\Requests\Carrier\StoreCarrierRequest;
use App\Http\Requests\Carrier\UpdateCarrierRequest;
use App\Models\Carrier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarrierController extends Controller
{
    /**
     * Display a listing of carriers.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Carrier::class);

        $query = Carrier::query()->withCount('shipments');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        $carriers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('carriers.index', compact('carriers'));
    }

    /**
     * Show the form for creating a new carrier.
     */
    public function create(): View
    {
        $this->authorize('create', Carrier::class);

        return view('carriers.create');
    }

    /**
     * Store a newly created carrier.
     */
    public function store(StoreCarrierRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        Carrier::create($validated);

        return redirect()->route('carriers.index')
            ->with('status', 'Freight Carrier created successfully.');
    }

    /**
     * Display the specified carrier.
     */
    public function show(Carrier $carrier): View
    {
        $this->authorize('view', $carrier);

        $carrier->loadCount('shipments');
        $recentShipments = $carrier->shipments()
            ->with(['originLocation', 'destinationLocation', 'customer'])
            ->latest()
            ->take(5)
            ->get();

        return view('carriers.show', compact('carrier', 'recentShipments'));
    }

    /**
     * Show the form for editing the specified carrier.
     */
    public function edit(Carrier $carrier): View
    {
        $this->authorize('update', $carrier);

        return view('carriers.edit', compact('carrier'));
    }

    /**
     * Update the specified carrier.
     */
    public function update(UpdateCarrierRequest $request, Carrier $carrier): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_active'] = $request->boolean('is_active', true);

        $carrier->update($validated);

        return redirect()->route('carriers.index')
            ->with('status', "Carrier {$carrier->name} updated successfully.");
    }

    /**
     * Remove the specified carrier.
     */
    public function destroy(Carrier $carrier): RedirectResponse
    {
        $this->authorize('delete', $carrier);

        if ($carrier->shipments()->exists()) {
            return back()->with('error', 'Cannot delete carrier: Active or historical shipments are linked to this carrier.');
        }

        $carrier->delete();

        return redirect()->route('carriers.index')
            ->with('status', 'Carrier deleted successfully.');
    }
}
