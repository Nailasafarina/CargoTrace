@extends('layouts.app')

@section('title', __('dashboard.admin.title'))

@section('content')
<div class="space-y-8">
    <!-- NexCargo Visual Freight & Route Command Center -->
    <x-nex-workspace :metrics="$metrics" role="admin" />

    <!-- Header with Platform Metrics & Global Actions -->
    <div class="bg-white rounded-[24px] p-6 shadow-sm border border-black/[0.03] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900">{{ __('dashboard.admin.title') }}</h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-900 text-white">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B1A]"></span>
                    {{ __('dashboard.admin.role_badge') }}
                </span>
            </div>
            <p class="text-xs text-zinc-500 mt-1">
                {{ __('dashboard.admin.subtitle') }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 rounded-full border border-zinc-200 bg-white text-xs font-semibold text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('dashboard.admin.audit_trails') }}
            </a>
            <a href="{{ route('admin.settings.index') }}" class="px-4 py-2 rounded-full border border-zinc-200 bg-white text-xs font-semibold text-zinc-700 hover:text-zinc-900 hover:bg-zinc-50 transition flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#FF6B1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ __('dashboard.admin.delay_threshold_config') }}
            </a>
            <a href="{{ route('shipments.create') }}" class="px-4 py-2 rounded-full bg-[#FF6B1A] hover:bg-[#e5560a] text-white text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('dashboard.admin.new_shipment') }}
            </a>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Total Shipments -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium">{{ __('dashboard.admin.total_fleet') }}</span>
                <span class="p-2 rounded-xl bg-zinc-100 text-zinc-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-zinc-900 tabular-nums">{{ $metrics['total_shipments'] }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">{{ __('dashboard.admin.registered_units') }}</div>
        </div>

        <!-- In Transit -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium">{{ __('dashboard.admin.in_transit') }}</span>
                <span class="p-2 rounded-xl bg-orange-50 text-[#FF6B1A]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-[#FF6B1A] tabular-nums">{{ $metrics['in_transit'] }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">{{ __('dashboard.admin.active_corridors') }}</div>
        </div>

        <!-- Delivered -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium">{{ __('dashboard.admin.delivered') }}</span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-700 tabular-nums">{{ $metrics['delivered'] }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">{{ __('dashboard.admin.terminal_completed') }}</div>
        </div>

        <!-- Delayed -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium">{{ __('dashboard.admin.delayed') }}</span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-700 tabular-nums">{{ $metrics['delayed'] }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">{{ __('dashboard.admin.exceeding_threshold') }}</div>
        </div>

        <!-- Open Exceptions -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium">{{ __('dashboard.admin.open_exceptions') }}</span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-rose-700 tabular-nums">{{ $metrics['open_exceptions'] }}</div>
            <div class="text-[11px] text-zinc-400 mt-1">{{ __('dashboard.admin.action_required') }}</div>
        </div>
    </div>

    <!-- Key Performance Indicators (KPIs) Strip -->
    <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
        <div class="text-xs font-bold uppercase tracking-wider text-zinc-600 mb-4 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#FF6B1A] animate-ping"></span>
                {{ __('dashboard.admin.kpi_heading') }} {{ __('dashboard.admin.kpi_corridor_benchmark') }}
            </span>
            <span class="text-[11px] text-zinc-400 lowercase font-mono">{{ __('dashboard.admin.corridor_benchmark_sub') }}</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- KPI 1: On-Time Delivery Rate -->
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70">
                <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">{{ __('dashboard.admin.on_time_rate') }}</div>
                <div class="text-2xl font-bold text-emerald-700 mt-1 tabular-nums">
                    {{ $metrics['kpis']['on_time_delivery_rate'] }}%
                </div>
                <div class="text-xs text-zinc-500 mt-1">{{ __('dashboard.admin.target_benchmark') }}</div>
                <div class="w-full bg-zinc-200 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ min(100, $metrics['kpis']['on_time_delivery_rate']) }}%"></div>
                </div>
            </div>

            <!-- KPI 2: Average Delay Duration -->
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70">
                <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">{{ __('dashboard.admin.avg_delay') }}</div>
                <div class="text-2xl font-bold text-amber-700 mt-1 tabular-nums">
                    {{ $metrics['kpis']['avg_delay_duration_hours'] }} <span class="text-sm font-normal text-zinc-500">{{ __('settings.hours_suffix') }}</span>
                </div>
                <div class="text-xs text-zinc-500 mt-1">{{ __('dashboard.admin.across_delayed') }}</div>
                <div class="w-full bg-zinc-200 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ min(100, ($metrics['kpis']['avg_delay_duration_hours'] / 48) * 100) }}%"></div>
                </div>
            </div>

            <!-- KPI 3: Exception Resolution Time -->
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70">
                <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider">{{ __('dashboard.admin.exception_res_time') }}</div>
                <div class="text-2xl font-bold text-[#FF6B1A] mt-1 tabular-nums">
                    {{ $metrics['kpis']['avg_exception_resolution_hours'] }} <span class="text-sm font-normal text-zinc-500">{{ __('settings.hours_suffix') }}</span>
                </div>
                <div class="text-xs text-zinc-500 mt-1">{{ __('dashboard.admin.avg_res_time_desc') }}</div>
                <div class="w-full bg-zinc-200 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-[#FF6B1A] h-1.5 rounded-full" style="width: {{ min(100, ($metrics['kpis']['avg_exception_resolution_hours'] / 72) * 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart 1: Lifecycle Status Breakdown -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">{{ __('dashboard.admin.lifecycle_chart_title') }}</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('dashboard.admin.lifecycle_chart_desc') }}</p>
                </div>
                <span class="text-[10px] font-mono text-zinc-600 bg-zinc-100 px-3 py-1 rounded-full">
                    {{ __('common.labels.total') }}: {{ $metrics['total_shipments'] }}
                </span>
            </div>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Shipments by Carrier -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">{{ __('dashboard.admin.carrier_chart_title') }}</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('dashboard.admin.carrier_chart_desc') }}</p>
                </div>
                <span class="text-[10px] font-mono text-zinc-600 bg-zinc-100 px-3 py-1 rounded-full">
                    {{ __('dashboard.admin.active_carriers') }}
                </span>
            </div>
            <div class="relative h-64">
                <canvas id="carrierChart"></canvas>
            </div>
        </div>

        <!-- Chart 3: Delay Severity Distribution -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">{{ __('dashboard.admin.delay_chart_title') }}</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('dashboard.admin.delay_chart_desc') }}</p>
                </div>
                <span class="text-[10px] font-mono text-amber-800 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    {{ __('dashboard.admin.rate_label', ['rate' => $metrics['kpis']['delay_rate']]) }}
                </span>
            </div>
            <div class="relative h-64">
                <canvas id="delayChart"></canvas>
            </div>
        </div>

        <!-- Chart 4: Exception Types Breakdown -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider">{{ __('dashboard.admin.disruption_chart_title') }}</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('dashboard.admin.disruption_chart_desc') }}</p>
                </div>
                <span class="text-[10px] font-mono text-rose-700 bg-rose-50 px-3 py-1 rounded-full border border-rose-200">
                    {{ __('dashboard.admin.rate_label', ['rate' => $metrics['kpis']['exception_rate']]) }}
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
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        {{ __('dashboard.admin.active_exceptions_title') }}
                    </h2>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('dashboard.admin.open_investigating') }}</p>
                </div>
                <span class="text-xs font-mono font-semibold text-rose-700 bg-rose-50 px-3 py-1 rounded-full border border-rose-200">
                    {{ __('dashboard.admin.open_count', ['count' => $metrics['open_exceptions']]) }}
                </span>
            </div>
            <div class="space-y-3">
                @forelse($metrics['recent_exceptions'] as $ex)
                    <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70 flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $ex->severity === 'CRITICAL' ? 'bg-red-100 text-red-800 border border-red-300 font-bold' : ($ex->severity === 'HIGH' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($ex->severity === 'MEDIUM' ? 'bg-orange-50 text-orange-800 border border-orange-200' : 'bg-yellow-50 text-yellow-800 border border-yellow-300')) }}">
                                    {{ $ex->severity }}
                                </span>
                                <span class="text-xs font-mono text-zinc-700 font-semibold">{{ $ex->type }}</span>
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-200 text-zinc-700 font-mono font-medium">{{ $ex->status }}</span>
                            </div>
                            <p class="text-xs text-zinc-600 line-clamp-1">{{ $ex->description }}</p>
                            <div class="text-[11px] text-zinc-500 font-mono">
                                {{ __('shipments.fields.shipment_number') }}: <a href="{{ route('shipments.show', $ex->shipment) }}" class="text-[#FF6B1A] hover:underline font-semibold">{{ $ex->shipment->shipment_number }}</a>
                                &bull; {{ __('common.labels.timestamp') }}: {{ $ex->detected_at->diffForHumans() }}
                            </div>
                        </div>
                        <a href="{{ route('shipments.show', $ex->shipment) }}" class="px-3 py-1.5 rounded-full bg-white border border-zinc-200 hover:bg-zinc-100 text-[11px] text-zinc-800 shrink-0 font-semibold transition">
                            {{ __('dashboard.admin.resolve_action') }} &rarr;
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-zinc-500">
                        {{ __('dashboard.admin.no_active_exceptions') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Shipments -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#FF6B1A]"></span>
                        {{ __('dashboard.admin.recent_activity_title') }}
                    </h2>
                    <p class="text-xs text-zinc-500 mt-0.5">{{ __('dashboard.admin.recent_activity_desc') }}</p>
                </div>
                <a href="{{ route('shipments.index') }}" class="text-xs text-[#FF6B1A] hover:underline font-semibold">
                    {{ __('dashboard.admin.view_all') }} &rarr;
                </a>
            </div>
            <div class="space-y-3">
                @forelse($metrics['recent_shipments'] as $sh)
                    <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70 flex items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('shipments.show', $sh) }}" class="text-xs font-mono font-bold text-zinc-900 hover:text-[#FF6B1A] hover:underline">
                                    {{ $sh->shipment_number }}
                                </a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider {{ $sh->current_status->badgeClass() }}">
                                    {{ $sh->current_status->label() }}
                                </span>
                            </div>
                            <div class="text-xs text-zinc-500 flex items-center gap-1.5">
                                <span class="text-zinc-800 font-medium">{{ $sh->originLocation->city }}</span>
                                <span class="text-zinc-400">&rarr;</span>
                                <span class="text-zinc-800 font-medium">{{ $sh->destinationLocation->city }}</span>
                                <span class="text-zinc-300">&bull;</span>
                                <span class="text-zinc-500 font-mono">{{ $sh->carrier->name }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider border {{ $sh->delay_status->badgeClass() }}">
                                {{ $sh->delay_status->label() }}
                            </span>
                            <span class="text-[10px] text-zinc-500 font-mono block mt-1">
                                {{ $sh->updated_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-xs text-zinc-500">
                        {{ __('dashboard.admin.no_shipments') }}
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
    Chart.defaults.color = '#71717a';
    Chart.defaults.font.family = 'Plus Jakarta Sans, Instrument Sans, system-ui, sans-serif';

    // 1. Status Chart (Doughnut)
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($metrics['status_chart']['labels']),
            datasets: [{
                data: @json($metrics['status_chart']['data']),
                backgroundColor: [
                    '#A1A1AA', // CREATED
                    '#18181B', // BOOKED (Charcoal)
                    '#FB923C', // PICKED_UP
                    '#FF6B1A', // IN_TRANSIT (NexCargo Orange)
                    '#10B981'  // DELIVERED (Emerald)
                ],
                borderColor: '#ffffff',
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
                    labels: { boxWidth: 12, padding: 12, color: '#52525b' }
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
                backgroundColor: '#FF6B1A',
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
                x: { grid: { display: false }, ticks: { color: '#71717a' } },
                y: {
                    grid: { color: '#f4f4f5' },
                    ticks: { precision: 0, color: '#71717a' },
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
                    '#10B981', // On Time
                    '#FBBF24', // Low
                    '#FF6B1A', // Medium
                    '#EF4444'  // High
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
                x: { grid: { display: false }, ticks: { color: '#71717a' } },
                y: {
                    grid: { color: '#f4f4f5' },
                    ticks: { precision: 0, color: '#71717a' },
                    beginAtZero: true
                }
            }
        }
    });

    // 4. Exception Types Chart (Doughnut)
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
                    '#FF6B1A', '#18181B', '#F43F5E', '#FB923C', '#71717A', '#D4D4D8'
                ] : ['#e4e4e7'],
                borderColor: '#ffffff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { boxWidth: 12, padding: 12, color: '#52525b' }
                }
            }
        }
    });
});
</script>
@endpush
