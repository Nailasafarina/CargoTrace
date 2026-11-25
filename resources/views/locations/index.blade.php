@extends('layouts.app')

@section('title', __('locations.title'))

@section('content')
<div class="space-y-4">
    <!-- Top Quick Type Mode Strip -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2">
            @php $activeType = request('type'); @endphp
            <a href="{{ route('locations.index') }}" class="w-9 h-9 rounded-full flex items-center justify-center shadow-[0_2px_10px_rgba(31,23,27,0.03)] transition hover:scale-105 {{ !$activeType ? 'bg-[#C93565] text-white shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6]' }}" title="{{ __('locations.all_types') }}" aria-label="{{ __('locations.all_types') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </a>
            @foreach($types as $type)
                <a href="{{ route('locations.index', ['type' => $type->value]) }}" class="px-4 py-2 rounded-full text-xs font-semibold transition {{ $activeType === $type->value ? 'bg-[#C93565] text-white shadow-sm shadow-[#C93565]/25' : 'bg-white text-zinc-700 hover:bg-[#FDF2F6] shadow-[0_2px_10px_rgba(31,23,27,0.03)]' }}">
                    {{ $type->label() }}
                </a>
            @endforeach
        </div>

        @can('create', App\Models\Location::class)
            <a href="{{ route('locations.create') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white text-xs font-semibold shadow-sm shadow-[#C93565]/20 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                {{ __('locations.add_location') }}
            </a>
        @endcan
    </div>

    <!-- NexCargo Asymmetric 12-Col Hub & Network Stage -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
        <!-- Left Sticky Hub Control Rail -->
        <aside class="xl:col-span-4 xl:sticky xl:top-20 space-y-3">
            <div class="nex-card p-5 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-[#1F171B]">{{ __('locations.title') }}</h1>
                        <p class="text-xs text-zinc-500 mt-1">{{ __('locations.subtitle') }}</p>
                    </div>
                    <span class="w-9 h-9 rounded-full bg-[#FDF2F6] text-[#C93565] border border-[#F8C9DC] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                    </span>
                </div>
                <div class="pt-2 border-t border-[#F5F0F3] text-xs text-zinc-500">
                    {!! __('common.labels.showing_count', ['count' => '<span class="font-semibold text-[#1F171B]">' . $locations->total() . '</span>', 'entity' => __('common.navigation.locations')]) !!}
                </div>
            </div>

            <div class="nex-card p-5 space-y-3">
                <div class="text-xs font-bold text-zinc-900 pb-2 border-b border-[#F5F0F3]">{{ __('common.actions.filter') }}</div>
                <form method="GET" action="{{ route('locations.index') }}" class="space-y-3">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('locations.search_placeholder') }}"
                        class="nex-input"
                    />
                    <select name="type" class="nex-input">
                        <option value="">{{ __('locations.all_types') }}</option>
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="submit" class="py-2 px-4 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-xs font-semibold text-white transition cursor-pointer">
                            {{ __('common.actions.filter') }}
                        </button>
                        <a href="{{ route('locations.index') }}" class="py-2 px-4 rounded-full bg-[#FAF7F9] hover:bg-[#FDF2F6] border border-[#ECE4E8] text-xs text-zinc-700 font-semibold text-center transition">
                            {{ __('common.actions.clear_filters') }}
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Right Map + Locations Table Studio Stage -->
        <div class="xl:col-span-8 space-y-4">
            @if(!empty($mapMarkers))
                <x-map
                    id="locations-summary-network-map"
                    mode="multi"
                    :markers="$mapMarkers"
                    height="300px"
                    :title="__('locations.map.summary_title')"
                    :subtitle="__('locations.map.summary_subtitle')"
                />
            @endif

            <div class="nex-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-700">
                        <thead class="bg-[#FAF7F9] text-zinc-600 uppercase tracking-wider text-[11px] font-semibold border-b border-[#ECE4E8]">
                            <tr>
                                <th class="px-5 py-3.5">{{ __('locations.table.hub_name') }}</th>
                                <th class="px-5 py-3.5">{{ __('locations.table.type') }}</th>
                                <th class="px-5 py-3.5">{{ __('locations.table.city_country') }}</th>
                                <th class="px-5 py-3.5">{{ __('locations.table.coordinates') }}</th>
                                <th class="px-5 py-3.5 text-center">{{ __('locations.table.shipments_ratio') }}</th>
                                <th class="px-5 py-3.5 text-right">{{ __('locations.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5F0F3]">
                            @forelse($locations as $loc)
                                <tr class="hover:bg-[#FDF2F6]/45 transition">
                                    <td class="px-5 py-3.5 font-semibold text-zinc-900">
                                        <a href="{{ route('locations.show', $loc) }}" class="hover:text-[#C93565] transition">
                                            {{ $loc->name }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @php
                                            $typeStyles = [
                                                'port' => 'bg-[#FDF2F6] text-[#9E224B] border-[#F8C9DC]',
                                                'airport' => 'bg-[#1F171B] text-white border-[#1F171B]',
                                                'warehouse' => 'bg-[#FCE5EE] text-[#9E224B] border-[#F29EC0]',
                                                'city' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            ];
                                            $style = $typeStyles[$loc->type->value] ?? 'bg-[#FAF7F9] text-zinc-700 border-[#ECE4E8]';
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $style }}">
                                            {{ $loc->type->label() }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="text-zinc-900 font-semibold">{{ $loc->city }}</span>,
                                        <span class="text-zinc-500">{{ $loc->country }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 font-mono text-[11px] text-zinc-500 tabular-nums">
                                        {{ number_format($loc->latitude, 6) }}, {{ number_format($loc->longitude, 6) }}
                                    </td>
                                    <td class="px-5 py-3.5 text-center tabular-nums">
                                        <span class="text-[#C93565] font-bold">{{ $loc->origin_shipments_count }}</span>
                                        <span class="text-zinc-300">/</span>
                                        <span class="text-emerald-700 font-bold">{{ $loc->destination_shipments_count }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                        <a href="{{ route('locations.show', $loc) }}" class="px-3 py-1.5 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-zinc-700 font-semibold transition">
                                            {{ __('common.actions.view') }}
                                        </a>
                                        @can('update', $loc)
                                            <a href="{{ route('locations.edit', $loc) }}" class="px-3 py-1.5 rounded-full bg-[#FDF2F6] hover:bg-[#FCE5EE] text-[#9E224B] border border-[#F8C9DC] font-semibold transition">
                                                {{ __('common.actions.edit') }}
                                            </a>
                                        @endcan
                                        @can('delete', $loc)
                                            <form method="POST" action="{{ route('locations.destroy', $loc) }}" class="inline-block" onsubmit="return confirm('{{ __('locations.delete_confirm', ['name' => $loc->name]) }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 rounded-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-semibold transition cursor-pointer">
                                                    {{ __('common.actions.delete') }}
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-8 text-center text-zinc-500">
                                        {{ __('locations.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $locations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
