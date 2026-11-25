<?php $__env->startSection('title', __('locations.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900"><?php echo e(__('locations.title')); ?></h1>
            <p class="text-sm text-slate-500 mt-1"><?php echo e(__('locations.subtitle')); ?></p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Location::class)): ?>
            <a href="<?php echo e(route('locations.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <?php echo e(__('locations.add_location')); ?>

            </a>
        <?php endif; ?>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 items-center justify-between">
        <form method="GET" action="<?php echo e(route('locations.index')); ?>" class="w-full sm:w-auto flex flex-wrap gap-2">
            <input
                type="text"
                name="search"
                value="<?php echo e(request('search')); ?>"
                placeholder="<?php echo e(__('locations.search_placeholder')); ?>"
                class="px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs w-full sm:w-60"
            />
            <select name="type" class="px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                <option value=""><?php echo e(__('locations.all_types')); ?></option>
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->value); ?>" <?php echo e(request('type') === $type->value ? 'selected' : ''); ?>>
                        <?php echo e($type->label()); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition cursor-pointer">
                <?php echo e(__('common.actions.filter')); ?>

            </button>
            <?php if(request('search') || request('type')): ?>
                <a href="<?php echo e(route('locations.index')); ?>" class="px-2.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs text-slate-600 font-semibold transition"><?php echo e(__('common.actions.clear_filters')); ?></a>
            <?php endif; ?>
        </form>
        <div class="text-xs text-slate-500">
            <?php echo __('common.labels.showing_count', ['count' => '<span class="font-semibold text-slate-900">' . $locations->total() . '</span>', 'entity' => __('common.navigation.locations')]); ?>

        </div>
    </div>

    <!-- Summary Global Network Map (Bonus B) -->
    <?php if(!empty($mapMarkers)): ?>
        <?php if (isset($component)) { $__componentOriginalef01a91168a4da2e34093d402e425370 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalef01a91168a4da2e34093d402e425370 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.map','data' => ['id' => 'locations-summary-network-map','mode' => 'multi','markers' => $mapMarkers,'height' => '340px','title' => __('locations.map.summary_title'),'subtitle' => __('locations.map.summary_subtitle')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('map'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'locations-summary-network-map','mode' => 'multi','markers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($mapMarkers),'height' => '340px','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('locations.map.summary_title')),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('locations.map.summary_subtitle'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalef01a91168a4da2e34093d402e425370)): ?>
<?php $attributes = $__attributesOriginalef01a91168a4da2e34093d402e425370; ?>
<?php unset($__attributesOriginalef01a91168a4da2e34093d402e425370); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalef01a91168a4da2e34093d402e425370)): ?>
<?php $component = $__componentOriginalef01a91168a4da2e34093d402e425370; ?>
<?php unset($__componentOriginalef01a91168a4da2e34093d402e425370); ?>
<?php endif; ?>
    <?php endif; ?>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[11px] font-semibold border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5"><?php echo e(__('locations.table.hub_name')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('locations.table.type')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('locations.table.city_country')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('locations.table.coordinates')); ?></th>
                    <th class="px-5 py-3.5 text-center"><?php echo e(__('locations.table.shipments_ratio')); ?></th>
                    <th class="px-5 py-3.5 text-right"><?php echo e(__('locations.table.actions')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-5 py-3.5 font-semibold text-slate-900">
                            <a href="<?php echo e(route('locations.show', $loc)); ?>" class="hover:text-blue-600 transition">
                                <?php echo e($loc->name); ?>

                            </a>
                        </td>
                        <td class="px-5 py-3.5">
                            <?php
                                $typeStyles = [
                                    'port' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'airport' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'warehouse' => 'bg-amber-50 text-amber-800 border-amber-200',
                                    'city' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                ];
                                $style = $typeStyles[$loc->type->value] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border <?php echo e($style); ?>">
                                <?php echo e($loc->type->label()); ?>

                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="text-slate-900 font-semibold"><?php echo e($loc->city); ?></span>,
                            <span class="text-slate-500"><?php echo e($loc->country); ?></span>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-[11px] text-slate-500">
                            <?php echo e(number_format($loc->latitude, 6)); ?>, <?php echo e(number_format($loc->longitude, 6)); ?>

                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="text-blue-600 font-bold"><?php echo e($loc->origin_shipments_count); ?></span>
                            <span class="text-slate-300">/</span>
                            <span class="text-emerald-700 font-bold"><?php echo e($loc->destination_shipments_count); ?></span>
                        </td>
                        <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="<?php echo e(route('locations.show', $loc)); ?>" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                                <?php echo e(__('common.actions.view')); ?>

                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $loc)): ?>
                                <a href="<?php echo e(route('locations.edit', $loc)); ?>" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold transition">
                                    <?php echo e(__('common.actions.edit')); ?>

                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $loc)): ?>
                                <form method="POST" action="<?php echo e(route('locations.destroy', $loc)); ?>" class="inline-block" onsubmit="return confirm('<?php echo e(__('locations.delete_confirm', ['name' => $loc->name])); ?>');">
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
                            <?php echo e(__('locations.empty')); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        <?php echo e($locations->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/locations/index.blade.php ENDPATH**/ ?>