@extends('layouts.app')

@section('title', 'Edit Shipment ' . $shipment->shipment_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-800 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-white">Edit Shipment:</h1>
                <span class="font-mono text-xl font-bold text-sky-400">{{ $shipment->shipment_number }}</span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Modify routing, milestone status, delay parameters, or cargo items.</p>
        </div>
        <a href="{{ route('shipments.show', $shipment) }}" class="text-xs text-slate-400 hover:text-white transition">&larr; Back to Shipment</a>
    </div>

    <form method="POST" action="{{ route('shipments.update', $shipment) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Hidden Shipment Number (Fixed) -->
        <input type="hidden" name="shipment_number" value="{{ $shipment->shipment_number }}">

        <!-- Card 1: Status & Delay Updates (Crucial for Operators) -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-4">
            <h2 class="text-sm font-semibold text-white uppercase tracking-wider text-amber-400">1. Operations Status & Delay Management</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Status -->
                <div>
                    <label for="current_status" class="block text-xs font-medium text-slate-300 mb-1">Current Lifecycle Status *</label>
                    <select id="current_status" name="current_status" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('current_status', $shipment->current_status->value) === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('current_status') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Delay Status -->
                <div>
                    <label for="delay_status" class="block text-xs font-medium text-slate-300 mb-1">Delay Classification *</label>
                    <select id="delay_status" name="delay_status" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @foreach($delayStatuses as $dst)
                            <option value="{{ $dst->value }}" {{ old('delay_status', $shipment->delay_status->value) === $dst->value ? 'selected' : '' }}>
                                {{ $dst->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('delay_status') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Delay Hours -->
                <div>
                    <label for="delay_hours" class="block text-xs font-medium text-slate-300 mb-1">Cumulative Delay (Hours)</label>
                    <input
                        type="number"
                        min="0"
                        id="delay_hours"
                        name="delay_hours"
                        value="{{ old('delay_hours', $shipment->delay_hours ?? 0) }}"
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                    @error('delay_hours') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: Routing & Carrier Details -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <h2 class="text-sm font-semibold text-white uppercase tracking-wider text-sky-400">2. Route & Carrier Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Customer -->
                <div>
                    <label for="customer_id" class="block text-xs font-medium text-slate-300 mb-1">Consignee Customer *</label>
                    <select id="customer_id" name="customer_id" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id', $shipment->customer_id) == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Carrier -->
                <div>
                    <label for="carrier_id" class="block text-xs font-medium text-slate-300 mb-1">Carrier Line *</label>
                    <select id="carrier_id" name="carrier_id" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier->id }}" {{ old('carrier_id', $shipment->carrier_id) == $carrier->id ? 'selected' : '' }}>
                                [{{ $carrier->code }}] {{ $carrier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('carrier_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Origin & Destination -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="origin_location_id" class="block text-xs font-medium text-slate-300 mb-1">Origin Port / Hub *</label>
                    <select id="origin_location_id" name="origin_location_id" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_location_id', $shipment->origin_location_id) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('origin_location_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="destination_location_id" class="block text-xs font-medium text-slate-300 mb-1">Destination Port / Hub *</label>
                    <select id="destination_location_id" name="destination_location_id" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_location_id', $shipment->destination_location_id) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('destination_location_id') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Schedules -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="expected_departure_at" class="block text-xs font-medium text-slate-300 mb-1">Departure Schedule *</label>
                    <input
                        type="datetime-local"
                        id="expected_departure_at"
                        name="expected_departure_at"
                        value="{{ old('expected_departure_at', $shipment->expected_departure_at->format('Y-m-d\TH:i')) }}"
                        required
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                    @error('expected_departure_at') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="expected_arrival_at" class="block text-xs font-medium text-slate-300 mb-1">Expected Arrival *</label>
                    <input
                        type="datetime-local"
                        id="expected_arrival_at"
                        name="expected_arrival_at"
                        value="{{ old('expected_arrival_at', $shipment->expected_arrival_at->format('Y-m-d\TH:i')) }}"
                        required
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                    @error('expected_arrival_at') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="estimated_arrival_at" class="block text-xs font-medium text-slate-300 mb-1">Dynamic ETA</label>
                    <input
                        type="datetime-local"
                        id="estimated_arrival_at"
                        name="estimated_arrival_at"
                        value="{{ old('estimated_arrival_at', $shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('Y-m-d\TH:i') : '') }}"
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                    @error('estimated_arrival_at') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Cargo specs -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="cargo_type" class="block text-xs font-medium text-slate-300 mb-1">Cargo Commodity *</label>
                    <input
                        type="text"
                        id="cargo_type"
                        name="cargo_type"
                        value="{{ old('cargo_type', $shipment->cargo_type) }}"
                        required
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>

                <div>
                    <label for="weight_kg" class="block text-xs font-medium text-slate-300 mb-1">Total Weight (KG) *</label>
                    <input
                        type="number"
                        step="0.01"
                        id="weight_kg"
                        name="weight_kg"
                        value="{{ old('weight_kg', $shipment->weight_kg) }}"
                        required
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>

                <div>
                    <label for="volume_cbm" class="block text-xs font-medium text-slate-300 mb-1">Volume (CBM) *</label>
                    <input
                        type="number"
                        step="0.01"
                        id="volume_cbm"
                        name="volume_cbm"
                        value="{{ old('volume_cbm', $shipment->volume_cbm) }}"
                        required
                        class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:ring-2 focus:ring-sky-500"
                    />
                </div>
            </div>
        </div>

        <!-- Card 3: Dynamic Manifest Items -->
        <div class="p-6 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-white uppercase tracking-wider text-sky-400">3. Cargo Line Items Manifest</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Manage line item packing lists.</p>
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
                @foreach(old('items', $shipment->items) as $index => $item)
                    @php
                        $desc = is_array($item) ? ($item['description'] ?? '') : $item->description;
                        $qty = is_array($item) ? ($item['quantity'] ?? 1) : $item->quantity;
                        $wt = is_array($item) ? ($item['weight_kg'] ?? 0) : $item->weight_kg;
                    @endphp
                    <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 p-3 bg-slate-950 rounded-xl border border-slate-800 items-end">
                        <div class="sm:col-span-6">
                            <label class="block text-[11px] font-medium text-slate-400 mb-1">Item Description *</label>
                            <input
                                type="text"
                                name="items[{{ $index }}][description]"
                                value="{{ $desc }}"
                                required
                                class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white focus:outline-none focus:ring-1 focus:ring-sky-500"
                            />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-medium text-slate-400 mb-1">Quantity *</label>
                            <input
                                type="number"
                                name="items[{{ $index }}][quantity]"
                                value="{{ $qty }}"
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
                                name="items[{{ $index }}][weight_kg]"
                                value="{{ $wt }}"
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
                @endforeach
            </div>
        </div>

        <!-- Submit Actions -->
        <div class="pt-2 flex justify-end gap-3">
            <a href="{{ route('shipments.show', $shipment) }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300">
                Cancel
            </a>
            <button
                type="submit"
                class="px-6 py-2.5 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-xs font-semibold shadow-lg shadow-sky-500/25 transition cursor-pointer"
            >
                Save Changes
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = {{ count(old('items', $shipment->items)) }};

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
                    placeholder="Description of cargo"
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
