@extends('layouts.app')

@section('title', 'My Cargo Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Customer Tracking Portal</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    role: customer
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Real-time tracking, ETA forecasts, and operational notifications for your assigned container consignments.
            </p>
        </div>
        <div>
            <a href="{{ route('shipments.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold border border-slate-700 transition flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                View All My Cargo
            </a>
        </div>
    </div>

    <!-- Customer Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Total Shipments -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span>My Consignments</span>
                <span class="p-1.5 rounded-lg bg-sky-500/10 text-sky-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-white">{{ $metrics['total_shipments'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Total Booked Cargo</div>
        </div>

        <!-- In Transit -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span>Active En Route</span>
                <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-indigo-400">{{ $metrics['in_transit'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Live Maritime Tracking</div>
        </div>

        <!-- Delivered -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span>Fulfilled</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-400">{{ $metrics['delivered'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Safely Received</div>
        </div>

        <!-- Delayed -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span>Delayed</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-400">{{ $metrics['delayed'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Pending Clearance/Weather</div>
        </div>
    </div>

    <!-- Customer KPI Strip & Status Doughnut -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- KPI Card -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 flex flex-col justify-between">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Service Level Agreement (SLA)</h2>
                <div class="mt-4">
                    <div class="text-[11px] text-slate-400 uppercase tracking-wider">On-Time Arrival Rate</div>
                    <div class="text-4xl font-extrabold text-emerald-400 mt-1">
                        {{ $metrics['kpis']['on_time_delivery_rate'] }}%
                    </div>
                    <div class="text-xs text-slate-400 mt-2">
                        Cargo fulfilled within expected schedule parameters.
                    </div>
                </div>
            </div>
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400">Open Incidents on Cargo:</span>
                    <span class="font-bold {{ $metrics['open_exceptions'] > 0 ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ $metrics['open_exceptions'] }} Active
                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400">Delay Rate:</span>
                    <span class="font-bold text-amber-400">{{ $metrics['kpis']['delay_rate'] }}%</span>
                </div>
            </div>
        </div>

        <!-- Cargo Status Distribution Chart -->
        <div class="lg:col-span-2 p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">My Cargo Lifecycle Breakdown</h2>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="customerStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Active Consignments & Notifications Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Active Consignments List -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                        Active Consignments
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Live tracking and status updates</p>
                </div>
                <a href="{{ route('shipments.index') }}" class="text-xs text-sky-400 hover:underline">
                    View All &rarr;
                </a>
            </div>
            <div class="space-y-3">
                @forelse($metrics['recent_shipments'] as $sh)
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('shipments.show', $sh) }}" class="text-xs font-mono font-bold text-sky-400 hover:underline">
                                    {{ $sh->shipment_number }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider {{ $sh->current_status->badgeClass() }}">
                                    {{ $sh->current_status->label() }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-400">
                                {{ $sh->originLocation->city }} &rarr; {{ $sh->destinationLocation->city }}
                                &bull; <span class="font-mono text-slate-500">{{ $sh->carrier->name }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 font-mono">
                                ETA: {{ $sh->estimated_arrival_at ? $sh->estimated_arrival_at->format('d M Y, H:i') : $sh->expected_arrival_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider border {{ $sh->delay_status->badgeClass() }}">
                                {{ $sh->delay_status->label() }}
                            </span>
                            <a href="{{ route('shipments.show', $sh) }}" class="mt-2 block text-xs text-sky-400 hover:underline font-medium">
                                Map &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-500">
                        You do not have any registered cargo shipments at this time.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent In-App Notifications -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Recent Notifications
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Status changes, milestone pings, and alerts</p>
                </div>
                <a href="{{ route('notifications.index') }}" class="text-xs text-emerald-400 hover:underline">
                    Inbox &rarr;
                </a>
            </div>
            <div class="space-y-3">
                @forelse($metrics['recent_notifications'] as $notif)
                    <div class="p-3.5 rounded-xl bg-slate-950 border {{ is_null($notif->read_at) ? 'border-sky-500/30 bg-sky-500/5' : 'border-slate-800/80' }} flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ is_null($notif->read_at) ? 'bg-sky-500/20 text-sky-400' : 'bg-slate-800 text-slate-400' }}">
                                    {{ $notif->type }}
                                </span>
                                @if(is_null($notif->read_at))
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-ping"></span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-300">{{ $notif->message }}</p>
                            <span class="text-[10px] text-slate-500 font-mono block">
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($notif->shipment)
                            <a href="{{ route('shipments.show', $notif->shipment) }}" class="text-xs text-sky-400 hover:underline shrink-0">
                                Track &rarr;
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-500">
                        No notifications yet. You will receive updates as milestones are logged.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = 'Instrument Sans, system-ui, sans-serif';

    const statusCtx = document.getElementById('customerStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($metrics['status_chart']['labels']),
            datasets: [{
                data: @json($metrics['status_chart']['data']),
                backgroundColor: ['#64748b', '#3b82f6', '#f59e0b', '#6366f1', '#10b981'],
                borderColor: '#0f172a',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, color: '#cbd5e1' } }
            }
        }
    });
});
</script>
@endpush
