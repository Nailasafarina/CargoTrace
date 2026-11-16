@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-white tracking-tight">Register New Waypoint Hub</h1>
            <p class="text-xs text-slate-400 mt-0.5">Add a port, airport, warehouse, or city waypoint on the transit corridor</p>
        </div>
        <a href="{{ route('locations.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300 transition">
            &larr; Back to Locations
        </a>
    </div>

    <form action="{{ route('locations.store') }}" method="POST" class="p-6 rounded-2xl bg-slate-900 border border-slate-800 shadow-xl space-y-5">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-xs font-medium text-slate-300 mb-1">Facility / Port Name *</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
                    placeholder="e.g. Port of Rotterdam"
                />
                @error('name') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="block text-xs font-medium text-slate-300 mb-1">Facility Type *</label>
                <select id="type" name="type" required class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                    @foreach($types as $type)
                        <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
                @error('type') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="city" class="block text-xs font-medium text-slate-300 mb-1">City / Municipality *</label>
                <input
                    type="text"
                    id="city"
                    name="city"
                    value="{{ old('city') }}"
                    required
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
                    placeholder="e.g. Rotterdam"
                />
                @error('city') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="country" class="block text-xs font-medium text-slate-300 mb-1">Country *</label>
                <input
                    type="text"
                    id="country"
                    name="country"
                    value="{{ old('country') }}"
                    required
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
                    placeholder="e.g. Netherlands"
                />
                @error('country') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Automatic Geocoding Toolbar -->
        <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-xs font-semibold text-white flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Automatic Coordinate Resolver (OpenStreetMap Nominatim)
                </span>
                <p id="geocode-status" class="text-[11px] text-slate-400 mt-0.5">Click to auto-fetch GPS latitude & longitude using location name, city, and country.</p>
            </div>
            <button
                type="button"
                id="btn-geocode"
                class="px-3.5 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold transition shrink-0 flex items-center gap-1.5 cursor-pointer"
            >
                <span id="geocode-spinner" class="hidden animate-spin">↻</span>
                <span>Auto-Geocode GPS</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="latitude" class="block text-xs font-medium text-slate-300 mb-1">Latitude (Decimal -90 to +90) *</label>
                <input
                    type="number"
                    step="0.0000001"
                    id="latitude"
                    name="latitude"
                    value="{{ old('latitude') }}"
                    required
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
                    placeholder="51.9500300"
                />
                @error('latitude') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="longitude" class="block text-xs font-medium text-slate-300 mb-1">Longitude (Decimal -180 to +180) *</label>
                <input
                    type="number"
                    step="0.0000001"
                    id="longitude"
                    name="longitude"
                    value="{{ old('longitude') }}"
                    required
                    class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 rounded-xl text-sm text-white font-mono placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-sky-500"
                    placeholder="4.1333400"
                />
                @error('longitude') <p class="text-xs text-rose-400 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
            <a href="{{ route('locations.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-medium text-slate-300">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition cursor-pointer">
                Save Location
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
                statusText.innerText = '⚠️ Please enter Name, City, or Country first.';
                statusText.className = 'text-[11px] text-amber-400 mt-0.5';
                return;
            }

            const query = queryParts.join(', ');
            statusText.innerText = 'Resolving GPS coordinates for "' + query + '"...';
            statusText.className = 'text-[11px] text-sky-400 mt-0.5';
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
                    statusText.innerText = '✅ Resolved: ' + (data.display_name || query);
                    statusText.className = 'text-[11px] text-emerald-400 mt-0.5';
                } else {
                    statusText.innerText = '❌ ' + (data.message || 'Location not found.');
                    statusText.className = 'text-[11px] text-rose-400 mt-0.5';
                }
            } catch (err) {
                statusText.innerText = '❌ Geocoding request failed.';
                statusText.className = 'text-[11px] text-rose-400 mt-0.5';
            } finally {
                spinner.classList.add('hidden');
                btnGeocode.disabled = false;
            }
        });
    }
});
</script>
@endsection
