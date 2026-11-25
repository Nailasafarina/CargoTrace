@extends('layouts.app')

@section('title', __('carriers.title'))

@section('content')
@php
    $carrierItems = $carriers->getCollection();
    $activeCarriersCount = $carrierItems->where('is_active', true)->count();
    $assignedShipmentsSum = $carrierItems->sum('shipments_count');
@endphp

<div class="space-y-5">
    <!-- 1. Page Header Card (1 Balanced Row: Title + Badge + Subtitle on Left, Primary CTA on Right) -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#1A1718]">
                    {{ __('carriers.title') }}
                </h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-[#FCECF1] text-[#BE2B5B] text-xs font-bold font-mono whitespace-nowrap">
                    {{ $carriers->total() }} {{ __('common.navigation.carriers') }}
                </span>
            </div>
            <p class="text-xs sm:text-sm text-[#6B6467] mt-1.5 leading-relaxed">
                {{ __('carriers.subtitle') }}
            </p>
        </div>

        @can('create', App\Models\Carrier::class)
            <div class="shrink-0">
                <a href="{{ route('carriers.create') }}" class="h-10 px-4 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold inline-flex items-center justify-center gap-1.5 whitespace-nowrap shadow-xs transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ __('carriers.add_carrier') }}</span>
                </a>
            </div>
        @endcan
    </div>

    <!-- 2. Standalone 3 KPI Summary Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-[#EFE6EA] p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6467] font-medium block">{{ __('carriers.title') }}</span>
                <span class="text-2xl font-extrabold text-[#1A1718] tabular-nums mt-1 block">{{ $carriers->total() }}</span>
            </div>
            <span class="w-10 h-10 rounded-xl bg-[#FAF7F8] border border-[#EFE6EA] text-[#1A1718] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1"/>
                </svg>
            </span>
        </div>

        <div class="bg-white rounded-2xl border border-[#EFE6EA] p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6467] font-medium block">{{ __('carriers.status.active') }}</span>
                <span class="text-2xl font-extrabold text-emerald-700 tabular-nums mt-1 block">{{ $activeCarriersCount }}</span>
            </div>
            <span class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </span>
        </div>

        <div class="bg-white rounded-2xl border border-[#EFE6EA] p-5 shadow-sm flex items-center justify-between">
            <div>
                <span class="text-xs text-[#6B6467] font-medium block">{{ __('carriers.table.active_shipments') }}</span>
                <span class="text-2xl font-extrabold text-[#BE2B5B] tabular-nums mt-1 block">{{ $assignedShipmentsSum }}</span>
            </div>
            <span class="w-10 h-10 rounded-xl bg-[#FCECF1] border border-[#EFE6EA] text-[#BE2B5B] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </span>
        </div>
    </div>

    <!-- 3. Data Table Card with Top Search Toolbar -->
    <div class="bg-white rounded-2xl border border-[#EFE6EA] shadow-sm overflow-hidden">
        <!-- Top Search Toolbar Inside Table Card -->
        <div class="px-5 py-4 border-b border-[#EFE6EA] bg-white">
            <form method="GET" action="{{ route('carriers.index') }}" class="flex flex-wrap sm:flex-nowrap items-center gap-3">
                <div class="w-full sm:max-w-md flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('carriers.search_placeholder') }}"
                        class="w-full h-10 rounded-xl bg-white border border-[#E5DCE0] px-3.5 text-xs text-[#1A1718] placeholder-[#6B6467]/60 focus:border-[#BE2B5B] focus:ring-2 focus:ring-[#BE2B5B]/20 focus:outline-none transition"
                    />
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="h-10 px-5 rounded-xl bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold inline-flex items-center justify-center gap-1.5 whitespace-nowrap shadow-xs transition cursor-pointer">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>{{ __('common.actions.filter') }}</span>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('carriers.index') }}" class="h-10 px-4 rounded-xl bg-white hover:bg-[#FAF7F8] border border-[#E5DCE0] text-[#1A1718] text-xs font-semibold inline-flex items-center justify-center whitespace-nowrap transition">
                            {{ __('common.actions.clear_filters') }}
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Responsive Table Container -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1A1718]">
                <thead class="bg-[#FAF7F8] text-[#6B6467] uppercase tracking-wider text-xs font-semibold border-b border-[#EFE6EA]">
                    <tr>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('carriers.table.code') }}</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('carriers.table.carrier_name') }}</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">{{ __('carriers.table.contact_info') }}</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">{{ __('carriers.table.active_shipments') }}</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap">{{ __('carriers.table.status') }}</th>
                        <th class="py-3.5 px-4 text-right whitespace-nowrap">{{ __('carriers.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EFE6EA]">
                    @forelse($carriers as $carrier)
                        <tr class="hover:bg-[#FAF7F8]/60 transition">
                            <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                                <span class="inline-block whitespace-nowrap px-2.5 py-1 rounded-lg font-mono text-[11px] font-bold bg-[#FCECF1] text-[#BE2B5B] border border-[#EFE6EA]">
                                    {{ $carrier->code }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 align-middle font-bold text-[#1A1718] whitespace-nowrap">
                                <a href="{{ route('carriers.show', $carrier) }}" class="hover:text-[#BE2B5B] transition">
                                    {{ $carrier->name }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                                <div class="text-[#1A1718] font-medium">{{ $carrier->contact_email ?? '—' }}</div>
                                <div class="text-[11px] text-[#6B6467] mt-0.5">{{ $carrier->contact_phone ?? '—' }}</div>
                            </td>
                            <td class="py-3.5 px-4 align-middle text-center whitespace-nowrap">
                                <span class="inline-block whitespace-nowrap px-2.5 py-1 rounded-full bg-[#FAF7F8] border border-[#EFE6EA] font-bold text-[#1A1718] tabular-nums">
                                    {{ $carrier->shipments_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 align-middle text-center whitespace-nowrap">
                                @if($carrier->is_active)
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span>{{ __('carriers.status.active') }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap px-3 py-1 rounded-full text-xs font-medium bg-[#FAF7F8] text-[#6B6467] border border-[#EFE6EA]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#6B6467]/50 shrink-0"></span>
                                        <span>{{ __('carriers.status.inactive') }}</span>
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 align-middle text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5 whitespace-nowrap">
                                    <a href="{{ route('carriers.show', $carrier) }}" class="px-2.5 py-1.5 rounded-lg bg-[#FAF7F8] hover:bg-[#EFE6EA] border border-[#EFE6EA] text-xs text-[#1A1718] font-semibold transition">
                                        {{ __('common.actions.view') }}
                                    </a>
                                    @can('update', $carrier)
                                        <a href="{{ route('carriers.edit', $carrier) }}" class="px-2.5 py-1.5 rounded-lg bg-[#FCECF1] hover:bg-[#BE2B5B] text-xs text-[#BE2B5B] hover:text-white border border-[#EFE6EA] font-semibold transition">
                                            {{ __('common.actions.edit') }}
                                        </a>
                                    @endcan
                                    @can('delete', $carrier)
                                        <form method="POST" action="{{ route('carriers.destroy', $carrier) }}" class="inline-block" onsubmit="return confirm('{{ __('carriers.delete_confirm', ['name' => $carrier->name]) }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-xs text-rose-700 border border-rose-200 font-semibold transition cursor-pointer">
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
                                {{ __('carriers.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($carriers->hasPages())
            <div class="px-5 py-4 border-t border-[#EFE6EA] bg-[#FAF7F8]/40">
                {{ $carriers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
