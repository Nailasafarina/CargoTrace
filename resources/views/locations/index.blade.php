@extends('layouts.app')

@section('title', __('locations.title'))

@section('content')
@php
    $activeType = request('type');
@endphp

<div class="space-y-5">
    <!-- 1. Page Header Card (1 Balanced Row: Title + Badge + Subtitle on Left, Primary CTA on Right) -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#1A1718]">
                    {{ __('locations.title') }}
                </h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FCECF1] text-[#BE2B5B] text-xs font-bold font-mono whitespace-nowrap">
                    {{ $locations->total() }} {{ __('common.navigation.locations') }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-[#6B6467] mt-1.5 leading-relaxed">
                {{ __('locations.subtitle') }}
            </p>
        </div>

        @can('create', App\Models\Location::class)
            <div class="shrink-0">
                <a href="{{ route('locations.create') }}" class="h-10 px-4 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold inline-flex items-center justify-center gap-1.5 whitespace-nowrap shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('locations.add_location') }}</span>
                </a>
            </div>
        @endcan
    </div>

    <!-- 2. Filter Card (Top Facility Type Tabs + Bottom Search & Dropdown Row) -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] shadow-sm overflow-hidden">
        <!-- Top Quick Facility Type Tabs -->
        <div class="px-5 py-3.5 border-b border-[#EFE6EA] flex flex-wrap items-center gap-2 bg-white">
            <a href="{{ route('locations.index') }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ !$activeType ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                {{ __('locations.all_types') }}
            </a>
            @foreach($types as $type)
                <a href="{{ route('locations.index', ['type' => $type->value]) }}" class="whitespace-nowrap px-3.5 py-1.5 rounded-lg text-xs font-semibold transition {{ $activeType === $type->value ? 'bg-[#BE2B5B] text-white shadow-2xs' : 'bg-[#FAF7F8] text-[#6B6467] hover:bg-[#FCECF1] hover:text-[#BE2B5B] border border-[#EFE6EA]' }}">
                    {{ $type->label() }}
                </a>
            @endforeach
        </div>

        <!-- Bottom Search Input + Type Select + Filter & Reset Buttons in 1 Tidy Row -->
        <form method="GET" action="{{ route('locations.index') }}" class="p-5 flex flex-wrap md:flex-nowrap items-center gap-3">
            <div class="w-full md:flex-1">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('locations.search_placeholder') }}"
                    class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3.5 text-xs text-[#1A1718] placeholder-[#6B6467]/60 focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition"
                />
            </div>

            <div class="w-full sm:w-56 md:w-52 shrink-0">
                <select name="type" class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3 text-xs text-[#1A1718] focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition">
                    <option value="">{{ __('locations.all_types') }}</option>
                    @foreach($types as $type)
                        <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 shrink-0 ml-auto md:ml-0">
                <button type="submit" class="h-10 px-5 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold inline-flex items-center justify-center gap-1.5 whitespace-nowrap shadow-xs transition cursor-pointer">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <span>{{ __('common.actions.filter') }}</span>
                </button>
                <a href="{{ route('locations.index') }}" class="h-10 px-4 rounded-xl bg-white hover:bg-[#FAF7F8] border border-[#E5DCE0] text-[#1A1718] text-xs font-semibold inline-flex items-center justify-center whitespace-nowrap transition">
                    {{ __('common.actions.clear_filters') }}
                </a>
            </div>
        </form>
    </div>

    <!-- 3. Global Network Summary Map Card (100% Preserved Leaflet Component with Spacious 460px Height) -->
    @if(!empty($mapMarkers))
        <x-map
            id="locations-summary-network-map"
            mode="multi"
            :markers="$mapMarkers"
            height="460px"
            :title="__('locations.map.summary_title')"
            :subtitle="__('locations.map.summary_subtitle')"
            class="bg-white rounded-2xl border border-[#EFE6EA] shadow-sm"
        />
    @endif

    <!-- 4. Locations Data Table Card -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1A1718]">
                <thead class="bg-[#FAF7F8] text-[#6B6467] uppercase tracking-wider text-xs font-semibold border-b border-[#EFE6EA]">
                    <tr>
                        <th class="py-3.5 px-5 whitespace-nowrap">{{ __('locations.table.hub_name') }}</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('locations.table.type') }}</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('locations.table.city_country') }}</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('locations.table.coordinates') }}</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">{{ __('locations.table.shipments_ratio') }}</th>
                        <th class="py-3.5 px-5 text-right whitespace-nowrap">{{ __('locations.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFE6EA]">
                    @forelse($locations as $loc)
                        <tr class="hover:bg-[#FAF7F8]/60 transition">
                            <td class="py-3.5 px-5 align-middle font-bold text-[#1A1718] whitespace-nowrap">
                                <a href="{{ route('locations.show', $loc) }}" class="hover:text-[#BE2B5B] transition">
                                    {{ $loc->name }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                                @php
                                    $typeStyles = [
                                        'port' => 'bg-[#FCECF1] text-[#BE2B5B] border-[#EFE6EA]',
                                        'airport' => 'bg-[#FAF7F8] text-[#1A1718] border-[#EFE6EA]',
                                        'warehouse' => 'bg-amber-50 text-amber-800 border-amber-200',
                                        'city' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    ];
                                    $style = $typeStyles[$loc->type->value] ?? 'bg-[#FAF7F8] text-[#6B6467] border-[#EFE6EA]';
                                @endphp
                                <span class="inline-flex items-center whitespace-nowrap px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $style }}">
                                    {{ $loc->type->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                                <span class="text-[#1A1718] font-semibold">{{ $loc->city }}</span>,
                                <span class="text-[#6B6467]">{{ $loc->country }}</span>
                            </td>
                            <td class="py-3.5 px-4 align-middle font-mono text-[11px] text-[#6B6467] tabular-nums whitespace-nowrap">
                                {{ number_format($loc->latitude, 6) }}, {{ number_format($loc->longitude, 6) }}
                            </td>
                            <td class="py-3.5 px-4 align-middle text-center tabular-nums whitespace-nowrap">
                                <span class="text-[#BE2B5B] font-bold">{{ $loc->origin_shipments_count }}</span>
                                <span class="text-[#6B6467]/40 mx-0.5">/</span>
                                <span class="text-emerald-700 font-bold">{{ $loc->destination_shipments_count }}</span>
                            </td>
                            <td class="py-3.5 px-5 align-middle text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5 whitespace-nowrap">
                                    <a href="{{ route('locations.show', $loc) }}" class="px-3 py-1.5 rounded-lg bg-[#FAF7F8] hover:bg-[#EFE6EA] border border-[#EFE6EA] text-xs text-[#1A1718] font-semibold whitespace-nowrap transition">
                                        {{ __('common.actions.view') }}
                                    </a>
                                    @can('update', $loc)
                                        <a href="{{ route('locations.edit', $loc) }}" class="px-3 py-1.5 rounded-lg bg-[#FCECF1] hover:bg-[#BE2B5B] text-xs text-[#BE2B5B] hover:text-white border border-[#EFE6EA] font-semibold whitespace-nowrap transition">
                                            {{ __('common.actions.edit') }}
                                        </a>
                                    @endcan
                                    @can('delete', $loc)
                                        <form method="POST" action="{{ route('locations.destroy', $loc) }}" class="inline-block" onsubmit="return confirm('{{ __('locations.delete_confirm', ['name' => $loc->name]) }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-xs text-rose-700 border border-rose-200 font-semibold whitespace-nowrap transition cursor-pointer">
                                                {{ __('common.actions.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-[#6B6467]">
                                {{ __('locations.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
            <div class="px-5 py-4 border-t border-[#EFE6EA] bg-[#FAF7F8]/40">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
