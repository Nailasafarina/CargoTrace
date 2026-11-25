<?php $__env->startSection('title', __('shipments.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900"><?php echo e(__('shipments.title')); ?></h1>
            <p class="text-sm text-slate-500 mt-1">
                <?php if(Auth::user()->isCustomer()): ?>
                    <?php echo e(__('dashboard.customer.subtitle')); ?>

                <?php else: ?>
                    <?php echo e(__('shipments.subtitle')); ?>

                <?php endif; ?>
            </p>
        </div>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', App\Models\Shipment::class)): ?>
            <a href="<?php echo e(route('shipments.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <?php echo e(__('shipments.new_shipment')); ?>

            </a>
        <?php endif; ?>
    </div>

    <!-- Advanced Multi-Parameter Filter Card -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 text-xs">
            <span class="font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <?php echo e(__('shipments.filters.server_side_title')); ?>

            </span>
            <span class="text-slate-500 font-mono"><?php echo e(__('shipments.filters.found_count', ['count' => $shipments->total()])); ?></span>
        </div>

        <form method="GET" action="<?php echo e(route('shipments.index')); ?>" class="space-y-3">
            <!-- Row 1: Core Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Search Query -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1"><?php echo e(__('shipments.filters.search_identifier')); ?></label>
                    <input
                        type="text"
                        name="search"
                        value="<?php echo e(request('search')); ?>"
                        placeholder="<?php echo e(__('shipments.filters.search_placeholder_full')); ?>"
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1"><?php echo e(__('shipments.filters.lifecycle_status')); ?></label>
                    <select name="status" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        <option value=""><?php echo e(__('shipments.filters.all_statuses')); ?></option>
                        <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($st->value); ?>" <?php echo e(request('status') === $st->value ? 'selected' : ''); ?>>
                                <?php echo e($st->label()); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Delay Status Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1"><?php echo e(__('shipments.filters.delay_state')); ?></label>
                    <select name="delay_status" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        <option value=""><?php echo e(__('shipments.filters.all_delays')); ?></option>
                        <?php $__currentLoopData = $delayStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dst): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($dst->value); ?>" <?php echo e(request('delay_status') === $dst->value ? 'selected' : ''); ?>>
                                <?php echo e($dst->label()); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Carrier Filter -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1"><?php echo e(__('shipments.filters.operating_carrier')); ?></label>
                    <select name="carrier_id" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        <option value=""><?php echo e(__('shipments.filters.all_carriers')); ?></option>
                        <?php $__currentLoopData = $carriers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $car): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($car->id); ?>" <?php echo e((string) request('carrier_id') === (string) $car->id ? 'selected' : ''); ?>>
                                [<?php echo e($car->code); ?>] <?php echo e($car->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <!-- Row 2: Location & Date Range Filters -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Origin Port -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1"><?php echo e(__('shipments.filters.origin_port')); ?></label>
                    <select name="origin_id" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        <option value=""><?php echo e(__('shipments.filters.all_origins')); ?></option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->id); ?>" <?php echo e((string) request('origin_id') === (string) $loc->id ? 'selected' : ''); ?>>
                                <?php echo e($loc->name); ?> (<?php echo e($loc->city); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Destination Port -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1"><?php echo e(__('shipments.filters.destination_port')); ?></label>
                    <select name="destination_id" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                        <option value=""><?php echo e(__('shipments.filters.all_destinations')); ?></option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc->id); ?>" <?php echo e((string) request('destination_id') === (string) $loc->id ? 'selected' : ''); ?>>
                                <?php echo e($loc->name); ?> (<?php echo e($loc->city); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <!-- Departure From -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1"><?php echo e(__('shipments.filters.departure_after')); ?></label>
                    <input
                        type="date"
                        name="date_from"
                        value="<?php echo e(request('date_from')); ?>"
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                </div>

                <!-- Departure To -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1"><?php echo e(__('shipments.filters.departure_before')); ?></label>
                    <input
                        type="date"
                        name="date_to"
                        value="<?php echo e(request('date_to')); ?>"
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                    />
                </div>
            </div>

            <!-- Row 3: Sorting & Action Controls -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[11px] text-slate-600 font-semibold"><?php echo e(__('shipments.filters.sort_by')); ?></span>
                    <select name="sort_by" class="px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
                        <option value="expected_departure_at" <?php echo e(($sortBy ?? '') === 'expected_departure_at' ? 'selected' : ''); ?>><?php echo e(__('shipments.filters.sort_dep_date')); ?></option>
                        <option value="expected_arrival_at" <?php echo e(($sortBy ?? '') === 'expected_arrival_at' ? 'selected' : ''); ?>><?php echo e(__('shipments.filters.sort_arr_eta')); ?></option>
                        <option value="shipment_number" <?php echo e(($sortBy ?? '') === 'shipment_number' ? 'selected' : ''); ?>><?php echo e(__('shipments.filters.sort_shipment_num')); ?></option>
                        <option value="created_at" <?php echo e(($sortBy ?? '') === 'created_at' ? 'selected' : ''); ?>><?php echo e(__('shipments.filters.sort_created_at')); ?></option>
                        <option value="weight_kg" <?php echo e(($sortBy ?? '') === 'weight_kg' ? 'selected' : ''); ?>><?php echo e(__('shipments.filters.sort_cargo_weight')); ?></option>
                    </select>
                    <select name="sort_dir" class="px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-2xs">
                        <option value="desc" <?php echo e(($sortDir ?? '') === 'desc' ? 'selected' : ''); ?>><?php echo e(__('shipments.filters.descending')); ?></option>
                        <option value="asc" <?php echo e(($sortDir ?? '') === 'asc' ? 'selected' : ''); ?>><?php echo e(__('shipments.filters.ascending')); ?></option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-xs font-semibold text-white shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <?php echo e(__('shipments.filters.apply_filters')); ?>

                    </button>
                    <?php if(request()->hasAny(['search', 'status', 'delay_status', 'carrier_id', 'origin_id', 'destination_id', 'date_from', 'date_to', 'sort_by', 'sort_dir'])): ?>
                        <a href="<?php echo e(route('shipments.index')); ?>" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">
                            <?php echo e(__('shipments.filters.reset_all')); ?>

                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Active Filter Badges -->
    <?php
        $activeFilters = array_filter(request()->only(['search', 'status', 'delay_status', 'carrier_id', 'origin_id', 'destination_id', 'date_from', 'date_to']));
    ?>
    <?php if(!empty($activeFilters)): ?>
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="text-slate-500 font-semibold"><?php echo e(__('shipments.filters.active_filters')); ?></span>
            <?php $__currentLoopData = $activeFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-mono text-[11px]">
                    <?php echo e($key); ?>: <strong class="text-blue-900"><?php echo e($val); ?></strong>
                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('shipments.index')); ?>" class="text-xs text-slate-500 hover:text-rose-600 underline ml-1 font-semibold">
                <?php echo e(__('shipments.filters.clear_all')); ?>

            </a>
        </div>
    <?php endif; ?>

    <!-- Shipments Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[11px] font-semibold border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">
                        <a href="<?php echo e(route('shipments.index', array_merge(request()->query(), ['sort_by' => 'shipment_number', 'sort_dir' => ($sortBy === 'shipment_number' && $sortDir === 'asc') ? 'desc' : 'asc']))); ?>" class="hover:text-slate-900 flex items-center gap-1">
                            <?php echo e(__('shipments.table.shipment_number')); ?>

                            <?php if($sortBy === 'shipment_number'): ?>
                                <span><?php echo e($sortDir === 'asc' ? '↑' : '↓'); ?></span>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3.5"><?php echo e(__('shipments.table.route_corridor')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('shipments.table.carrier_cargo')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('shipments.table.customer')); ?></th>
                    <th class="px-5 py-3.5 text-center"><?php echo e(__('shipments.table.lifecycle_status')); ?></th>
                    <th class="px-5 py-3.5 text-center">
                        <a href="<?php echo e(route('shipments.index', array_merge(request()->query(), ['sort_by' => 'expected_departure_at', 'sort_dir' => ($sortBy === 'expected_departure_at' && $sortDir === 'asc') ? 'desc' : 'asc']))); ?>" class="hover:text-slate-900 inline-flex items-center gap-1">
                            <?php echo e(__('shipments.table.schedule_delay')); ?>

                            <?php if($sortBy === 'expected_departure_at'): ?>
                                <span><?php echo e($sortDir === 'asc' ? '↑' : '↓'); ?></span>
                            <?php endif; ?>
                        </a>
                    </th>
                    <th class="px-5 py-3.5 text-right"><?php echo e(__('shipments.table.actions')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php $__empty_1 = true; $__currentLoopData = $shipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-slate-50/80 transition">
                        <!-- Number & Exceptions Flag -->
                        <td class="px-5 py-3.5">
                            <a href="<?php echo e(route('shipments.show', $shipment)); ?>" class="font-mono font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                <?php echo e($shipment->shipment_number); ?>

                            </a>
                            <?php if($shipment->exceptions->whereIn('status', ['OPEN', 'INVESTIGATING'])->isNotEmpty()): ?>
                                <div class="mt-1">
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200">
                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                        <?php echo e($shipment->exceptions->whereIn('status', ['OPEN', 'INVESTIGATING'])->first()->type); ?>

                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Route Corridor -->
                        <td class="px-5 py-3.5">
                            <div class="font-semibold text-slate-900 flex items-center gap-1.5">
                                <span><?php echo e($shipment->originLocation->city); ?></span>
                                <span class="text-slate-400">&rarr;</span>
                                <span><?php echo e($shipment->destinationLocation->city); ?></span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">
                                <?php echo e($shipment->originLocation->name); ?>

                            </div>
                        </td>

                        <!-- Carrier & Cargo -->
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-slate-800">
                                <span class="font-mono text-blue-600 font-bold"><?php echo e($shipment->carrier->code); ?></span> &middot; <?php echo e($shipment->carrier->name); ?>

                            </div>
                            <div class="text-[11px] text-slate-500 truncate max-w-[200px]" title="<?php echo e($shipment->cargo_type); ?>">
                                <?php echo e($shipment->cargo_type); ?> (<?php echo e(number_format($shipment->weight_kg, 1)); ?> kg)
                            </div>
                        </td>

                        <!-- Customer -->
                        <td class="px-5 py-3.5 text-slate-700">
                            <div class="font-semibold text-slate-900"><?php echo e($shipment->customer->name); ?></div>
                            <div class="text-[11px] text-slate-500"><?php echo e($shipment->customer->email); ?></div>
                        </td>

                        <!-- Status Badge -->
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border <?php echo e($shipment->current_status->badgeClass()); ?>">
                                <?php echo e($shipment->current_status->label()); ?>

                            </span>
                        </td>

                        <!-- Delay Status -->
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border <?php echo e($shipment->delay_status->badgeClass()); ?>" title="<?php echo e($shipment->delay_reason ?? __('shipments.show.on_schedule')); ?>">
                                <?php echo e($shipment->delay_status->label()); ?>

                                <?php if($shipment->delay_hours > 0): ?>
                                    (<?php echo e($shipment->delay_hours); ?>h)
                                <?php endif; ?>
                            </span>
                            <div class="text-[10px] text-slate-500 mt-1 font-mono">
                                ETA: <?php echo e($shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('d M Y') : $shipment->expected_arrival_at->format('d M Y')); ?>

                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-5 py-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="<?php echo e(route('shipments.show', $shipment)); ?>" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition">
                                <?php echo e(__('common.actions.view')); ?>

                            </a>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $shipment)): ?>
                                <a href="<?php echo e(route('shipments.edit', $shipment)); ?>" class="px-2.5 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold transition">
                                    <?php echo e(__('common.actions.edit')); ?>

                                </a>
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $shipment)): ?>
                                <form method="POST" action="<?php echo e(route('shipments.destroy', $shipment)); ?>" class="inline-block" onsubmit="return confirm('<?php echo e(__('shipments.show.delete_confirm', ['number' => $shipment->shipment_number])); ?>');">
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
                        <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                            <?php echo e(__('shipments.empty.description')); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination links with query string preservation -->
    <div>
        <?php echo e($shipments->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/shipments/index.blade.php ENDPATH**/ ?>