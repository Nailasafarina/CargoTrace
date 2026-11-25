@extends('layouts.app')

@section('title', __('carriers.details_title') . ' - ' . $carrier->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    <!-- Left Partner Profile Bento Card -->
    <aside class="lg:col-span-4 lg:sticky lg:top-20 space-y-4">
        <div class="nex-card p-6 space-y-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]">
                            {{ $carrier->code }}
                        </span>
                        @if($carrier->is_active)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">{{ __('carriers.status.active') }}</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-[#FAF7F9] text-zinc-600 border border-[#ECE4E8]">{{ __('carriers.status.inactive') }}</span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold tracking-tight text-[#1F171B] mt-3">{{ $carrier->name }}</h1>
                    <p class="text-xs text-zinc-500 mt-1">{{ __('carriers.profile_subtitle') }}</p>
                </div>
                <img src="{{ asset('images/fleet-truck-mini.png') }}" alt="{{ $carrier->name }}" class="w-20 h-10 object-contain shrink-0 select-none" />
            </div>

            <div class="space-y-2.5">
                <div class="nex-inset p-3.5">
                    <span class="text-[11px] text-zinc-500 block">{{ __('carriers.fields.assigned_shipments') }}</span>
                    <div class="text-2xl font-bold text-[#1F171B] mt-0.5 tabular-nums">{{ $carrier->shipments_count }}</div>
                </div>
                <div class="nex-inset p-3.5">
                    <span class="text-[11px] text-zinc-500 block">{{ __('carriers.fields.contact_email') }}</span>
                    <div class="text-xs font-semibold text-zinc-800 mt-0.5 break-all">{{ $carrier->contact_email ?? __('carriers.not_provided') }}</div>
                </div>
                <div class="nex-inset p-3.5">
                    <span class="text-[11px] text-zinc-500 block">{{ __('carriers.fields.contact_phone') }}</span>
                    <div class="text-xs font-semibold text-zinc-800 mt-0.5">{{ $carrier->contact_phone ?? __('carriers.not_provided') }}</div>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <a href="{{ route('carriers.index') }}" class="flex-1 py-2.5 px-4 rounded-full bg-[#FAF7F9] hover:bg-[#ECE4E8] border border-[#ECE4E8] text-xs font-semibold text-zinc-700 text-center transition">
                    &larr; {{ __('common.actions.back') }}
                </a>
                @can('update', $carrier)
                    <a href="{{ route('carriers.edit', $carrier) }}" class="flex-1 py-2.5 px-4 rounded-full bg-[#FDF2F6] hover:bg-[#FCE5EE] text-[#9E224B] border border-[#F8C9DC] text-xs font-semibold text-center transition">
                        {{ __('common.actions.edit') }}
                    </a>
                @endcan
            </div>
        </div>
    </aside>

    <!-- Right Recent Shipments Studio Stage -->
    <div class="lg:col-span-8 nex-card p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-[#F5F0F3]">
            <h2 class="text-base font-bold text-[#1F171B]">{{ __('carriers.recent_shipments') }}</h2>
            <span class="px-3 py-1 rounded-full bg-[#FAF7F9] border border-[#ECE4E8] text-xs font-mono text-zinc-600 tabular-nums">
                {{ $recentShipments->count() }} shown
            </span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-[#ECE4E8]">
            <table class="w-full text-left text-xs text-zinc-700">
                <thead class="bg-[#FAF7F9] text-zinc-600 uppercase text-[10px] font-semibold border-b border-[#ECE4E8]">
                    <tr>
                        <th class="px-4 py-3">{{ __('shipments.fields.shipment_number') }}</th>
                        <th class="px-4 py-3">{{ __('shipments.fields.route') }}</th>
                        <th class="px-4 py-3">{{ __('shipments.fields.customer') }}</th>
                        <th class="px-4 py-3 text-center">{{ __('shipments.fields.status') }}</th>
                        <th class="px-4 py-3 text-right">{{ __('common.actions.view') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#F5F0F3]">
                    @forelse($recentShipments as $shipment)
                        <tr class="hover:bg-[#FDF2F6]/45 transition">
                            <td class="px-4 py-3 font-mono text-[#C93565] font-bold">{{ $shipment->shipment_number }}</td>
                            <td class="px-4 py-3 font-medium text-zinc-900">{{ $shipment->originLocation->city }} &rarr; {{ $shipment->destinationLocation->city }}</td>
                            <td class="px-4 py-3 text-zinc-700">{{ $shipment->customer->name }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider {{ $shipment->current_status->badgeClass() }}">
                                    {{ $shipment->current_status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('shipments.show', $shipment) }}" class="text-[#C93565] hover:underline font-semibold">{{ __('common.actions.view') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-zinc-500">{{ __('carriers.no_shipments') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
