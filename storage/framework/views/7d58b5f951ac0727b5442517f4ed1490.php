<?php $__env->startSection('title', __('carriers.details_title') . ' - ' . $carrier->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-200 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900"><?php echo e($carrier->name); ?></h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                    <?php echo e($carrier->code); ?>

                </span>
                <?php if($carrier->is_active): ?>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><?php echo e(__('carriers.status.active')); ?></span>
                <?php else: ?>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200"><?php echo e(__('carriers.status.inactive')); ?></span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-slate-500 mt-1"><?php echo e(__('carriers.profile_subtitle')); ?></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('carriers.index')); ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">&larr; <?php echo e(__('common.actions.back')); ?></a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $carrier)): ?>
                <a href="<?php echo e(route('carriers.edit', $carrier)); ?>" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold transition"><?php echo e(__('common.actions.edit')); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Contact & Stats Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500"><?php echo e(__('carriers.fields.assigned_shipments')); ?></span>
            <div class="text-2xl font-bold text-slate-900 mt-1"><?php echo e($carrier->shipments_count); ?></div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500"><?php echo e(__('carriers.fields.contact_email')); ?></span>
            <div class="text-sm font-semibold text-slate-800 mt-1 break-all"><?php echo e($carrier->contact_email ?? __('carriers.not_provided')); ?></div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500"><?php echo e(__('carriers.fields.contact_phone')); ?></span>
            <div class="text-sm font-semibold text-slate-800 mt-1"><?php echo e($carrier->contact_phone ?? __('carriers.not_provided')); ?></div>
        </div>
    </div>

    <!-- Recent Shipments -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-4"><?php echo e(__('carriers.recent_shipments')); ?></h2>
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2.5"><?php echo e(__('shipments.fields.shipment_number')); ?></th>
                        <th class="px-4 py-2.5"><?php echo e(__('shipments.fields.route')); ?></th>
                        <th class="px-4 py-2.5"><?php echo e(__('shipments.fields.customer')); ?></th>
                        <th class="px-4 py-2.5 text-center"><?php echo e(__('shipments.fields.status')); ?></th>
                        <th class="px-4 py-2.5 text-right"><?php echo e(__('common.actions.view')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $recentShipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-2.5 font-mono text-blue-600 font-bold"><?php echo e($shipment->shipment_number); ?></td>
                            <td class="px-4 py-2.5 font-medium text-slate-900"><?php echo e($shipment->originLocation->city); ?> &rarr; <?php echo e($shipment->destinationLocation->city); ?></td>
                            <td class="px-4 py-2.5 text-slate-700"><?php echo e($shipment->customer->name); ?></td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold uppercase tracking-wider border <?php echo e($shipment->current_status->badgeClass()); ?>">
                                    <?php echo e($shipment->current_status->label()); ?>

                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-right">
                                <a href="<?php echo e(route('shipments.show', $shipment)); ?>" class="text-blue-600 hover:text-blue-800 hover:underline font-semibold"><?php echo e(__('common.actions.view')); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-slate-500"><?php echo e(__('carriers.no_shipments')); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/carriers/show.blade.php ENDPATH**/ ?>