@extends('layouts.app')

@section('title', 'Create Global Shipment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-800 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Create New Global Shipment</h1>
            <p class="text-xs text-slate-400 mt-1">Book containerized freight, establish route origin/destination, and define cargo manifest.</p>
        </div>
        <a href="{{ route('shipments.index') }}" class="text-xs text-slate-400 hover:text-white transition">&larr; Back to Shipments</a>
    </div>

    <form method="POST" action="{{ route('shipments.store') }}" class="space-y-6">
        @csrf

        <!-- Card 1: Core Routing & Master Data -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <h2 class="text-sm font-semibold text-white uppercase tracking-wider text-sky-400">1. Booking & Route Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Shipment Number (Optional) -->
                <div>
                    <label for="shipment_number" class="block text-xs font-medium text-slate-300 mb-1">
                        Tracking / B/L Number
                        <span class="text-slate-500 font-normal">(Auto-generated if empty)</span>
                    </label>
                    <input
                        type="text"
                        id="shipment_number"
                        name="shipment_number"
                        value="{{ old('shipment_number') }}"
                        placeholder="e.g. CT-SEA-EUR-2026-99001"
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white font-mono uppercase focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                    @error('shipment_number') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Customer Account -->
                <div>
                    <label for="customer_id" class="block text-xs font-medium text-slate-300 mb-1">Consignee Customer Account *</label>
                    <select id="customer_id" name="customer_id" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Select Customer...</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Carrier -->
                <div>
                    <label for="carrier_id" class="block text-xs font-medium text-slate-300 mb-1">Freight Carrier *</label>
                    <select id="carrier_id" name="carrier_id" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Select Shipping Line...</option>
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier->id }}" {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                                [{{ $carrier->code }}] {{ $carrier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('carrier_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Origin & Destination -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="origin_location_id" class="block text-xs font-medium text-slate-300 mb-1">Origin Port / Terminal *</label>
                    <select id="origin_location_id" name="origin_location_id" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Select Origin Hub...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('origin_location_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="destination_location_id" class="block text-xs font-medium text-slate-300 mb-1">Destination Port / Hub *</label>
                    <select id="destination_location_id" name="destination_location_id" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="">Select Destination Hub...</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('destination_location_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Schedules -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="expected_departure_at" class="block text-xs font-medium text-slate-300 mb-1">Expected Departure Date & Time *</label>
                    <input
                        type="datetime-local"
                        id="expected_departure_at"
                        name="expected_departure_at"
                        value="{{ old('expected_departure_at', now()->addDays(2)->format('Y-m-d\TH:i')) }}"
                        required
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                    @error('expected_departure_at') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="expected_arrival_at" class="block text-xs font-medium text-slate-300 mb-1">Expected Arrival Date & Time *</label>
                    <input
                        type="datetime-local"
                        id="expected_arrival_at"
                        name="expected_arrival_at"
                        value="{{ old('expected_arrival_at', now()->addDays(22)->format('Y-m-d\TH:i')) }}"
                        required
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                    @error('expected_arrival_at') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: Cargo Specifications -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <h2 class="text-sm font-semibold text-white uppercase tracking-wider text-sky-400">2. Freight & Volume Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-1">
                    <label for="cargo_type" class="block text-xs font-medium text-slate-300 mb-1">Cargo Type / Commodity *</label>
                    <input
                        type="text"
                        id="cargo_type"
                        name="cargo_type"
                        value="{{ old('cargo_type') }}"
                        required
                        placeholder="e.g. Specialty Green Coffee (FCL)"
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                    @error('cargo_type') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="volume_cbm" class="block text-xs font-medium text-slate-300 mb-1">Volume (CBM) *</label>
                    <input
                        type="number"
                        step="0.01"
                        id="volume_cbm"
                        name="volume_cbm"
                        value="{{ old('volume_cbm', '45.00') }}"
                        required
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                    @error('volume_cbm') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="current_status" class="block text-xs font-medium text-slate-300 mb-1">Initial Status</label>
                    <select id="current_status" name="current_status" class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('current_status', 'BOOKED') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Card 3: Dynamic Shipment Items (Multiple Items Support via Vanilla JS) -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-white uppercase tracking-wider text-sky-400">3. Cargo Line Items Manifest</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Add packaged goods, quantities, and weights for this container shipment.</p>
                </div>
                <button
                    type="button"
                    onclick="addItemRow()"
                    class="px-3 py-1.5 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 text-sky-300 border border-sky-500/30 text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Item
                </button>
            </div>

            <!-- Items Container -->
            <div id="items-container" class="space-y-3">
                <!-- Initial Item Row -->
                <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 p-3 bg-slate-950 rounded-xl border border-slate-800 items-end">
                    <div class="sm:col-span-6">
                        <label class="block text-[11px] font-medium text-slate-400 mb-1">Item Description *</label>
                        <input
                            type="text"
                            name="items[0][description]"
                            value="{{ old('items.0.description', 'Industrial Microcontroller Trays') }}"
                            required
                            placeholder="Description of packaged cargo"
                            class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:ring-1 focus:ring-sky-500"
                        />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[11px] font-medium text-slate-400 mb-1">Quantity *</label>
                        <input
                            type="number"
                            name="items[0][quantity]"
                            value="{{ old('items.0.quantity', 150) }}"
                            min="1"
                            required
                            class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:ring-1 focus:ring-sky-500"
                        />
                    </div>
                    <div class="sm:col-span-3">
                        <label class="block text-[11px] font-medium text-slate-400 mb-1">Weight (KG) *</label>
                        <input
                            type="number"
                            step="0.01"
                            name="items[0][weight_kg]"
                            value="{{ old('items.0.weight_kg', 6500.00) }}"
                            min="0.01"
                            required
                            class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:ring-1 focus:ring-sky-500"
                        />
                    </div>
                    <div class="sm:col-span-1 text-right">
                        <button
                            type="button"
                            onclick="removeItemRow(this)"
                            class="p-2 text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 rounded-lg transition cursor-pointer"
                            title="Remove line item"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Actions -->
        <div class="pt-2 flex justify-end gap-3">
            <a href="{{ route('shipments.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300">
                Cancel
            </a>
            <button
                type="submit"
                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition cursor-pointer"
            >
                Confirm & Create Shipment
            </button>
        </div>
    </form>
</div>

<!-- Vanilla JS for Dynamic Line Items -->
<script>
    let itemIndex = 1;

    function addItemRow() {
        const container = document.getElementById('items-container');
        const div = document.createElement('div');
        div.className = 'item-row grid grid-cols-1 sm:grid-cols-12 gap-3 p-3 bg-slate-950 rounded-xl border border-slate-800 items-end';
        div.innerHTML = `
            <div class="sm:col-span-6">
                <label class="block text-[11px] font-medium text-slate-400 mb-1">Item Description *</label>
                <input
                    type="text"
                    name="items[${itemIndex}][description]"
                    required
                    placeholder="e.g. Spare Parts, Bulk Grain"
                    class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:ring-1 focus:ring-sky-500"
                />
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-medium text-slate-400 mb-1">Quantity *</label>
                <input
                    type="number"
                    name="items[${itemIndex}][quantity]"
                    value="1"
                    min="1"
                    required
                    class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:ring-1 focus:ring-sky-500"
                />
            </div>
            <div class="sm:col-span-3">
                <label class="block text-[11px] font-medium text-slate-400 mb-1">Weight (KG) *</label>
                <input
                    type="number"
                    step="0.01"
                    name="items[${itemIndex}][weight_kg]"
                    value="100.00"
                    min="0.01"
                    required
                    class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:ring-1 focus:ring-sky-500"
                />
            </div>
            <div class="sm:col-span-1 text-right">
                <button
                    type="button"
                    onclick="removeItemRow(this)"
                    class="p-2 text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 rounded-lg transition cursor-pointer"
                    title="Remove line item"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>
        `;
        container.appendChild(div);
        itemIndex++;
    }

    function removeItemRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('.item-row').remove();
        } else {
            alert('A shipment must contain at least one manifest item.');
        }
    }
</script>
@endsection
