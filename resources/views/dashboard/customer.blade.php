@extends('layouts.app')

@section('title', __('dashboard.customer.title'))

@section('content')
<div class="space-y-8">
    <!-- Visual Freight & Route Command Center -->
    <x-nex-workspace :metrics="$metrics" role="customer" />

    <!-- Header -->
    <div id="operational-analytics" class="nex-card p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">{{ __('dashboard.customer.title') }}</h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#1F171B] text-white">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#E04879]"></span>
                    {{ __('dashboard.customer.role_badge') }}
                </span>
            </div>
            <p class="text-xs text-zinc-500 mt-1">
                {{ __('dashboard.customer.subtitle') }}
            </p>
        </div>
        <div>
            <a href="{{ route('shipments.index') }}" class="px-4 py-2 rounded-full bg-white hover:bg-[#FDF2F6] text-zinc-800 text-xs font-semibold border border-[#ECE4E8] transition flex items-center gap-2">
                <svg class="w-4 h-4 text-[#E04879]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                {{ __('dashboard.customer.view_all_my_cargo') }}
            </a>
        </div>
    </div>

    <!-- Customer Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="nex-card p-5">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium">{{ __('dashboard.customer.my_consignments') }}</span>
                <span class="p-2 rounded-xl bg-[#FAF7F9] text-[#1F171B]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-zinc-900 tabular-nums">{{ $metrics['total_shipments'] }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">{{ __('dashboard.customer.total_booked_cargo') }}</div>
        </div>

        <div class="nex-card p-5">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium">{{ __('dashboard.customer.active_en_route') }}</span>
                <span class="p-2 rounded-xl bg-[#FDF2F6] text-[#C93565]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-[#C93565] tabular-nums">{{ $metrics['in_transit'] }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">{{ __('dashboard.customer.live_tracking') }}</div>
        </div>

        <div class="nex-card p-5">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium">{{ __('dashboard.customer.fulfilled') }}</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-700 tabular-nums">{{ $metrics['delivered'] }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">{{ __('dashboard.customer.safely_received') }}</div>
        </div>

        <div class="nex-card p-5">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium">{{ __('dashboard.customer.delayed') }}</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-700 tabular-nums">{{ $metrics['delayed'] }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">{{ __('dashboard.customer.pending_clearance') }}</div>
        </div>
    </div>

    <!-- Customer KPI Strip & Status Doughnut -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="nex-card p-6 space-y-4 flex flex-col justify-between">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-600">{{ __('dashboard.customer.sla_title') }}</h2>
                <div class="mt-4">
                    <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">{{ __('dashboard.customer.on_time_arrival_rate') }}</div>
                    <div class="text-4xl font-extrabold text-emerald-700 mt-1 tabular-nums">
                        {{ $metrics['kpis']['on_time_delivery_rate'] }}%
                    </div>
                    <div class="text-xs text-zinc-500 mt-2">
                        {{ __('dashboard.customer.sla_desc') }}
                    </div>
                </div>
            </div>
            <div class="nex-inset p-4 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-600">{{ __('dashboard.customer.open_incidents_on_cargo') }}</span>
                    <span class="font-bold {{ $metrics['open_exceptions'] > 0 ? 'text-red-700' : 'text-emerald-700' }}">
                        {{ __('dashboard.customer.active_count', ['count' => $metrics['open_exceptions']]) }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-600">{{ __('dashboard.customer.delay_rate') }}</span>
                    <span class="font-bold text-amber-700">{{ $metrics['kpis']['delay_rate'] }}%</span>
                </div>
            </div>
        </div>

        <div class="nex-card lg:col-span-2 p-6 space-y-4">
            <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">{{ __('dashboard.customer.cargo_lifecycle_breakdown') }}</h2>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="customerStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Active Consignments & Notifications Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="nex-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#E04879]"></span>
                        {{ __('dashboard.customer.active_consignments') }}
                    </h2>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('dashboard.customer.live_tracking_desc') }}</p>
                </div>
                <a href="{{ route('shipments.index') }}" class="text-xs text-[#C93565] hover:underline font-semibold">
                    {{ __('dashboard.admin.view_all') }} &rarr;
                </a>
            </div>
            <div class="space-y-3">
                @forelse($metrics['recent_shipments'] as $sh)
                    <div class="nex-inset p-4 flex items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('shipments.show', $sh) }}" class="text-xs font-mono font-bold text-zinc-900 hover:text-[#C93565] hover:underline">
                                    {{ $sh->shipment_number }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider {{ $sh->current_status->badgeClass() }}">
                                    {{ $sh->current_status->label() }}
                                </span>
                            </div>
                            <div class="text-xs text-zinc-600">
                                <span class="font-medium text-zinc-800">{{ $sh->originLocation->city }}</span> &rarr; <span class="font-medium text-zinc-800">{{ $sh->destinationLocation->city }}</span>
                                &bull; <span class="font-mono text-zinc-500">{{ $sh->carrier->name }}</span>
                            </div>
                            <div class="text-[11px] text-zinc-500 font-mono">
                                ETA: {{ $sh->estimated_arrival_at ? $sh->estimated_arrival_at->format('d M Y, H:i') : $sh->expected_arrival_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider border {{ $sh->delay_status->badgeClass() }}">
                                {{ $sh->delay_status->label() }}
                            </span>
                            <a href="{{ route('shipments.show', $sh) }}" class="mt-2 block text-xs text-[#C93565] hover:underline font-semibold">
                                {{ __('dashboard.customer.map_link') }} &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-zinc-500">
                        {{ __('dashboard.customer.no_shipments') }}
                    </div>
                @endforelse
            </div>
        </div>

        <div class="nex-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#E04879]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        {{ __('dashboard.customer.recent_notifications_title') }}
                    </h2>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('dashboard.customer.recent_notifications_desc') }}</p>
                </div>
                <a href="{{ route('notifications.index') }}" class="text-xs text-[#C93565] hover:underline font-semibold">
                    {{ __('dashboard.customer.inbox_link') }} &rarr;
                </a>
            </div>
            <div class="space-y-3">
                @forelse($metrics['recent_notifications'] as $notif)
                    <div class="p-4 rounded-2xl border {{ is_null($notif->read_at) ? 'border-[#F8C9DC] bg-[#FDF2F6]/70' : 'border-[#ECE4E8] bg-[#FAF7F9]' }} flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ is_null($notif->read_at) ? 'bg-[#FCE5EE] text-[#9E224B] border border-[#F8C9DC]' : 'bg-white border border-[#ECE4E8] text-zinc-700' }}">
                                    {{ $notif->type }}
                                </span>
                                @if(is_null($notif->read_at))
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#E04879]"></span>
                                @endif
                            </div>
                            <p class="text-xs text-zinc-700">{{ $notif->message }}</p>
                            <span class="text-[10px] text-zinc-500 font-mono block">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($notif->shipment)
                            <a href="{{ route('shipments.show', $notif->shipment) }}" class="text-xs text-[#C93565] hover:underline font-semibold shrink-0">
                                {{ __('dashboard.customer.track_link') }} &rarr;
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-zinc-500">
                        {{ __('dashboard.customer.no_notifications') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const Chart = window.Chart;
    if (!Chart) return;
    Chart.defaults.color = '#71717a';
    Chart.defaults.font.family = 'Plus Jakarta Sans, Instrument Sans, system-ui, sans-serif';

    const statusCtx = document.getElementById('customerStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($metrics['status_chart']['labels']),
            datasets: [{
                data: @json($metrics['status_chart']['data']),
                backgroundColor: ['#C8B9C1', '#1F171B', '#F29EC0', '#E04879', '#10B981'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, color: '#52525b' } }
            }
        }
    });
});
</script>
@endpush
