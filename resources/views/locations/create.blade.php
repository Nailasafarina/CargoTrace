@extends('layouts.app')

@section('title', __('locations.add_location'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">{{ __('locations.add_location') }}</h1>
            <p class="text-xs text-slate-500 mt-0.5">{{ __('locations.subtitle') }}</p>
        </div>
        <a href="{{ route('locations.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
            &larr; {{ __('common.actions.back') }}
        </a>
    </div>

    <form action="{{ route('locations.store') }}" method="POST" class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('locations.fields.name') }} *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    placeholder="e.g. Port of Rotterdam"
                />
                @error('name') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('locations.fields.type') }} *</label>
                <select id="type" name="type" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                    @foreach($types as $type)
                        <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
                @error('type') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="city" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('locations.fields.city') }} *</label>
                <input
                    type="text"
                    id="city"
                    name="city"
                    value="{{ old('city') }}"
                    required
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    placeholder="e.g. Rotterdam"
                />
                @error('city') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="country" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('locations.fields.country') }} *</label>
                <input
                    type="text"
                    id="country"
                    name="country"
                    value="{{ old('country') }}"
                    required
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    placeholder="e.g. Netherlands"
                />
                @error('country') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Automatic Geocoding Toolbar -->
        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-xs font-semibold text-slate-900 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    {{ __('locations.geocoding.toolbar_title') }}
                </span>
                <p id="geocode-status" class="text-[11px] text-slate-500 mt-0.5">{{ __('locations.geocoding.toolbar_desc') }}</p>
            </div>
            <button
                type="button"
                id="btn-geocode"
                class="px-3.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition shrink-0 flex items-center gap-1.5 cursor-pointer shadow-2xs"
            >
                <span id="geocode-spinner" class="hidden animate-spin">↻</span>
                <span>{{ __('locations.geocoding.lookup_button') }}</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="latitude" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('locations.fields.latitude') }} (Decimal -90 to +90) *</label>
                <input
                    type="number"
                    step="0.0000001"
                    id="latitude"
                    name="latitude"
                    value="{{ old('latitude') }}"
                    required
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 font-mono placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    placeholder="51.9500300"
                />
                @error('latitude') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="longitude" class="block text-xs font-semibold text-slate-700 mb-1">{{ __('locations.fields.longitude') }} (Decimal -180 to +180) *</label>
                <input
                    type="number"
                    step="0.0000001"
                    id="longitude"
                    name="longitude"
                    value="{{ old('longitude') }}"
                    required
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-sm text-slate-800 font-mono placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    placeholder="4.1333400"
                />
                @error('longitude') <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Interactive Map Picker (Bonus A) -->
        <div class="space-y-1.5 pt-1">
            <label class="block text-xs font-semibold text-slate-700">
                {{ __('locations.map.picker_title') }}
            </label>
            <p class="text-[11px] text-slate-500">
                {{ __('locations.map.picker_hint') }}
            </p>
            <x-map
                id="location-picker-map"
                mode="picker"
                :latitude="old('latitude')"
                :longitude="old('longitude')"
                lat-input="#latitude"
                lng-input="#longitude"
                height="300px"
                class="p-3"
            />
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('locations.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                {{ __('common.actions.cancel') }}
            </a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm hover:shadow transition cursor-pointer">
                {{ __('locations.add_location') }}
            </button>
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
                statusText.innerText = '⚠️ ' + (window.i18n?.geocoding?.min_chars || 'Please enter Name, City, or Country first.');
                statusText.className = 'text-[11px] text-amber-700 font-medium mt-0.5';
                return;
            }

            const query = queryParts.join(', ');
            statusText.innerText = (window.i18n?.geocoding?.loading || 'Resolving GPS coordinates...') + ' "' + query + '"';
            statusText.className = 'text-[11px] text-blue-600 font-medium mt-0.5';
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
                    statusText.innerText = '✅ ' + (window.i18n?.geocoding?.success || 'Resolved:') + ' ' + (data.display_name || query);
                    statusText.className = 'text-[11px] text-emerald-700 font-medium mt-0.5';
                } else {
                    statusText.innerText = '❌ ' + (data.message || (window.i18n?.geocoding?.failed || 'Location not found.'));
                    statusText.className = 'text-[11px] text-rose-600 font-medium mt-0.5';
                }
            } catch (err) {
                statusText.innerText = '❌ ' + (window.i18n?.geocoding?.network_error || 'Geocoding request failed.');
                statusText.className = 'text-[11px] text-rose-600 font-medium mt-0.5';
            } finally {
                spinner.classList.add('hidden');
                btnGeocode.disabled = false;
            }
        });
    }
});
</script>
@endsection
