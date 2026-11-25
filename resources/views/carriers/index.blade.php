@extends('layouts.app')

@section('title', __('carriers.title'))

@section('content')
<div class="space-y-4">
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">
        <!-- Left Sticky Partner Control Rail -->
        <aside class="xl:col-span-4 xl:sticky xl:top-20 space-y-3">
            <!-- Fleet Partner Identity Card -->
            <div class="nex-card p-5 space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <h1 class="text-xl font-bold tracking-tight text-[#1F171B]">{{ __('carriers.title') }}</h1>
                        <p class="text-xs text-zinc-500 mt-1">{{ __('carriers.subtitle') }}</p>
                    </div>
                    <div class="w-24 h-12 shrink-0 flex items-center justify-end select-none">
                        <img src="{{ asset('images/fleet-truck-mini.png') }}" alt="Fleet carrier unit" class="w-full h-full object-contain" />
                    </div>
                </div>

                @can('create', App\Models\Carrier::class)
                    <a href="{{ route('carriers.create') }}" class="w-full py-2.5 px-4 rounded-full bg-[#C93565] hover:bg-[#b32956] text-white text-xs font-semibold shadow-sm shadow-[#C93565]/20 transition flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        {{ __('carriers.add_carrier') }}
                    </a>
                @endcan
            </div>

            <!-- Search & Directory Filter Card -->
            <div class="nex-card p-5 space-y-3">
                <div class="flex items-center justify-between pb-2.5 border-b border-[#F5F0F3]">
                    <span class="text-xs font-bold text-zinc-900">{{ __('common.actions.filter') }}</span>
                    <span class="text-xs text-zinc-500">
                        {!! __('common.labels.showing_count', ['count' => '<span class="font-semibold text-[#1F171B]">' . $carriers->total() . '</span>', 'entity' => __('common.navigation.carriers')]) !!}
                    </span>
                </div>

                <form method="GET" action="{{ route('carriers.index') }}" class="space-y-3">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="{{ __('carriers.search_placeholder') }}"
                        class="nex-input"
                    />
                    <div class="grid grid-cols-2 gap-2">
                        <button type="submit" class="py-2 px-4 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-xs font-semibold text-white transition cursor-pointer">
                            {{ __('common.actions.filter') }}
                        </button>
                        <a href="{{ route('carriers.index') }}" class="py-2 px-4 rounded-full bg-[#FAF7F9] hover:bg-[#FDF2F6] border border-[#ECE4E8] text-xs text-zinc-700 font-semibold text-center transition">
                            {{ __('common.actions.clear_filters') }}
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Right Studio Directory Stage -->
        <div class="xl:col-span-8 space-y-4">
            @php
                $carrierItems = $carriers->getCollection();
                $activeCarriersCount = $carrierItems->where('is_active', true)->count();
                $assignedShipmentsSum = $carrierItems->sum('shipments_count');
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="nex-card p-5 space-y-2">
                    <span class="text-xs text-zinc-500 font-medium">{{ __('carriers.title') }}</span>
                    <div class="text-2xl font-bold text-[#1F171B] tabular-nums">{{ $carriers->total() }}</div>
                    <div class="h-2 rounded-full route-track-hatch overflow-hidden">
                        <div class="h-full bg-[#E04879] rounded-full w-3/4"></div>
                    </div>
                </div>
                <div class="nex-card p-5 space-y-2">
                    <span class="text-xs text-zinc-500 font-medium">{{ __('carriers.status.active') }}</span>
                    <div class="text-2xl font-bold text-emerald-700 tabular-nums">{{ $activeCarriersCount }}</div>
                    <div class="text-[11px] text-zinc-400">{{ __('carriers.table.status') }}</div>
                </div>
                <div class="nex-card p-5 space-y-2">
                    <span class="text-xs text-zinc-500 font-medium">{{ __('carriers.table.active_shipments') }}</span>
                    <div class="text-2xl font-bold text-[#C93565] tabular-nums">{{ $assignedShipmentsSum }}</div>
                    <div class="text-[11px] text-zinc-400">{{ __('carriers.table.carrier_name') }}</div>
                </div>
            </div>

            <div class="nex-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-700">
                        <thead class="bg-[#FAF7F9] text-zinc-600 uppercase tracking-wider text-[11px] font-semibold border-b border-[#ECE4E8]">
                            <tr>
                                <th class="px-5 py-3.5">{{ __('carriers.table.code') }}</th>
                                <th class="px-5 py-3.5">{{ __('carriers.table.carrier_name') }}</th>
                                <th class="px-5 py-3.5">{{ __('carriers.table.contact_info') }}</th>
                                <th class="px-5 py-3.5 text-center">{{ __('carriers.table.active_shipments') }}</th>
                                <th class="px-5 py-3.5 text-center">{{ __('carriers.table.status') }}</th>
                                <th class="px-5 py-3.5 text-right">{{ __('carriers.table.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F5F0F3]">
                            @forelse($carriers as $carrier)
                                <tr class="hover:bg-[#FDF2F6]/45 transition">
                                    <td class="px-5 py-3.5">
                                        <span class="px-2.5 py-1 rounded-full font-mono text-[11px] font-bold bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]">
                                            {{ $carrier->code }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 font-semibold text-zinc-900">
                                        <a href="{{ route('carriers.show', $carrier) }}" class="hover:text-[#C93565] transition">
                                            {{ $carrier->name }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5 text-zinc-600">
                                        <div>{{ $carrier->contact_email ?? '—' }}</div>
                                        <div class="text-[11px] text-zinc-400">{{ $carrier->contact_phone ?? '—' }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full bg-[#FAF7F9] border border-[#ECE4E8] font-semibold text-zinc-800 tabular-nums">
                                            {{ $carrier->shipments_count }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        @if($carrier->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ __('carriers.status.active') }}
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-[#FAF7F9] text-zinc-600 border border-[#ECE4E8]">{{ __('carriers.status.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                                        <a href="{{ route('carriers.show', $carrier) }}" class="px-3 py-1.5 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-zinc-700 font-semibold transition">
                                            {{ __('common.actions.view') }}
                                        </a>
                                        @can('update', $carrier)
                                            <a href="{{ route('carriers.edit', $carrier) }}" class="px-3 py-1.5 rounded-full bg-[#FDF2F6] hover:bg-[#FCE5EE] text-[#9E224B] border border-[#F8C9DC] font-semibold transition">
                                                {{ __('common.actions.edit') }}
                                            </a>
                                        @endcan
                                        @can('delete', $carrier)
                                            <form method="POST" action="{{ route('carriers.destroy', $carrier) }}" class="inline-block" onsubmit="return confirm('{{ __('carriers.delete_confirm', ['name' => $carrier->name]) }}');">
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
                                        {{ __('carriers.empty') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $carriers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
