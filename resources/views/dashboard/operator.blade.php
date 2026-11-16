@extends('layouts.app')

@section('title', 'Logistics Operator Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header with Operational Role Badge -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Logistics Operations Control Hub</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    role: operator
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Dispatch execution, milestone telemetry updates, exception mitigation, and active fleet monitoring.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('shipments.create') }}" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white text-xs font-semibold shadow-md shadow-amber-500/20 transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Book New Shipment
            </a>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Total Shipments -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span>Total Fleet</span>
                <span class="p-1.5 rounded-lg bg-sky-500/10 text-sky-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-white">{{ $metrics['total_shipments'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Managed Shipments</div>
        </div>

        <!-- In Transit -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span>In Transit</span>
                <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-indigo-400">{{ $metrics['in_transit'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">En Route on Sea/Air</div>
        </div>

        <!-- Delivered -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span>Delivered</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-400">{{ $metrics['delivered'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Successfully Fulfilled</div>
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
            <div class="text-[11px] text-slate-500 mt-1">Exceeding ETA Window</div>
        </div>

        <!-- Open Exceptions -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span>Open Incidents</span>
                <span class="p-1.5 rounded-lg bg-rose-500/10 text-rose-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-rose-400">{{ $metrics['open_exceptions'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Requires Operator Action</div>
        </div>
    </div>

    <!-- Operational KPIs -->
    <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800">
        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                Operations Performance KPIs (Corridor Southeast Asia &rarr; Europe)
            </span>
            <span class="text-[11px] text-slate-500">Fleet Evaluation Active</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider">On-Time Delivery Rate</div>
                <div class="text-2xl font-bold text-emerald-400 mt-1">{{ $metrics['kpis']['on_time_delivery_rate'] }}%</div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, $metrics['kpis']['on_time_delivery_rate']) }}%"></div>
                </div>
            </div>
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider">Average Delay Duration</div>
                <div class="text-2xl font-bold text-amber-400 mt-1">{{ $metrics['kpis']['avg_delay_duration_hours'] }} <span class="text-sm font-normal text-slate-400">hrs</span></div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ min(100, ($metrics['kpis']['avg_delay_duration_hours'] / 48) * 100) }}%"></div>
                </div>
            </div>
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider">Avg Exception Resolution Time</div>
                <div class="text-2xl font-bold text-sky-400 mt-1">{{ $metrics['kpis']['avg_exception_resolution_hours'] }} <span class="text-sm font-normal text-slate-400">hrs</span></div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-sky-500 h-1.5 rounded-full" style="width: {{ min(100, ($metrics['kpis']['avg_exception_resolution_hours'] / 72) * 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Doughnut -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Lifecycle Status Distribution</h2>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="operatorStatusChart"></canvas>
            </div>
        </div>

        <!-- Carrier Bar Chart -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider">Carrier Allocation</h2>
            <div class="relative h-64">
                <canvas id="operatorCarrierChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Immediate Action Queue (Open Exceptions) -->
    <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                    Immediate Operational Action Queue
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Exceptions pending investigation or resolution</p>
            </div>
            <a href="{{ route('shipments.index', ['delay_status' => 'DELAYED_HIGH']) }}" class="text-xs text-sky-400 hover:underline">
                View Critical Delayed &rarr;
            </a>
        </div>
        <div class="space-y-3">
            @forelse($metrics['recent_exceptions'] as $ex)
                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $ex->severity === 'CRITICAL' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-amber-500/20 text-amber-400 border border-amber-500/40' }}">
                                {{ $ex->severity }}
                            </span>
                            <span class="text-xs font-mono font-semibold text-white">{{ $ex->type }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-mono">{{ $ex->status }}</span>
                        </div>
                        <p class="text-xs text-slate-300">{{ $ex->description }}</p>
                        <div class="text-[11px] text-slate-500 font-mono">
                            Shipment: <span class="text-sky-400">{{ $ex->shipment->shipment_number }}</span>
                            &bull; Carrier: {{ $ex->shipment->carrier->name }}
                            &bull; Detected: {{ $ex->detected_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('shipments.show', $ex->shipment) }}" class="px-3 py-1.5 rounded-lg bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 border border-sky-500/30 text-xs font-semibold transition">
                            Open Shipment &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-xs text-slate-500">
                    No open exceptions currently require intervention. Fleet running smoothly.
                </div>
            @endforelse
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

    // Status Chart
    const statusCtx = document.getElementById('operatorStatusChart').getContext('2d');
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

    // Carrier Chart
    const carrierCtx = document.getElementById('operatorCarrierChart').getContext('2d');
    new Chart(carrierCtx, {
        type: 'bar',
        data: {
            labels: @json($metrics['carrier_chart']['labels']),
            datasets: [{
                label: 'Assigned Shipments',
                data: @json($metrics['carrier_chart']['data']),
                backgroundColor: '#f59e0b',
                borderRadius: 8,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                y: { grid: { color: '#334155' }, ticks: { precision: 0, color: '#94a3b8' }, beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
