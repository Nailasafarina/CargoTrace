@extends('layouts.app')

@section('title', __('shipments.new_shipment'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="nex-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-full bg-[#FDF2F6] text-[#C93565] border border-[#F8C9DC] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            </span>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-[#4A2C35]">{{ __('shipments.new_shipment') }}</h1>
                <p class="text-xs text-[#8C737B] mt-0.5">{{ __('shipments.subtitle') }}</p>
            </div>
        </div>
        <a href="{{ route('shipments.index') }}" class="px-4 py-2 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-[#6E545D] transition self-start sm:self-auto">
            &larr; {{ __('common.actions.back') }}
        </a>
    </div>

    <form method="POST" action="{{ route('shipments.store') }}" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf

        <!-- Left 8 Cols: Booking, Freight Specs & Dynamic Manifest -->
        <div class="lg:col-span-8 space-y-6">
            <!-- Card 1: Core Routing & Master Data -->
            <div class="nex-card p-6 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#C93565]">{{ __('shipments.form.booking_info') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="shipment_number" class="block text-xs font-semibold text-[#6E545D] mb-1.5 truncate" title="{{ __('shipments.form.tracking_number') }} {{ __('shipments.form.auto_generated') }}">
                            {{ __('shipments.form.tracking_number') }}
                        </label>
                        <input type="text" id="shipment_number" name="shipment_number" value="{{ old('shipment_number') }}" placeholder="e.g. CT-SEA-EUR-2026-99001" class="nex-input font-mono uppercase" />
                        @error('shipment_number') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="customer_id" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.customer_account') }}</label>
                        <select id="customer_id" name="customer_id" required class="nex-input">
                            <option value="">{{ __('shipments.form.select_customer') }}</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}" {{ old('customer_id') == $cust->id ? 'selected' : '' }}>
                                    {{ $cust->name }} ({{ $cust->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="carrier_id" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.carrier') }}</label>
                        <select id="carrier_id" name="carrier_id" required class="nex-input">
                            <option value="">{{ __('shipments.form.select_carrier') }}</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}" {{ old('carrier_id') == $carrier->id ? 'selected' : '' }}>
                                    [{{ $carrier->code }}] {{ $carrier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('carrier_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="origin_location_id" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.origin_port') }}</label>
                        <select id="origin_location_id" name="origin_location_id" required class="nex-input">
                            <option value="">{{ __('shipments.form.select_origin') }}</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('origin_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('origin_location_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="destination_location_id" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.destination_port') }}</label>
                        <select id="destination_location_id" name="destination_location_id" required class="nex-input">
                            <option value="">{{ __('shipments.form.select_destination') }}</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('destination_location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }} ({{ $loc->city }}, {{ $loc->country }})
                                </option>
                            @endforeach
                        </select>
                        @error('destination_location_id') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div>
                        <label for="expected_departure_at" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.exp_departure') }}</label>
                        <input type="datetime-local" id="expected_departure_at" name="expected_departure_at" value="{{ old('expected_departure_at', now()->addDays(2)->format('Y-m-d\TH:i')) }}" required class="nex-input" />
                        @error('expected_departure_at') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="expected_arrival_at" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.exp_arrival') }}</label>
                        <input type="datetime-local" id="expected_arrival_at" name="expected_arrival_at" value="{{ old('expected_arrival_at', now()->addDays(22)->format('Y-m-d\TH:i')) }}" required class="nex-input" />
                        @error('expected_arrival_at') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Card 2: Cargo Specifications -->
            <div class="nex-card p-6 space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-wider text-[#C93565]">{{ __('shipments.form.freight_details') }}</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="cargo_type" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.cargo_type') }}</label>
                        <input type="text" id="cargo_type" name="cargo_type" value="{{ old('cargo_type') }}" required placeholder="e.g. Specialty Green Coffee (FCL)" class="nex-input" />
                        @error('cargo_type') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="volume_cbm" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.volume_cbm') }}</label>
                        <input type="number" step="0.01" id="volume_cbm" name="volume_cbm" value="{{ old('volume_cbm', '45.00') }}" required class="nex-input font-mono" />
                        @error('volume_cbm') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="current_status" class="block text-xs font-semibold text-[#6E545D] mb-1.5">{{ __('shipments.form.initial_status') }}</label>
                        <select id="current_status" name="current_status" class="nex-input">
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}" {{ old('current_status', 'BOOKED') === $st->value ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card 3: Dynamic Shipment Items -->
            <div class="nex-card p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-[#C93565]">{{ __('shipments.form.manifest_title') }}</h2>
                        <p class="text-xs text-[#8C737B] mt-0.5">{{ __('shipments.form.manifest_desc') }}</p>
                    </div>
                    <button type="button" onclick="addItemRow()" class="px-3.5 py-1.5 rounded-full bg-[#FDF2F6] hover:bg-[#FCE5EE] text-[#9E224B] border border-[#F8C9DC] text-xs font-semibold flex items-center gap-1.5 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        {{ __('shipments.form.add_item') }}
                    </button>
                </div>

                <div id="items-container" class="space-y-3">
                    <div class="item-row grid grid-cols-1 sm:grid-cols-12 gap-3 p-3.5 bg-[#FAF7F9] rounded-2xl border border-[#ECE4E8] items-end">
                        <div class="sm:col-span-6">
                            <label class="block text-[11px] font-semibold text-[#7D626B] mb-1">{{ __('shipments.form.item_description') }}</label>
                            <input type="text" name="items[0][description]" value="{{ old('items.0.description', 'Industrial Microcontroller Trays') }}" required placeholder="Description of packaged cargo" class="nex-input bg-white" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-semibold text-[#7D626B] mb-1">{{ __('shipments.form.quantity') }}</label>
                            <input type="number" name="items[0][quantity]" value="{{ old('items.0.quantity', 150) }}" min="1" required class="nex-input bg-white font-mono" />
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-[11px] font-semibold text-[#7D626B] mb-1">{{ __('shipments.form.weight_kg') }}</label>
                            <input type="number" step="0.01" name="items[0][weight_kg]" value="{{ old('items.0.weight_kg', 6500.00) }}" min="0.01" required class="nex-input bg-white font-mono" />
                        </div>
                        <div class="sm:col-span-1 text-right">
                            <button type="button" onclick="removeItemRow(this)" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-full transition cursor-pointer" title="{{ __('shipments.form.remove_item') }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right 4 Cols: Sticky Dispatch Summary & Submit Card -->
        <aside class="lg:col-span-4 lg:sticky lg:top-20 space-y-4">
            <div class="nex-card p-6 space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <h2 class="text-sm font-bold text-[#4A2C35]">{{ __('shipments.form.booking_info') }}</h2>
                    <img src="{{ asset('images/fleet-truck-mini.png') }}" alt="Dispatch preview" class="w-20 h-10 object-contain shrink-0 select-none" />
                </div>

                <div class="h-2.5 rounded-full route-track-hatch overflow-hidden">
                    <div class="h-full bg-[#E04879] rounded-full w-1/3"></div>
                </div>

                <div class="nex-inset p-4 space-y-2 text-xs text-[#7D626B]">
                    <p>{{ __('shipments.form.manifest_desc') }}</p>
                </div>

                <div class="space-y-2 pt-2">
                    <button type="submit" class="w-full py-3 px-6 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white text-xs font-semibold shadow-sm shadow-[#C93565]/20 transition cursor-pointer">
                        {{ __('shipments.form.confirm_create') }}
                    </button>
                    <a href="{{ route('shipments.index') }}" class="block w-full py-2.5 px-5 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-[#6E545D] text-center transition">
                        {{ __('common.actions.cancel') }}
                    </a>
                </div>
            </div>
        </aside>
    </form>
</div>

<script>
    let itemIndex = 1;

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
