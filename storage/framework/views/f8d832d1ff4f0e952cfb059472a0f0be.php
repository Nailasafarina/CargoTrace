<?php $__env->startSection('title', __('carriers.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900"><?php echo e(__('carriers.title')); ?></h1>
            <p class="text-sm text-slate-500 mt-1"><?php echo e(__('carriers.subtitle')); ?></p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Carrier::class)): ?>
            <a href="<?php echo e(route('carriers.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <?php echo e(__('carriers.add_carrier')); ?>

            </a>
        <?php endif; ?>
    </div>

    <!-- Search Bar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="<?php echo e(route('carriers.index')); ?>" class="w-full sm:w-80 flex gap-2">
            <input
                type="text"
                name="search"
                value="<?php echo e(request('search')); ?>"
                placeholder="<?php echo e(__('carriers.search_placeholder')); ?>"
                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
            />
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition cursor-pointer">
                <?php echo e(__('common.actions.filter')); ?>

            </button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('carriers.index')); ?>" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs text-slate-600 font-semibold transition"><?php echo e(__('common.actions.clear_filters')); ?></a>
            <?php endif; ?>
        </form>
        <div class="text-xs text-slate-500">
            <?php echo __('common.labels.showing_count', ['count' => '<span class="font-semibold text-slate-900">' . $carriers->total() . '</span>', 'entity' => __('common.navigation.carriers')]); ?>

        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[11px] font-semibold border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5"><?php echo e(__('carriers.table.code')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('carriers.table.carrier_name')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('carriers.table.contact_info')); ?></th>
                    <th class="px-5 py-3.5 text-center"><?php echo e(__('carriers.table.active_shipments')); ?></th>
                    <th class="px-5 py-3.5 text-center"><?php echo e(__('carriers.table.status')); ?></th>
                    <th class="px-5 py-3.5 text-right"><?php echo e(__('carriers.table.actions')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $carrier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-5 py-3.5 font-mono font-bold text-blue-600">
                            <?php echo e($carrier->code); ?>

                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-900">
                            <a href="<?php echo e(route('carriers.show', $carrier)); ?>" class="hover:text-blue-600 transition">
                                <?php echo e($carrier->name); ?>

                            </a>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            <div><?php echo e($carrier->contact_email ?? '—'); ?></div>
                            <div class="text-[11px] text-slate-400"><?php echo e($carrier->contact_phone ?? '—'); ?></div>
                        </td>
                        <td class="px-5 py-3.5 text-center font-semibold text-slate-800">
                            <?php echo e($carrier->shipments_count); ?>

                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <?php if($carrier->is_active): ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><?php echo e(__('carriers.status.active')); ?></span>
                            <?php else: ?>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200"><?php echo e(__('carriers.status.inactive')); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="<?php echo e(route('carriers.show', $carrier)); ?>" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                                <?php echo e(__('common.actions.view')); ?>

                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $carrier)): ?>
                                <a href="<?php echo e(route('carriers.edit', $carrier)); ?>" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold transition">
                                    <?php echo e(__('common.actions.edit')); ?>

                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $carrier)): ?>
                                <form method="POST" action="<?php echo e(route('carriers.destroy', $carrier)); ?>" class="inline-block" onsubmit="return confirm('<?php echo e(__('carriers.delete_confirm', ['name' => $carrier->name])); ?>');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold transition cursor-pointer">
                                        <?php echo e(__('common.actions.delete')); ?>

                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-slate-500">
                            <?php echo e(__('carriers.empty')); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        <?php echo e($carriers->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/carriers/index.blade.php ENDPATH**/ ?>