<?php $__env->startSection('title', __('dashboard.customer.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8">
    <!-- NexCargo Visual Freight & Route Command Center -->
    <?php if (isset($component)) { $__componentOriginald13a447d90446e30c2d06958fbf49d2b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald13a447d90446e30c2d06958fbf49d2b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.nex-workspace','data' => ['metrics' => $metrics,'role' => 'customer']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nex-workspace'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['metrics' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($metrics),'role' => 'customer']); ?>
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

    <!-- Header -->
    <div class="bg-white rounded-[24px] p-6 shadow-sm border border-black/[0.03] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-zinc-900"><?php echo e(__('dashboard.customer.title')); ?></h1>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-zinc-900 text-white">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <?php echo e(__('dashboard.customer.role_badge')); ?>

                </span>
            </div>
            <p class="text-xs text-zinc-500 mt-1">
                <?php echo e(__('dashboard.customer.subtitle')); ?>

            </p>
        </div>
        <div>
            <a href="<?php echo e(route('shipments.index')); ?>" class="px-4 py-2 rounded-full bg-white hover:bg-zinc-50 text-zinc-800 text-xs font-semibold border border-zinc-200 transition flex items-center gap-2">
                <svg class="w-4 h-4 text-[#FF6B1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <?php echo e(__('dashboard.customer.view_all_my_cargo')); ?>

            </a>
        </div>
    </div>

    <!-- Customer Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Total Shipments -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium"><?php echo e(__('dashboard.customer.my_consignments')); ?></span>
                <span class="p-2 rounded-xl bg-zinc-100 text-zinc-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-zinc-900 tabular-nums"><?php echo e($metrics['total_shipments']); ?></div>
            <div class="text-[11px] text-zinc-400 mt-1"><?php echo e(__('dashboard.customer.total_booked_cargo')); ?></div>
        </div>

        <!-- In Transit -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium"><?php echo e(__('dashboard.customer.active_en_route')); ?></span>
                <span class="p-2 rounded-xl bg-orange-50 text-[#FF6B1A]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-[#FF6B1A] tabular-nums"><?php echo e($metrics['in_transit']); ?></div>
            <div class="text-[11px] text-zinc-400 mt-1"><?php echo e(__('dashboard.customer.live_tracking')); ?></div>
        </div>

        <!-- Delivered -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium"><?php echo e(__('dashboard.customer.fulfilled')); ?></span>
                <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-emerald-700 tabular-nums"><?php echo e($metrics['delivered']); ?></div>
            <div class="text-[11px] text-zinc-400 mt-1"><?php echo e(__('dashboard.customer.safely_received')); ?></div>
        </div>

        <!-- Delayed -->
        <div class="p-5 rounded-[24px] bg-white border border-black/[0.03] shadow-sm">
            <div class="flex items-center justify-between text-xs text-zinc-500 mb-2">
                <span class="font-medium"><?php echo e(__('dashboard.customer.delayed')); ?></span>
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <div class="text-2xl font-bold text-amber-700 tabular-nums"><?php echo e($metrics['delayed']); ?></div>
            <div class="text-[11px] text-zinc-400 mt-1"><?php echo e(__('dashboard.customer.pending_clearance')); ?></div>
        </div>
    </div>

    <!-- Customer KPI Strip & Status Doughnut -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- KPI Card -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4 flex flex-col justify-between">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-zinc-600"><?php echo e(__('dashboard.customer.sla_title')); ?></h2>
                <div class="mt-4">
                    <div class="text-[11px] font-semibold text-zinc-500 uppercase tracking-wider"><?php echo e(__('dashboard.customer.on_time_arrival_rate')); ?></div>
                    <div class="text-4xl font-extrabold text-emerald-700 mt-1 tabular-nums">
                        <?php echo e($metrics['kpis']['on_time_delivery_rate']); ?>%
                    </div>
                    <div class="text-xs text-zinc-500 mt-2">
                        <?php echo e(__('dashboard.customer.sla_desc')); ?>

                    </div>
                </div>
            </div>
            <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-600"><?php echo e(__('dashboard.customer.open_incidents_on_cargo')); ?></span>
                    <span class="font-bold <?php echo e($metrics['open_exceptions'] > 0 ? 'text-rose-700' : 'text-emerald-700'); ?>">
                        <?php echo e(__('dashboard.customer.active_count', ['count' => $metrics['open_exceptions']])); ?>

                    </span>
                </div>
                <div class="flex items-center justify-between text-xs">
                    <span class="text-zinc-600"><?php echo e(__('dashboard.customer.delay_rate')); ?></span>
                    <span class="font-bold text-amber-700"><?php echo e($metrics['kpis']['delay_rate']); ?>%</span>
                </div>
            </div>
        </div>

        <!-- Cargo Status Distribution Chart -->
        <div class="lg:col-span-2 p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider"><?php echo e(__('dashboard.customer.cargo_lifecycle_breakdown')); ?></h2>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="customerStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Active Consignments & Notifications Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Active Consignments List -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#FF6B1A]"></span>
                        <?php echo e(__('dashboard.customer.active_consignments')); ?>

                    </h2>
                    <p class="text-xs text-zinc-500 mt-0.5"><?php echo e(__('dashboard.customer.live_tracking_desc')); ?></p>
                </div>
                <a href="<?php echo e(route('shipments.index')); ?>" class="text-xs text-[#FF6B1A] hover:underline font-semibold">
                    <?php echo e(__('dashboard.admin.view_all')); ?> &rarr;
                </a>
            </div>
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $metrics['recent_shipments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sh): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4 rounded-2xl bg-zinc-50/80 border border-zinc-200/70 flex items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <a href="<?php echo e(route('shipments.show', $sh)); ?>" class="text-xs font-mono font-bold text-zinc-900 hover:text-[#FF6B1A] hover:underline">
                                    <?php echo e($sh->shipment_number); ?>

                                </a>
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider <?php echo e($sh->current_status->badgeClass()); ?>">
                                    <?php echo e($sh->current_status->label()); ?>

                                </span>
                            </div>
                            <div class="text-xs text-zinc-600">
                                <span class="font-medium text-zinc-800"><?php echo e($sh->originLocation->city); ?></span> &rarr; <span class="font-medium text-zinc-800"><?php echo e($sh->destinationLocation->city); ?></span>
                                &bull; <span class="font-mono text-zinc-500"><?php echo e($sh->carrier->name); ?></span>
                            </div>
                            <div class="text-[11px] text-zinc-500 font-mono">
                                ETA: <?php echo e($sh->estimated_arrival_at ? $sh->estimated_arrival_at->format('d M Y, H:i') : $sh->expected_arrival_at->format('d M Y, H:i')); ?>

                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider border <?php echo e($sh->delay_status->badgeClass()); ?>">
                                <?php echo e($sh->delay_status->label()); ?>

                            </span>
                            <a href="<?php echo e(route('shipments.show', $sh)); ?>" class="mt-2 block text-xs text-[#FF6B1A] hover:underline font-semibold">
                                <?php echo e(__('dashboard.customer.map_link')); ?> &rarr;
                            </a>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-xs text-zinc-500">
                        <?php echo e(__('dashboard.customer.no_shipments')); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent In-App Notifications -->
        <div class="p-6 rounded-[24px] bg-white border border-black/[0.03] shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-zinc-900 uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#FF6B1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <?php echo e(__('dashboard.customer.recent_notifications_title')); ?>

                    </h2>
                    <p class="text-xs text-zinc-500 mt-0.5"><?php echo e(__('dashboard.customer.recent_notifications_desc')); ?></p>
                </div>
                <a href="<?php echo e(route('notifications.index')); ?>" class="text-xs text-[#FF6B1A] hover:underline font-semibold">
                    <?php echo e(__('dashboard.customer.inbox_link')); ?> &rarr;
                </a>
            </div>
            <div class="space-y-3">
                <?php $__empty_1 = true; $__currentLoopData = $metrics['recent_notifications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4 rounded-2xl border <?php echo e(is_null($notif->read_at) ? 'border-orange-200 bg-orange-50/40' : 'border-zinc-200/70 bg-zinc-50/80'); ?> flex items-start justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?php echo e(is_null($notif->read_at) ? 'bg-orange-100 text-[#FF6B1A] border border-orange-200' : 'bg-zinc-200 text-zinc-700'); ?>">
                                    <?php echo e($notif->type); ?>

                                </span>
                                <?php if(is_null($notif->read_at)): ?>
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B1A] animate-ping"></span>
                                <?php endif; ?>
                            </div>
                            <p class="text-xs text-zinc-700"><?php echo e($notif->message); ?></p>
                            <span class="text-[10px] text-zinc-500 font-mono block">
                                <?php echo e($notif->created_at->diffForHumans()); ?>

                            </span>
                        </div>
                        <?php if($notif->shipment): ?>
                            <a href="<?php echo e(route('shipments.show', $notif->shipment)); ?>" class="text-xs text-[#FF6B1A] hover:underline font-semibold shrink-0">
                                <?php echo e(__('dashboard.customer.track_link')); ?> &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-xs text-zinc-500">
                        <?php echo e(__('dashboard.customer.no_notifications')); ?>

                    </div>
                <?php endif; ?>
            </div>
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

    const statusCtx = document.getElementById('customerStatusChart').getContext('2d');
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
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/dashboard/customer.blade.php ENDPATH**/ ?>