@extends('layouts.app')

@section('title', 'Admin Platform Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header with Platform Metrics & Global Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Administrator Console</h1>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-400 border border-purple-500/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                    role: admin
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1">
                Real-time visibility into global container flows, delay telemetry, carrier performance, and audit compliance.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.audit-logs.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-700 bg-slate-900 text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Audit Trails
            </a>
            <a href="{{ route('admin.settings.index') }}" class="px-3 py-1.5 rounded-xl border border-slate-700 bg-slate-900 text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800 transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Delay Threshold Config
            </a>
            <a href="{{ route('shipments.create') }}" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white text-xs font-semibold shadow-md shadow-sky-500/20 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Shipment
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
            <div class="text-[11px] text-slate-500 mt-1">Registered Cargo Units</div>
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
            <div class="text-[11px] text-slate-500 mt-1">Active on Corridors</div>
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
            <div class="text-[11px] text-slate-500 mt-1">Terminal State Completed</div>
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
            <div class="text-[11px] text-slate-500 mt-1">Exceeding ETA Threshold</div>
        </div>

        <!-- Open Exceptions -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                <span>Open Exceptions</span>
                <span class="p-1.5 rounded-lg bg-rose-500/10 text-rose-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-rose-400">{{ $metrics['open_exceptions'] }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Action Required</div>
        </div>
    </div>

    <!-- Key Performance Indicators (KPIs) Strip -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 via-slate-900/90 to-slate-950 border border-slate-800">
        <div class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-sky-400 animate-ping"></span>
                Strategic Logistics KPIs (Group Corridor Benchmark)
            </span>
            <span class="text-[11px] text-slate-500 lowercase font-mono">corridor: Southeast Asia &rarr; Europe</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-1">
            <!-- KPI 1: On-Time Delivery Rate -->
            <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider">On-Time Delivery Rate</div>
                <div class="text-2xl font-bold text-emerald-400 mt-1">
                    {{ $metrics['kpis']['on_time_delivery_rate'] }}%
                </div>
                <div class="text-xs text-slate-500 mt-1">Target benchmark: &ge; 85.0%</div>
                <!-- Progress bar -->
                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, $metrics['kpis']['on_time_delivery_rate']) }}%"></div>
                </div>
            </div>

            <!-- KPI 2: Average Delay Duration -->
            <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider">Average Delay Duration</div>
                <div class="text-2xl font-bold text-amber-400 mt-1">
                    {{ $metrics['kpis']['avg_delay_duration_hours'] }} <span class="text-sm font-normal text-slate-400">hours</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">Across delayed maritime shipments</div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ min(100, ($metrics['kpis']['avg_delay_duration_hours'] / 48) * 100) }}%"></div>
                </div>
            </div>

            <!-- KPI 3: Exception Resolution Time -->
            <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800/80">
                <div class="text-[11px] text-slate-400 uppercase tracking-wider">Exception Resolution Time</div>
                <div class="text-2xl font-bold text-sky-400 mt-1">
                    {{ $metrics['kpis']['avg_exception_resolution_hours'] }} <span class="text-sm font-normal text-slate-400">hours</span>
                </div>
                <div class="text-xs text-slate-500 mt-1">Average time from OPEN &rarr; RESOLVED</div>
                <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-sky-500 h-1.5 rounded-full" style="width: {{ min(100, ($metrics['kpis']['avg_exception_resolution_hours'] / 72) * 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart 1: Lifecycle Status Breakdown -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Shipment Lifecycle Status</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Distribution across deterministic FSM states</p>
                </div>
                <span class="text-[10px] font-mono text-slate-500 bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800">
                    Total: {{ $metrics['total_shipments'] }}
                </span>
            </div>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Shipments by Carrier -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Carrier Fleet Allocation</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Assigned volume by operating maritime carrier</p>
                </div>
                <span class="text-[10px] font-mono text-slate-500 bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800">
                    Active Carriers
                </span>
            </div>
            <div class="relative h-64">
                <canvas id="carrierChart"></canvas>
            </div>
        </div>

        <!-- Chart 3: Delay Severity Distribution -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Delay Telemetry Breakdown</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Evaluated against ETA threshold (0-6h LOW, 6-24h MED, &gt;24h HIGH)</p>
                </div>
                <span class="text-[10px] font-mono text-amber-400 bg-amber-500/10 px-2.5 py-1 rounded-lg border border-amber-500/30">
                    Rate: {{ $metrics['kpis']['delay_rate'] }}%
                </span>
            </div>
            <div class="relative h-64">
                <canvas id="delayChart"></canvas>
            </div>
        </div>

        <!-- Chart 4: Exception Types Breakdown -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider">Disruption & Exception Analysis</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Incident categorization (Customs Hold, Weather, Congestion)</p>
                </div>
                <span class="text-[10px] font-mono text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-lg border border-rose-500/30">
                    Rate: {{ $metrics['kpis']['exception_rate'] }}%
                </span>
            </div>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="exceptionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Active Exceptions & Recent Shipments Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Active Exceptions -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        Active Exceptions Requiring Intervention
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Open &amp; Investigating incidents</p>
                </div>
                <span class="text-xs font-mono text-rose-400 bg-rose-500/10 px-2.5 py-1 rounded-lg border border-rose-500/30">
                    {{ $metrics['open_exceptions'] }} Open
                </span>
            </div>
            <div class="space-y-3">
                @forelse($metrics['recent_exceptions'] as $ex)
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800/80 flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $ex->severity === 'CRITICAL' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/40' : 'bg-amber-500/20 text-amber-400 border border-amber-500/40' }}">
                                    {{ $ex->severity }}
                                </span>
                                <span class="text-xs font-mono text-slate-300 font-semibold">{{ $ex->type }}</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-800 text-slate-400 font-mono">{{ $ex->status }}</span>
                            </div>
                            <p class="text-xs text-slate-400 line-clamp-1">{{ $ex->description }}</p>
                            <div class="text-[11px] text-slate-500 font-mono">
                                Shipment: <a href="{{ route('shipments.show', $ex->shipment) }}" class="text-sky-400 hover:underline">{{ $ex->shipment->shipment_number }}</a>
                                &bull; Logged: {{ $ex->detected_at->diffForHumans() }}
                            </div>
                        </div>
                        <a href="{{ route('shipments.show', $ex->shipment) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-[11px] text-white shrink-0 font-medium transition">
                            Resolve &rarr;
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-500">
                        No active exceptions recorded. Platform operational integrity at 100%.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Shipments -->
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                        Recent Fleet Activity
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Latest updated cargo records</p>
                </div>
                <a href="{{ route('shipments.index') }}" class="text-xs text-sky-400 hover:underline font-medium">
                    View All &rarr;
                </a>
            </div>
            <div class="space-y-3">
                @forelse($metrics['recent_shipments'] as $sh)
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800/80 flex items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('shipments.show', $sh) }}" class="text-xs font-mono font-bold text-sky-400 hover:underline">
                                    {{ $sh->shipment_number }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider {{ $sh->current_status->badgeClass() }}">
                                    {{ $sh->current_status->label() }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-400 flex items-center gap-1.5">
                                <span>{{ $sh->originLocation->city }}</span>
                                <span class="text-slate-600">&rarr;</span>
                                <span>{{ $sh->destinationLocation->city }}</span>
                                <span class="text-slate-600">&bull;</span>
                                <span class="text-slate-500 font-mono">{{ $sh->carrier->name }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider border {{ $sh->delay_status->badgeClass() }}">
                                {{ $sh->delay_status->label() }}
                            </span>
                            <span class="text-[10px] text-slate-500 font-mono block mt-1">
                                {{ $sh->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-slate-500">
                        No shipments registered yet.
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
    // Shared Chart.js styling defaults
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = 'Instrument Sans, system-ui, sans-serif';

    // 1. Status Chart (Doughnut)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($metrics['status_chart']['labels']),
            datasets: [{
                data: @json($metrics['status_chart']['data']),
                backgroundColor: [
                    '#64748b', // CREATED (Slate)
                    '#3b82f6', // BOOKED (Blue)
                    '#f59e0b', // PICKED_UP (Amber)
                    '#6366f1', // IN_TRANSIT (Indigo)
                    '#10b981'  // DELIVERED (Emerald)
                ],
                borderColor: '#0f172a',
                borderWidth: 2,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 12, color: '#cbd5e1' }
                }
            }
        }
    });

    // 2. Carrier Chart (Bar)
    const carrierCtx = document.getElementById('carrierChart').getContext('2d');
    new Chart(carrierCtx, {
        type: 'bar',
        data: {
            labels: @json($metrics['carrier_chart']['labels']),
            datasets: [{
                label: 'Shipments',
                data: @json($metrics['carrier_chart']['data']),
                backgroundColor: '#38bdf8',
                borderRadius: 8,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                y: {
                    grid: { color: '#334155' },
                    ticks: { precision: 0, color: '#94a3b8' },
                    beginAtZero: true
                }
            }
        }
    });

    // 3. Delay Breakdown Chart (Bar)
    const delayCtx = document.getElementById('delayChart').getContext('2d');
    new Chart(delayCtx, {
        type: 'bar',
        data: {
            labels: @json($metrics['delay_chart']['labels']),
            datasets: [{
                label: 'Shipment Count',
                data: @json($metrics['delay_chart']['data']),
                backgroundColor: [
                    '#10b981', // On Time
                    '#fbbf24', // Low
                    '#f97316', // Medium
                    '#ef4444'  // High
                ],
                borderRadius: 8,
                barThickness: 28
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                y: {
                    grid: { color: '#334155' },
                    ticks: { precision: 0, color: '#94a3b8' },
                    beginAtZero: true
                }
            }
        }
    });

    // 4. Exception Types Chart (Doughnut / PolarArea)
    const exceptionCtx = document.getElementById('exceptionChart').getContext('2d');
    const exceptionLabels = @json($metrics['exception_chart']['labels']);
    const exceptionData = @json($metrics['exception_chart']['data']);

    new Chart(exceptionCtx, {
        type: 'doughnut',
        data: {
            labels: exceptionLabels.length > 0 ? exceptionLabels : ['No Exceptions'],
            datasets: [{
                data: exceptionData.length > 0 ? exceptionData : [1],
                backgroundColor: exceptionData.length > 0 ? [
                    '#f43f5e', '#fb7185', '#fda4af', '#e11d48', '#be123c', '#9f1239'
                ] : ['#334155'],
                borderColor: '#0f172a',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 12, color: '#cbd5e1' }
                }
            }
        }
    });
});
</script>
@endpush
