<?php $__env->startSection('title', __('dashboard.operator.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- NexCargo Visual Freight & Route Command Center -->
    <?php if (isset($component)) { $__componentOriginald13a447d90446e30c2d06958fbf49d2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald13a447d90446e30c2d06958fbf49d2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nex-workspace','data' => ['metrics' => $metrics,'role' => 'operator']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nex-workspace'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['metrics' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics),'role' => 'operator']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald13a447d90446e30c2d06958fbf49d2b)): ?>
<?php $attributes = $__attributesOriginald13a447d90446e30c2d06958fbf49d2b; ?>
<?php unset($__attributesOriginald13a447d90446e30c2d06958fbf49d2b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald13a447d90446e30c2d06958fbf49d2b)): ?>
<?php $component = $__componentOriginald13a447d90446e30c2d06958fbf49d2b; ?>
<?php unset($__componentOriginald13a447d90446e30c2d06958fbf49d2b); ?>
<?php endif; ?>

    <!-- Header with Operational Role Badge -->
    <div class="bg-white rounded-[24px] p-6 shadow-sm border border-black/[0.03] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900"><?php echo e(__('dashboard.operator.title')); ?></h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-900 text-white">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B1A]"></span>
                    <?php echo e(__('dashboard.operator.role_badge')); ?>

                </span>
            </div>
            <p class="text-xs text-zinc-500 mt-1">
                <?php echo e(__('dashboard.operator.subtitle')); ?>

            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('shipments.create')); ?>" class="px-4 py-2 rounded-full bg-[#FF6B1A] hover:bg-[#e5560a] text-white text-xs font-semibold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <?php echo e(__('shipments.new_shipment')); ?>

            </a>
        </div>
    </div>

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Total Shipments -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium"><?php echo e(__('dashboard.operator.total_fleet')); ?></span>
                <span class="p-2 rounded-xl bg-zinc-100 text-zinc-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-zinc-900 tabular-nums"><?php echo e($metrics['total_shipments']); ?></div>
            <div class="text-[11px] text-zinc-400 mt-1"><?php echo e(__('dashboard.operator.managed_shipments')); ?></div>
        </div>

        <!-- In Transit -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium"><?php echo e(__('dashboard.operator.in_transit')); ?></span>
                <span class="p-2 rounded-xl bg-orange-50 text-[#FF6B1A]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-[#FF6B1A] tabular-nums"><?php echo e($metrics['in_transit']); ?></div>
            <div class="text-[11px] text-zinc-400 mt-1"><?php echo e(__('dashboard.operator.en_route')); ?></div>
        </div>

        <!-- Delivered -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium"><?php echo e(__('dashboard.operator.delivered')); ?></span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-700 tabular-nums"><?php echo e($metrics['delivered']); ?></div>
            <div class="text-[11px] text-zinc-400 mt-1"><?php echo e(__('dashboard.operator.successfully_fulfilled')); ?></div>
        </div>

        <!-- Delayed -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium"><?php echo e(__('dashboard.operator.delayed')); ?></span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-700 tabular-nums"><?php echo e($metrics['delayed']); ?></div>
            <div class="text-[11px] text-zinc-400 mt-1"><?php echo e(__('dashboard.operator.exceeding_window')); ?></div>
        </div>

        <!-- Open Exceptions -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium"><?php echo e(__('dashboard.operator.open_incidents')); ?></span>
                <span class="p-2 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-rose-700 tabular-nums"><?php echo e($metrics['open_exceptions']); ?></div>
            <div class="text-[11px] text-zinc-400 mt-1"><?php echo e(__('dashboard.operator.requires_action')); ?></div>
        </div>
    </div>

    <!-- Operational KPIs -->
    <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
        <div class="text-xs font-bold uppercase tracking-wider text-zinc-600 mb-4 flex items-center justify-between">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#FF6B1A]"></span>
                <?php echo e(__('dashboard.operator.ops_kpis')); ?>

            </span>
            <span class="text-[11px] text-zinc-400"><?php echo e(__('dashboard.operator.fleet_eval_active')); ?></span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70">
                <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider"><?php echo e(__('dashboard.admin.on_time_rate')); ?></div>
                <div class="text-2xl font-bold text-emerald-700 mt-1 tabular-nums"><?php echo e($metrics['kpis']['on_time_delivery_rate']); ?>%</div>
                <div class="w-full bg-zinc-200 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-emerald-600 h-1.5 rounded-full" style="width: <?php echo e(min(100, $metrics['kpis']['on_time_delivery_rate'])); ?>%"></div>
                </div>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70">
                <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider"><?php echo e(__('dashboard.admin.avg_delay')); ?></div>
                <div class="text-2xl font-bold text-amber-700 mt-1 tabular-nums"><?php echo e($metrics['kpis']['avg_delay_duration_hours']); ?> <span class="text-sm font-normal text-zinc-500"><?php echo e(__('settings.hours_suffix')); ?></span></div>
                <div class="w-full bg-zinc-200 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-amber-500 h-1.5 rounded-full" style="width: <?php echo e(min(100, ($metrics['kpis']['avg_delay_duration_hours'] / 48) * 100)); ?>%"></div>
                </div>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70">
                <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider"><?php echo e(__('dashboard.admin.exception_res_time')); ?></div>
                <div class="text-2xl font-bold text-[#FF6B1A] mt-1 tabular-nums"><?php echo e($metrics['kpis']['avg_exception_resolution_hours']); ?> <span class="text-sm font-normal text-zinc-500"><?php echo e(__('settings.hours_suffix')); ?></span></div>
                <div class="w-full bg-zinc-200 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-[#FF6B1A] h-1.5 rounded-full" style="width: <?php echo e(min(100, ($metrics['kpis']['avg_exception_resolution_hours'] / 72) * 100)); ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Doughnut -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider"><?php echo e(__('dashboard.operator.lifecycle_distribution')); ?></h2>
            <div class="relative h-64 flex items-center justify-center">
                <canvas id="operatorStatusChart"></canvas>
            </div>
        </div>

        <!-- Carrier Bar Chart -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider"><?php echo e(__('dashboard.operator.carrier_allocation')); ?></h2>
            <div class="relative h-64">
                <canvas id="operatorCarrierChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Immediate Action Queue (Open Exceptions) -->
    <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <?php echo e(__('dashboard.operator.action_queue_title')); ?>

                </h2>
                <p class="text-xs text-zinc-500 mt-0.5"><?php echo e(__('dashboard.operator.action_queue_desc')); ?></p>
            </div>
            <a href="<?php echo e(route('shipments.index', ['delay_status' => 'DELAYED_HIGH'])); ?>" class="text-xs text-[#FF6B1A] hover:underline font-semibold">
                <?php echo e(__('dashboard.operator.view_critical_delayed')); ?> &rarr;
            </a>
        </div>
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $metrics['recent_exceptions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ex): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?php echo e($ex->severity === 'CRITICAL' ? 'bg-red-100 text-red-800 border border-red-300 font-bold' : ($ex->severity === 'HIGH' ? 'bg-rose-50 text-rose-700 border border-rose-200' : ($ex->severity === 'MEDIUM' ? 'bg-orange-50 text-orange-800 border border-orange-200' : 'bg-yellow-50 text-yellow-800 border border-yellow-300'))); ?>">
                                <?php echo e($ex->severity); ?>

                            </span>
                            <span class="text-xs font-mono font-semibold text-zinc-800"><?php echo e($ex->type); ?></span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-200 text-zinc-700 font-mono font-medium"><?php echo e($ex->status); ?></span>
                        </div>
                        <p class="text-xs text-zinc-600"><?php echo e($ex->description); ?></p>
                        <div class="text-[11px] text-zinc-500 font-mono">
                            <?php echo e(__('shipments.fields.shipment_number')); ?>: <span class="text-[#FF6B1A] font-semibold"><?php echo e($ex->shipment->shipment_number); ?></span>
                            &bull; <?php echo e(__('carriers.fields.name')); ?>: <?php echo e($ex->shipment->carrier->name); ?>

                            &bull; <?php echo e(__('common.labels.timestamp')); ?>: <?php echo e($ex->detected_at->diffForHumans()); ?>

                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="<?php echo e(route('shipments.show', $ex->shipment)); ?>" class="px-3.5 py-1.5 rounded-full bg-white border border-zinc-200 hover:bg-zinc-100 text-zinc-800 text-xs font-semibold transition">
                            <?php echo e(__('dashboard.operator.open_shipment')); ?> &rarr;
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-8 text-xs text-zinc-500">
                    <?php echo e(__('dashboard.operator.no_open_exceptions')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.color = '#71717a';
    Chart.defaults.font.family = 'Plus Jakarta Sans, Instrument Sans, system-ui, sans-serif';

    // Status Chart
    const statusCtx = document.getElementById('operatorStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($metrics['status_chart']['labels'], 15, 512) ?>,
            datasets: [{
                data: <?php echo json_encode($metrics['status_chart']['data'], 15, 512) ?>,
                backgroundColor: ['#A1A1AA', '#18181B', '#FB923C', '#FF6B1A', '#10B981'],
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

    // Carrier Chart
    const carrierCtx = document.getElementById('operatorCarrierChart').getContext('2d');
    new Chart(carrierCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($metrics['carrier_chart']['labels'], 15, 512) ?>,
            datasets: [{
                label: 'Assigned Shipments',
                data: <?php echo json_encode($metrics['carrier_chart']['data'], 15, 512) ?>,
                backgroundColor: '#FF6B1A',
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
                y: { grid: { color: '#f4f4f5' }, ticks: { precision: 0, color: '#71717a' }, beginAtZero: true }
            }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/dashboard/operator.blade.php ENDPATH**/ ?>