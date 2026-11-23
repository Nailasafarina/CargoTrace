@extends('layouts.app')

@section('title', __('carriers.title'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">{{ __('carriers.title') }}</h1>
            <p class="text-sm text-slate-500 mt-1">{{ __('carriers.subtitle') }}</p>
        </div>
        @can('create', App\Models\Carrier::class)
            <a href="{{ route('carriers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                {{ __('carriers.add_carrier') }}
            </a>
        @endcan
    </div>

    <!-- Search Bar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="{{ route('carriers.index') }}" class="w-full sm:w-80 flex gap-2">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="{{ __('carriers.search_placeholder') }}"
                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
            />
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition cursor-pointer">
                {{ __('common.actions.filter') }}
            </button>
            @if(request('search'))
                <a href="{{ route('carriers.index') }}" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs text-slate-600 font-semibold transition">{{ __('common.actions.clear_filters') }}</a>
            @endif
        </form>
        <div class="text-xs text-slate-500">
            {!! __('common.labels.showing_count', ['count' => '<span class="font-semibold text-slate-900">' . $carriers->total() . '</span>', 'entity' => __('common.navigation.carriers')]) !!}
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[11px] font-semibold border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">{{ __('carriers.table.code') }}</th>
                    <th class="px-5 py-3.5">{{ __('carriers.table.carrier_name') }}</th>
                    <th class="px-5 py-3.5">{{ __('carriers.table.contact_info') }}</th>
                    <th class="px-5 py-3.5 text-center">{{ __('carriers.table.active_shipments') }}</th>
                    <th class="px-5 py-3.5 text-center">{{ __('carriers.table.status') }}</th>
                    <th class="px-5 py-3.5 text-right">{{ __('carriers.table.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($carriers as $carrier)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-5 py-3.5 font-mono font-bold text-blue-600">
                            {{ $carrier->code }}
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-900">
                            <a href="{{ route('carriers.show', $carrier) }}" class="hover:text-blue-600 transition">
                                {{ $carrier->name }}
                            </a>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            <div>{{ $carrier->contact_email ?? '—' }}</div>
                            <div class="text-[11px] text-slate-400">{{ $carrier->contact_phone ?? '—' }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-center font-semibold text-slate-800">
                            {{ $carrier->shipments_count }}
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            @if($carrier->is_active)
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ __('carriers.status.active') }}</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">{{ __('carriers.status.inactive') }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('carriers.show', $carrier) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                                {{ __('common.actions.view') }}
                            </a>
                            @can('update', $carrier)
                                <a href="{{ route('carriers.edit', $carrier) }}" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold transition">
                                    {{ __('common.actions.edit') }}
                                </a>
                            @endcan
                            @can('delete', $carrier)
                                <form method="POST" action="{{ route('carriers.destroy', $carrier) }}" class="inline-block" onsubmit="return confirm('{{ __('carriers.delete_confirm', ['name' => $carrier->name]) }}');">
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
                            {{ __('carriers.empty') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        {{ $carriers->links() }}
    </div>
</div>
@endsection
