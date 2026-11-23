@extends('layouts.app')

@section('title', __('locations.title'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">{{ __('locations.title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('locations.subtitle') }}</p>
        </div>
        @can('create', App\Models\Location::class)
            <a href="{{ route('locations.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                {{ __('locations.add_location') }}
            </a>
        @endcan
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('locations.index') }}" class="w-full sm:w-auto flex flex-wrap gap-2">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="{{ __('locations.search_placeholder') }}"
                class="px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs w-full sm:w-60"
            />
            <select name="type" class="px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                <option value="">{{ __('locations.all_types') }}</option>
                @foreach($types as $type)
                    <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition cursor-pointer">
                {{ __('common.actions.filter') }}
            </button>
            @if(request('search') || request('type'))
                <a href="{{ route('locations.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs text-slate-600 font-semibold transition">{{ __('common.actions.clear_filters') }}</a>
            @endif
        </form>
        <div class="text-xs text-slate-500">
            {!! __('common.labels.showing_count', ['count' => '<span class="font-semibold text-slate-900">' . $locations->total() . '</span>', 'entity' => __('common.navigation.locations')]) !!}
        </div>
    </div>

    <!-- Summary Global Network Map (Bonus B) -->
    @if(!empty($mapMarkers))
        <x-map
            id="locations-summary-network-map"
            mode="multi"
            :markers="$mapMarkers"
            height="340px"
            :title="__('locations.map.summary_title')"
            :subtitle="__('locations.map.summary_subtitle')"
        />
    @endif

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[11px] font-semibold border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">{{ __('locations.table.hub_name') }}</th>
                    <th class="px-5 py-3.5">{{ __('locations.table.type') }}</th>
                    <th class="px-5 py-3.5">{{ __('locations.table.city_country') }}</th>
                    <th class="px-5 py-3.5">{{ __('locations.table.coordinates') }}</th>
                    <th class="px-5 py-3.5 text-center">{{ __('locations.table.shipments_ratio') }}</th>
                    <th class="px-5 py-3.5 text-right">{{ __('locations.table.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($locations as $loc)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-5 py-3.5 font-semibold text-slate-900">
                            <a href="{{ route('locations.show', $loc) }}" class="hover:text-blue-600 transition">
                                {{ $loc->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                                $typeStyles = [
                                    'port' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'airport' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'warehouse' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'city' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                ];
                                $style = $typeStyles[$loc->type->value] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            @endphp
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $style }}">
                                {{ $loc->type->label() }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-slate-900 font-semibold">{{ $loc->city }}</span>,
                            <span class="text-slate-500">{{ $loc->country }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-[11px] text-slate-500">
                            {{ number_format($loc->latitude, 6) }}, {{ number_format($loc->longitude, 6) }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="text-blue-600 font-bold">{{ $loc->origin_shipments_count }}</span>
                            <span class="text-slate-300">/</span>
                            <span class="text-emerald-700 font-bold">{{ $loc->destination_shipments_count }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('locations.show', $loc) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                                {{ __('common.actions.view') }}
                            </a>
                            @can('update', $loc)
                                <a href="{{ route('locations.edit', $loc) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold transition">
                                    {{ __('common.actions.edit') }}
                                </a>
                            @endcan
                            @can('delete', $loc)
                                <form method="POST" action="{{ route('locations.destroy', $loc) }}" class="inline-block" onsubmit="return confirm('{{ __('locations.delete_confirm', ['name' => $loc->name]) }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold transition cursor-pointer">
                                        {{ __('common.actions.delete') }}
                                    </button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                            {{ __('locations.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $locations->links() }}
    </div>
</div>
@endsection
