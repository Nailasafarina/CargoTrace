@extends('layouts.app')

@section('title', __('shipments.edit_shipment') . ' ' . $shipment->shipment_number)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="nex-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-[#4A2C35]">{{ __('shipments.edit_shipment') }}:</h1>
                <span class="px-3 py-1 rounded-full bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC] font-mono text-sm font-bold">{{ $shipment->shipment_number }}</span>
            </div>
            <p class="text-xs text-[#8C737B] mt-1">{{ __('shipments.form.edit_subtitle') }}</p>
        </div>
        <a href="{{ route('shipments.show', $shipment) }}" class="px-4 py-2 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-[#6E545D] transition self-start sm:self-auto">
            &larr; {{ __('common.actions.back') }}
        </a>
    </div>

    <form method="POST" action="{{ route('shipments.update', $shipment) }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf
        @method('PUT')
        <input type="hidden" name="shipment_number" value="{{ $shipment->shipment_number }}">

        <!-- Left 8 Cols: Routing, Carrier & Manifest Items -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Routing & Carrier Details -->
            <div class="nex-card p-6 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#C93565]">{{ __('shipments.form.route_carrier_details') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="customer_id" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.consignee_customer') }}</label>
                        <select id="customer_id" name="customer_id" required class="nex-input">
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ old('customer_id', $shipment->customer_id) == $cust->id ? 'selected' : '' }}>
                                    {{ $cust->name }} ({{ $cust->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="carrier_id" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.carrier_line') }}</label>
                        <select id="carrier_id" name="carrier_id" required class="nex-input">
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}" {{ old('carrier_id', $shipment->carrier_id) == $carrier->id ? 'selected' : '' }}>
                                    [{{ $carrier->code }}] {{ $carrier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrier_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="origin_location_id" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.origin_port') }}</label>
                        <select id="origin_location_id" name="origin_location_id" required class="nex-input">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('origin_location_id', $shipment->origin_location_id) == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('origin_location_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="destination_location_id" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.destination_port') }}</label>
                        <select id="destination_location_id" name="destination_location_id" required class="nex-input">
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('destination_location_id', $shipment->destination_location_id) == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('destination_location_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="expected_departure_at" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.departure_schedule') }}</label>
                        <input type="datetime-local" id="expected_departure_at" name="expected_departure_at" value="{{ old('expected_departure_at', $shipment->expected_departure_at->format('Y-m-d\TH:i')) }}" required class="nex-input" />
                        @error('expected_departure_at') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="expected_arrival_at" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.expected_arrival') }}</label>
                        <input type="datetime-local" id="expected_arrival_at" name="expected_arrival_at" value="{{ old('expected_arrival_at', $shipment->expected_arrival_at->format('Y-m-d\TH:i')) }}" required class="nex-input" />
                        @error('expected_arrival_at') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="estimated_arrival_at" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.dynamic_eta') }}</label>
                        <input type="datetime-local" id="estimated_arrival_at" name="estimated_arrival_at" value="{{ old('estimated_arrival_at', $shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('Y-m-d\TH:i') : '') }}" class="nex-input" />
                        @error('estimated_arrival_at') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="cargo_type" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.cargo_commodity') }}</label>
                        <input type="text" id="cargo_type" name="cargo_type" value="{{ old('cargo_type', $shipment->cargo_type) }}" required class="nex-input" />
                    </div>

                    <div>
                        <label for="weight_kg" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.total_weight_kg') }}</label>
                        <input type="number" step="0.01" id="weight_kg" name="weight_kg" value="{{ old('weight_kg', $shipment->weight_kg) }}" required class="nex-input font-mono" />
                    </div>

                    <div>
                        <label for="volume_cbm" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.volume_cbm') }}</label>
                        <input type="number" step="0.01" id="volume_cbm" name="volume_cbm" value="{{ old('volume_cbm', $shipment->volume_cbm) }}" required class="nex-input font-mono" />
                    </div>
                </div>
            </div>

            <!-- Dynamic Manifest Items -->
            <div class="nex-card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-[#C93565]">{{ __('shipments.form.manifest_title') }}</h2>
                        <p class="text-xs text-[#8C737B] mt-0.5">{{ __('shipments.form.manage_manifest_desc') }}</p>
                    </div>
                    <button type="button" onclick="addItemRow()" class="px-3.5 py-1.5 rounded-full bg-[#FDF2F6] hover:bg-[#FCE5EE] text-[#9E224B] border border-[#F8C9DC] text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('shipments.form.add_item') }}
                    </button>
                </div>

                <div id="items-container" class="space-y-3">
                    @foreach(old('items', $shipment->items) as $index => $item)
                        @php
                            $desc = is_array($item) ? ($item['description'] ?? '') : $item->description;
                            $qty = is_array($item) ? ($item['quantity'] ?? 1) : $item->quantity;
                            $wt = is_array($item) ? ($item['weight_kg'] ?? 0) : $item->weight_kg;
                        @endphp
                        <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 p-3.5 bg-[#FAF7F9] rounded-2xl border border-[#ECE4E8] items-end">
                            <div class="sm:col-span-6">
                                <label class="block text-[11px] font-semibold text-[#7D626B] mb-1">{{ __('shipments.form.item_description') }}</label>
                                <input type="text" name="items[{{ $index }}][description]" value="{{ $desc }}" required class="nex-input bg-white" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-semibold text-[#7D626B] mb-1">{{ __('shipments.form.quantity') }}</label>
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $qty }}" min="1" required class="nex-input bg-white font-mono" />
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-semibold text-[#7D626B] mb-1">{{ __('shipments.form.weight_kg') }}</label>
                                <input type="number" step="0.01" name="items[{{ $index }}][weight_kg]" value="{{ $wt }}" min="0.01" required class="nex-input bg-white font-mono" />
                            </div>
                            <div class="sm:col-span-1 text-right">
                                <button type="button" onclick="removeItemRow(this)" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full transition cursor-pointer" title="{{ __('shipments.form.remove_item') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right 4 Cols: Sticky Operational Status, Delay & Save Card -->
        <aside class="lg:col-span-4 lg:sticky lg:top-20 space-y-4">
            <div class="nex-card p-6 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#9E224B]">{{ __('shipments.form.ops_status_title') }}</h2>

                <div>
                    <label for="current_status" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.current_lifecycle_status') }}</label>
                    <select id="current_status" name="current_status" required class="nex-input">
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('current_status', $shipment->current_status->value) === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('current_status') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="delay_status" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.delay_classification') }}</label>
                    <select id="delay_status" name="delay_status" required class="nex-input">
                        @foreach($delayStatuses as $dst)
                            <option value="{{ $dst->value }}" {{ old('delay_status', $shipment->delay_status->value) === $dst->value ? 'selected' : '' }}>
                                {{ $dst->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('delay_status') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="delay_hours" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.cumulative_delay') }}</label>
                    <input type="number" min="0" id="delay_hours" name="delay_hours" value="{{ old('delay_hours', $shipment->delay_hours ?? 0) }}" class="nex-input font-mono" />
                    @error('delay_hours') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="pt-3 border-t border-[#F5F0F3] space-y-2">
                    <button type="submit" class="w-full py-3 px-6 rounded-full bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                        {{ __('shipments.form.save_changes') }}
                    </button>
                    <a href="{{ route('shipments.show', $shipment) }}" class="block w-full py-2.5 px-5 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-[#6E545D] text-center transition">
                        {{ __('common.actions.cancel') }}
                    </a>
                </div>
            </div>
        </aside>
    </form>
</div>

<script>
    let itemIndex = {{ count(old('items', $shipment->items)) }};

    function addItemRow() {
        const container = document.getElementById('items-container');
        const div = document.createElement('div');
        div.className = 'item-row grid grid-cols-1 sm:grid-cols-12 gap-3 p-3.5 bg-[#FAF7F9] rounded-2xl border border-[#ECE4E8] items-end';
        div.innerHTML = `
            <div class="sm:col-span-6">
                <label class="block text-[11px] font-semibold text-[#7D626B] mb-1">{{ __('shipments.form.item_description') }}</label>
                <input type="text" name="items[${itemIndex}][description]" required placeholder="{{ __('shipments.form.item_placeholder') }}" class="nex-input bg-white" />
            </div>
            <div class="sm:col-span-2">
                <label class="block text-[11px] font-semibold text-[#7D626B] mb-1">{{ __('shipments.form.quantity') }}</label>
                <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" required class="nex-input bg-white font-mono" />
            </div>
            <div class="sm:col-span-3">
                <label class="block text-[11px] font-semibold text-[#7D626B] mb-1">{{ __('shipments.form.weight_kg') }}</label>
                <input type="number" step="0.01" name="items[${itemIndex}][weight_kg]" value="100.00" min="0.01" required class="nex-input bg-white font-mono" />
            </div>
            <div class="sm:col-span-1 text-right">
                <button type="button" onclick="removeItemRow(this)" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full transition cursor-pointer" title="{{ __('shipments.form.remove_item') }}">
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
