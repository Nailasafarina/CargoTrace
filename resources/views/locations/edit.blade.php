@extends('layouts.app')

@section('title', __('locations.edit_location') . ' - ' . $location->name)

@section('content')
<div class="space-y-6">
    <!-- Top Bar -->
    <div class="nex-card p-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#1F171B] tracking-tight">{{ __('locations.edit_location') }}: {{ $location->name }}</h1>
            <p class="text-xs text-zinc-500 mt-0.5">{{ __('locations.subtitle') }}</p>
        </div>
        <a href="{{ route('locations.index') }}" class="px-4 py-2 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-zinc-700 transition shrink-0">
            &larr; {{ __('common.actions.back') }}
        </a>
    </div>

    <form action="{{ route('locations.update', $location) }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf
        @method('PUT')

        <!-- Left 6 Cols: Hub Details & Geocoding -->
        <div class="lg:col-span-6 nex-card p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('locations.fields.name') }} *</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $location->name) }}" required class="nex-input text-sm py-2.5" />
                    @error('name') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="type" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('locations.fields.type') }} *</label>
                    <select id="type" name="type" required class="nex-input text-sm py-2.5">
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" {{ old('type', $location->type->value) === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="city" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('locations.fields.city') }} *</label>
                    <input type="text" id="city" name="city" value="{{ old('city', $location->city) }}" required class="nex-input text-sm py-2.5" />
                    @error('city') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="country" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('locations.fields.country') }} *</label>
                    <input type="text" id="country" name="country" value="{{ old('country', $location->country) }}" required class="nex-input text-sm py-2.5" />
                    @error('country') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Automatic Geocoding Toolbar -->
            <div class="nex-inset p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-xs font-semibold text-zinc-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#E04879]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ __('locations.geocoding.toolbar_title') }}
                    </span>
                    <p id="geocode-status" class="text-[11px] text-zinc-500 mt-0.5">{{ __('locations.geocoding.toolbar_desc') }}</p>
                </div>
                <button type="button" id="btn-geocode" class="px-4 py-2 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-xs font-semibold transition shrink-0 flex items-center gap-1.5 cursor-pointer shadow-xs">
                    <span id="geocode-spinner" class="hidden animate-spin">&circlearrowright;</span>
                    <span>{{ __('locations.geocoding.lookup_button') }}</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="latitude" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('locations.fields.latitude') }} (Decimal -90 to +90) *</label>
                    <input type="number" step="0.0000001" id="latitude" name="latitude" value="{{ old('latitude', $location->latitude) }}" required class="nex-input text-sm py-2.5 font-mono" />
                    @error('latitude') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="longitude" class="block text-xs font-semibold text-zinc-700 mb-1.5">{{ __('locations.fields.longitude') }} (Decimal -180 to +180) *</label>
                    <input type="number" step="0.0000001" id="longitude" name="longitude" value="{{ old('longitude', $location->longitude) }}" required class="nex-input text-sm py-2.5 font-mono" />
                    @error('longitude') <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 border-t border-[#F5F0F3] flex justify-end gap-3">
                <a href="{{ route('locations.index') }}" class="px-5 py-2.5 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-zinc-700 transition">
                    {{ __('common.actions.cancel') }}
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                    {{ __('locations.edit_location') }}
                </button>
            </div>
        </div>

        <!-- Right 6 Cols: Interactive OpenStreetMap Picker -->
        <div class="lg:col-span-6 lg:sticky lg:top-20 space-y-2">
            <x-map
                id="location-picker-map"
                mode="picker"
                :latitude="old('latitude', $location->latitude)"
                :longitude="old('longitude', $location->longitude)"
                lat-input="#latitude"
                lng-input="#longitude"
                height="420px"
                :title="__('locations.map.picker_title')"
                :subtitle="__('locations.map.picker_hint')"
            />
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnGeocode = document.getElementById('btn-geocode');
    const inputName = document.getElementById('name');
    const inputCity = document.getElementById('city');
    const inputCountry = document.getElementById('country');
    const inputLat = document.getElementById('latitude');
    const inputLng = document.getElementById('longitude');
    const statusText = document.getElementById('geocode-status');
    const spinner = document.getElementById('geocode-spinner');

    if (btnGeocode) {
        btnGeocode.addEventListener('click', async function () {
            const queryParts = [inputName.value, inputCity.value, inputCountry.value].filter(Boolean);
            if (queryParts.length === 0) {
                statusText.innerText = window.i18n?.geocoding?.min_chars || 'Please enter Name, City, or Country first.';
                statusText.className = 'text-[11px] text-amber-700 font-medium mt-0.5';
                return;
            }

            const query = queryParts.join(', ');
            statusText.innerText = (window.i18n?.geocoding?.loading || 'Resolving GPS coordinates...') + ' "' + query + '"';
            statusText.className = 'text-[11px] text-[#C93565] font-medium mt-0.5';
            spinner.classList.remove('hidden');
            btnGeocode.disabled = true;

            try {
                const response = await fetch('{{ route("locations.geocode") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ query: query })
                });

                const data = await response.json();

                if (data.success && data.latitude && data.longitude) {
                    inputLat.value = data.latitude;
                    inputLng.value = data.longitude;
                    inputLat.dispatchEvent(new Event('input', { bubbles: true }));
                    inputLng.dispatchEvent(new Event('input', { bubbles: true }));
                    statusText.innerText = (window.i18n?.geocoding?.success || 'Resolved:') + ' ' + (data.display_name || query);
                    statusText.className = 'text-[11px] text-emerald-700 font-medium mt-0.5';
                } else {
                    statusText.innerText = data.message || (window.i18n?.geocoding?.failed || 'Location not found.');
                    statusText.className = 'text-[11px] text-red-600 font-medium mt-0.5';
                }
            } catch (err) {
                statusText.innerText = window.i18n?.geocoding?.network_error || 'Geocoding request failed.';
                statusText.className = 'text-[11px] text-red-600 font-medium mt-0.5';
            } finally {
                spinner.classList.add('hidden');
                btnGeocode.disabled = false;
            }
        });
    }
});
</script>
@endsection
