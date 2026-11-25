@extends('layouts.app')

@section('title', __('dashboard.operator.title'))

@section('content')
<div class="space-y-8">
    <!-- Visual Freight & Route Command Center -->
    <x-nex-workspace :metrics="$metrics" role="operator" />

    <!-- Unified Operator Summary Card: Header + 5 Fleet Metrics + 3 Operational KPIs -->
    <div id="operational-analytics" class="nex-card p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-[#EFE6EA]">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#4A2C35]">{{ __('dashboard.operator.title') }}</h1>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#BE2B5B] text-white">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#BE2B5B]"></span>
                        {{ __('dashboard.operator.role_badge') }}
                    </span>
                </div>
                <p class="text-xs text-[#826871] mt-1">
                    {{ __('dashboard.operator.subtitle') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('shipments.create') }}" class="px-4 py-2 rounded-full bg-[#BE2B5B] hover:bg-[#A0224B] text-white text-xs font-semibold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    {{ __('shipments.new_shipment') }}
                </a>
            </div>
        </div>

        <!-- 5 Core Metric Summary Tiles -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            <div class="nex-inset p-4">
                <div class="flex items-center justify-between text-xs text-[#826871] mb-1.5">
                    <span class="font-medium">{{ __('dashboard.operator.total_fleet') }}</span>
                    <span class="p-1.5 rounded-lg bg-white border border-[#EFE6EA] text-[#4A2C35]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-[#4A2C35] tabular-nums">{{ $metrics['total_shipments'] }}</div>
                <div class="text-[11px] text-[#826871] mt-0.5">{{ __('dashboard.operator.managed_shipments') }}</div>
            </div>

            <div class="nex-inset p-4">
                <div class="flex items-center justify-between text-xs text-[#826871] mb-1.5">
                    <span class="font-medium">{{ __('dashboard.operator.in_transit') }}</span>
                    <span class="p-1.5 rounded-lg bg-[#FCECF1] text-[#BE2B5B]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-[#BE2B5B] tabular-nums">{{ $metrics['in_transit'] }}</div>
                <div class="text-[11px] text-[#826871] mt-0.5">{{ __('dashboard.operator.en_route') }}</div>
            </div>

            <div class="nex-inset p-4">
                <div class="flex items-center justify-between text-xs text-[#826871] mb-1.5">
                    <span class="font-medium">{{ __('dashboard.operator.delivered') }}</span>
                    <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-emerald-700 tabular-nums">{{ $metrics['delivered'] }}</div>
                <div class="text-[11px] text-[#826871] mt-0.5">{{ __('dashboard.operator.successfully_fulfilled') }}</div>
            </div>

            <div class="nex-inset p-4">
                <div class="flex items-center justify-between text-xs text-[#826871] mb-1.5">
                    <span class="font-medium">{{ __('dashboard.operator.delayed') }}</span>
                    <span class="p-1.5 rounded-lg bg-amber-50 text-amber-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-amber-700 tabular-nums">{{ $metrics['delayed'] }}</div>
                <div class="text-[11px] text-[#826871] mt-0.5">{{ __('dashboard.operator.exceeding_window') }}</div>
            </div>

            <div class="nex-inset p-4 col-span-2 sm:col-span-1">
                <div class="flex items-center justify-between text-xs text-[#826871] mb-1.5">
                    <span class="font-medium">{{ __('dashboard.operator.open_incidents') }}</span>
                    <span class="p-1.5 rounded-lg bg-red-50 text-red-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-red-700 tabular-nums">{{ $metrics['open_exceptions'] }}</div>
                <div class="text-[11px] text-[#826871] mt-0.5">{{ __('dashboard.operator.requires_action') }}</div>
            </div>
        </div>

        <!-- Operational KPIs -->
        <div class="pt-4 border-t border-[#EFE6EA]">
            <div class="text-xs font-bold uppercase tracking-wider text-[#826871] mb-3 flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-[#BE2B5B]"></span>
                    {{ __('dashboard.operator.ops_kpis') }}
                </span>
                <span class="text-[11px] text-[#826871]">{{ __('dashboard.operator.fleet_eval_active') }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                <div class="nex-inset p-4">
                    <div class="text-[11px] font-semibold text-[#826871] uppercase tracking-wider">{{ __('dashboard.admin.on_time_rate') }}</div>
                    <div class="text-2xl font-extrabold text-emerald-700 mt-1 tabular-nums">{{ $metrics['kpis']['on_time_delivery_rate'] }}%</div>
                    <div class="w-full bg-[#EFE6EA] h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-emerald-600 h-1.5 rounded-full" style="width: {{ min(100, $metrics['kpis']['on_time_delivery_rate']) }}%"></div>
                    </div>
                </div>
                <div class="nex-inset p-4">
                    <div class="text-[11px] font-semibold text-[#826871] uppercase tracking-wider">{{ __('dashboard.admin.avg_delay') }}</div>
                    <div class="text-2xl font-extrabold text-amber-700 mt-1 tabular-nums">{{ $metrics['kpis']['avg_delay_duration_hours'] }} <span class="text-sm font-normal text-[#826871]">{{ __('settings.hours_suffix') }}</span></div>
                    <div class="w-full bg-[#EFE6EA] h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ min(100, ($metrics['kpis']['avg_delay_duration_hours'] / 48) * 100) }}%"></div>
                    </div>
                </div>
                <div class="nex-inset p-4">
                    <div class="text-[11px] font-semibold text-[#826871] uppercase tracking-wider">{{ __('dashboard.admin.exception_res_time') }}</div>
                    <div class="text-2xl font-extrabold text-[#BE2B5B] mt-1 tabular-nums">{{ $metrics['kpis']['avg_exception_resolution_hours'] }} <span class="text-sm font-normal text-[#826871]">{{ __('settings.hours_suffix') }}</span></div>
                    <div class="w-full bg-[#EFE6EA] h-1.5 rounded-full mt-3 overflow-hidden">
                        <div class="bg-[#BE2B5B] h-1.5 rounded-full" style="width: {{ min(100, ($metrics['kpis']['avg_exception_resolution_hours'] / 72) * 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="nex-card p-6 space-y-4">
            <h2 class="text-sm font-bold text-[#4A2C35] uppercase tracking-wider">{{ __('dashboard.operator.lifecycle_distribution') }}</h2>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="operatorStatusChart"></canvas>
            </div>
        </div>

        <div class="nex-card p-6 space-y-4">
            <h2 class="text-sm font-bold text-[#4A2C35] uppercase tracking-wider">{{ __('dashboard.operator.carrier_allocation') }}</h2>
            <div class="relative h-64">
                <canvas id="operatorCarrierChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Immediate Action Queue (Open Exceptions) -->
    <div class="nex-card p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-[#4A2C35] uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    {{ __('dashboard.operator.action_queue_title') }}
                </h2>
                <p class="text-xs text-[#8C737B] mt-0.5">{{ __('dashboard.operator.action_queue_desc') }}</p>
            </div>
            <a href="{{ route('shipments.index', ['delay_status' => 'DELAYED_HIGH']) }}" class="text-xs text-[#C93565] hover:underline font-semibold">
                {{ __('dashboard.operator.view_critical_delayed') }} &rarr;
            </a>
        </div>
        <div class="space-y-3">
            @forelse($metrics['recent_exceptions'] as $ex)
                <div class="nex-inset p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $ex->severity === 'CRITICAL' ? 'bg-[#BE2B5B] text-[#F8C9DC] border border-[#9E224B]' : ($ex->severity === 'HIGH' ? 'bg-red-50 text-red-700 border border-red-200' : ($ex->severity === 'MEDIUM' ? 'bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC]' : 'bg-amber-50 text-amber-800 border border-amber-200')) }}">
                                {{ $ex->severity }}
                            </span>
                            <span class="text-xs font-mono font-semibold text-[#4A2C35]">{{ $ex->type }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-white border border-[#ECE4E8] text-[#6E545D] font-mono font-medium">{{ $ex->status }}</span>
                        </div>
                        <p class="text-xs text-[#7D626B]">{{ $ex->description }}</p>
                        <div class="text-[11px] text-[#8C737B] font-mono">
                            {{ __('shipments.fields.shipment_number') }}: <span class="text-[#C93565] font-semibold">{{ $ex->shipment->shipment_number }}</span>
                            &bull; {{ __('carriers.fields.name') }}: {{ $ex->shipment->carrier->name }}
                            &bull; {{ __('common.labels.timestamp') }}: {{ $ex->detected_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('shipments.show', $ex->shipment) }}" class="px-3.5 py-1.5 rounded-full bg-white border border-[#ECE4E8] hover:bg-[#FDF2F6] text-[#4A2C35] text-xs font-semibold transition">
                            {{ __('dashboard.operator.open_shipment') }} &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-xs text-[#8C737B]">
                    {{ __('dashboard.operator.no_open_exceptions') }}
                </div>
            @endforelse
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

    const statusCtx = document.getElementById('operatorStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($metrics['status_chart']['labels']),
            datasets: [{
                data: @json($metrics['status_chart']['data']),
                backgroundColor: ['#C8B9C1', '#9E224B', '#F29EC0', '#E04879', '#10B981'],
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

    const carrierCtx = document.getElementById('operatorCarrierChart').getContext('2d');
    new Chart(carrierCtx, {
        type: 'bar',
        data: {
            labels: @json($metrics['carrier_chart']['labels']),
            datasets: [{
                label: 'Assigned Shipments',
                data: @json($metrics['carrier_chart']['data']),
                backgroundColor: '#E04879',
                borderRadius: 8,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { color: '#71717a' } },
                y: { grid: { color: '#f5f0f3' }, ticks: { precision: 0, color: '#71717a' }, beginAtZero: true }
            }
        }
    });
});
</script>
@endpush
