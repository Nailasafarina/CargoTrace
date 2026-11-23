@extends('layouts.app')

@section('title', __('shipments.edit_shipment') . ' ' . $shipment->shipment_number)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-200 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('shipments.edit_shipment') }}:</h1>
                <span class="font-mono text-xl font-bold text-blue-600">{{ $shipment->shipment_number }}</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">{{ __('shipments.form.edit_subtitle') }}</p>
        </div>
        <a href="{{ route('shipments.show', $shipment) }}" class="text-xs font-semibold text-slate-600 hover:text-blue-600 transition">&larr; {{ __('common.actions.back') }}</a>
    </div>

    <form method="POST" action="{{ route('shipments.update', $shipment) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Hidden Shipment Number (Fixed) -->
        <input type="hidden" name="shipment_number" value="{{ $shipment->shipment_number }}">

        <!-- Card 1: Status & Delay Updates (Crucial for Operators) -->
        <div class="p-6 rounded-2xl bg-white border border-amber-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-amber-700">{{ __('shipments.form.ops_status_title') }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Status -->
                <div>
                    <label for="current_status" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.current_lifecycle_status') }}</label>
                    <select id="current_status" name="current_status" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('current_status', $shipment->current_status->value) === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('current_status') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Delay Status -->
                <div>
                    <label for="delay_status" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.delay_classification') }}</label>
                    <select id="delay_status" name="delay_status" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        @foreach($delayStatuses as $dst)
                            <option value="{{ $dst->value }}" {{ old('delay_status', $shipment->delay_status->value) === $dst->value ? 'selected' : '' }}>
                                {{ $dst->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('delay_status') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Delay Hours -->
                <div>
                    <label for="delay_hours" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.cumulative_delay') }}</label>
                    <input
                        type="number"
                        min="0"
                        id="delay_hours"
                        name="delay_hours"
                        value="{{ old('delay_hours', $shipment->delay_hours ?? 0) }}"
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                    @error('delay_hours') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Card 2: Routing & Carrier Details -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-sm font-bold uppercase tracking-wider text-blue-600">{{ __('shipments.form.route_carrier_details') }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Customer -->
                <div>
                    <label for="customer_id" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.consignee_customer') }}</label>
                    <select id="customer_id" name="customer_id" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        @foreach($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id', $shipment->customer_id) == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <!-- Carrier -->
                <div>
                    <label for="carrier_id" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.carrier_line') }}</label>
                    <select id="carrier_id" name="carrier_id" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        @foreach($carriers as $carrier)
                            <option value="{{ $carrier->id }}" {{ old('carrier_id', $shipment->carrier_id) == $carrier->id ? 'selected' : '' }}>
                                [{{ $carrier->code }}] {{ $carrier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('carrier_id') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Origin & Destination -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="origin_location_id" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.origin_port') }}</label>
                    <select id="origin_location_id" name="origin_location_id" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('origin_location_id', $shipment->origin_location_id) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('origin_location_id') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="destination_location_id" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.destination_port') }}</label>
                    <select id="destination_location_id" name="destination_location_id" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('destination_location_id', $shipment->destination_location_id) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                            </option>
                        @endforeach
                    </select>
                    @error('destination_location_id') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Schedules -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="expected_departure_at" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.departure_schedule') }}</label>
                    <input
                        type="datetime-local"
                        id="expected_departure_at"
                        name="expected_departure_at"
                        value="{{ old('expected_departure_at', $shipment->expected_departure_at->format('Y-m-d\TH:i')) }}"
                        required
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                    @error('expected_departure_at') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="expected_arrival_at" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.expected_arrival') }}</label>
                    <input
                        type="datetime-local"
                        id="expected_arrival_at"
                        name="expected_arrival_at"
                        value="{{ old('expected_arrival_at', $shipment->expected_arrival_at->format('Y-m-d\TH:i')) }}"
                        required
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                    @error('expected_arrival_at') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="estimated_arrival_at" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.dynamic_eta') }}</label>
                    <input
                        type="datetime-local"
                        id="estimated_arrival_at"
                        name="estimated_arrival_at"
                        value="{{ old('estimated_arrival_at', $shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('Y-m-d\TH:i') : '') }}"
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                    @error('estimated_arrival_at') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Cargo specs -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="cargo_type" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.cargo_commodity') }}</label>
                    <input
                        type="text"
                        id="cargo_type"
                        name="cargo_type"
                        value="{{ old('cargo_type', $shipment->cargo_type) }}"
                        required
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                </div>

                <div>
                    <label for="weight_kg" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.total_weight_kg') }}</label>
                    <input
                        type="number"
                        step="0.01"
                        id="weight_kg"
                        name="weight_kg"
                        value="{{ old('weight_kg', $shipment->weight_kg) }}"
                        required
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                </div>

                <div>
                    <label for="volume_cbm" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('shipments.form.volume_cbm') }}</label>
                    <input
                        type="number"
                        step="0.01"
                        id="volume_cbm"
                        name="volume_cbm"
                        value="{{ old('volume_cbm', $shipment->volume_cbm) }}"
                        required
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                </div>
            </div>
        </div>

        <!-- Card 3: Dynamic Manifest Items -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-blue-600">{{ __('shipments.form.manifest_title') }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ __('shipments.form.manage_manifest_desc') }}</p>
                </div>
                <button
                    type="button"
                    onclick="addItemRow()"
                    class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('shipments.form.add_item') }}
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
                    <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 items-end">
                        <div class="sm:col-span-6">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">{{ __('shipments.form.item_description') }}</label>
                            <input
                                type="text"
                                name="items[{{ $index }}][description]"
                                value="{{ $desc }}"
                                required
                                class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                            />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">{{ __('shipments.form.quantity') }}</label>
                            <input
                                type="number"
                                name="items[{{ $index }}][quantity]"
                                value="{{ $qty }}"
                                min="1"
                                required
                                class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                            />
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">{{ __('shipments.form.weight_kg') }}</label>
                            <input
                                type="number"
                                step="0.01"
                                name="items[{{ $index }}][weight_kg]"
                                value="{{ $wt }}"
                                min="0.01"
                                required
                                class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                            />
                        </div>
                        <div class="sm:col-span-1 text-right">
                            <button
                                type="button"
                                onclick="removeItemRow(this)"
                                class="p-2 text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer"
                                title="{{ __('shipments.form.remove_item') }}"
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
            <a href="{{ route('shipments.show', $shipment) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                {{ __('common.actions.cancel') }}
            </a>
            <button
                type="submit"
                class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm hover:shadow transition cursor-pointer"
            >
                {{ __('shipments.form.save_changes') }}
            </button>
        </div>
    </form>
</div>

<script>
    let itemIndex = {{ count(old('items', $shipment->items)) }};

    function addItemRow() {
        const container = document.getElementById('items-container');
        const div = document.createElement('div');
        div.className = 'item-row grid grid-cols-1 sm:grid-cols-12 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200 items-end';
        div.innerHTML = `
            <div class="sm:col-span-6">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">{{ __('shipments.form.item_description') }}</label>
                <input
                    type="text"
                    name="items[${itemIndex}][description]"
                    required
                    placeholder="{{ __('shipments.form.item_placeholder') }}"
                    class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                />
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">{{ __('shipments.form.quantity') }}</label>
                <input
                    type="number"
                    name="items[${itemIndex}][quantity]"
                    value="1"
                    min="1"
                    required
                    class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                />
            </div>
            <div class="sm:col-span-3">
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">{{ __('shipments.form.weight_kg') }}</label>
                <input
                    type="number"
                    step="0.01"
                    name="items[${itemIndex}][weight_kg]"
                    value="100.00"
                    min="0.01"
                    required
                    class="w-full px-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                />
            </div>
            <div class="sm:col-span-1 text-right">
                <button
                    type="button"
                    onclick="removeItemRow(this)"
                    class="p-2 text-rose-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition cursor-pointer"
                    title="{{ __('shipments.form.remove_item') }}"
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
            alert(@json(__('shipments.form.manifest_min_alert')));
        }
    }
</script>
@endsection
