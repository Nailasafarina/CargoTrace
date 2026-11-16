@extends('layouts.app')

@section('title', 'Carrier Details - ' . $carrier->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-800 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-white">{{ $carrier->name }}</h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase bg-sky-500/10 text-sky-400 border border-sky-500/30">
                    {{ $carrier->code }}
                </span>
                @if($carrier->is_active)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Active</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-400 border border-slate-700">Inactive</span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-1">Carrier profile, contact details, and connected shipments.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('carriers.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 transition">&larr; Back</a>
            @can('update', $carrier)
                <a href="{{ route('carriers.edit', $carrier) }}" class="px-3 py-1.5 rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-medium transition">Edit</a>
            @endcan
        </div>
    </div>

    <!-- Contact & Stats Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-slate-400">Total Assigned Shipments</span>
            <div class="text-2xl font-bold text-white mt-1">{{ $carrier->shipments_count }}</div>
        </div>
        <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-slate-400">Dispatch Email</span>
            <div class="text-sm font-medium text-slate-200 mt-1 break-all">{{ $carrier->contact_email ?? 'Not Provided' }}</div>
        </div>
        <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800">
            <span class="text-xs text-slate-400">Emergency Phone</span>
            <div class="text-sm font-medium text-slate-200 mt-1">{{ $carrier->contact_phone ?? 'Not Provided' }}</div>
        </div>
    </div>

    <!-- Recent Shipments -->
    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800">
        <h2 class="text-base font-semibold text-white mb-4">Recent Shipments Carried</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-2">Tracking #</th>
                        <th class="px-4 py-2">Route Corridor</th>
                        <th class="px-4 py-2">Customer</th>
                        <th class="px-4 py-2 text-center">Status</th>
                        <th class="px-4 py-2 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($recentShipments as $shipment)
                        <tr class="hover:bg-slate-800/40">
                            <td class="px-4 py-2.5 font-mono text-sky-400 font-semibold">{{ $shipment->shipment_number }}</td>
                            <td class="px-4 py-2.5">{{ $shipment->originLocation->city }} &rarr; {{ $shipment->destinationLocation->city }}</td>
                            <td class="px-4 py-2.5">{{ $shipment->customer->name }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300">
                                    {{ $shipment->current_status->value }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <a href="{{ route('shipments.show', $shipment) }}" class="text-sky-400 hover:text-sky-300">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-slate-500">No shipments found for this carrier.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
