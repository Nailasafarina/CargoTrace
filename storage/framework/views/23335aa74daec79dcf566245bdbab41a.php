<?php $__env->startSection('title', __('audit.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900"><?php echo e(__('audit.title')); ?></h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-600"></span>
                    <?php echo e(__('common.badges.admin_guarded')); ?>

                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                <?php echo e(__('audit.subtitle')); ?>

            </p>
        </div>
        <div>
            <span class="text-xs font-mono text-slate-600 bg-white border border-slate-200 shadow-sm px-3.5 py-2 rounded-xl block">
                <?php echo e(__('audit.total_logged', ['total' => $auditLogs->total()])); ?>

            </span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 text-xs">
            <span class="font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <?php echo e(__('audit.filters_heading')); ?>

            </span>
            <?php if(request()->hasAny(['user_id', 'action', 'entity_type', 'date_from', 'date_to'])): ?>
                <a href="<?php echo e(route('admin.audit-logs.index')); ?>" class="text-xs text-blue-600 hover:text-blue-800 underline font-medium">
                    <?php echo e(__('common.actions.clear_filters')); ?>

                </a>
            <?php endif; ?>
        </div>

        <form method="GET" action="<?php echo e(route('admin.audit-logs.index')); ?>" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Actor Filter -->
            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1"><?php echo e(__('audit.actor')); ?></label>
                <select name="user_id" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    <option value=""><?php echo e(__('audit.all_actors')); ?></option>
                    <?php $__currentLoopData = $actors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $actor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($actor->id); ?>" <?php echo e((string) request('user_id') === (string) $actor->id ? 'selected' : ''); ?>>
                            <?php echo e($actor->name); ?> (<?php echo e(\App\Enums\Role::tryFrom($actor->role)?->label() ?? $actor->role); ?>)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Action Filter -->
            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1"><?php echo e(__('audit.action')); ?></label>
                <select name="action" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    <option value=""><?php echo e(__('audit.all_actions')); ?></option>
                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($act); ?>" <?php echo e(request('action') === $act ? 'selected' : ''); ?>>
                            <?php echo e(strtoupper($act)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Entity Type Filter -->
            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1"><?php echo e(__('audit.entity_type')); ?></label>
                <select name="entity_type" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600">
                    <option value=""><?php echo e(__('audit.all_entities')); ?></option>
                    <?php $__currentLoopData = $entityTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($ent); ?>" <?php echo e(request('entity_type') === $ent ? 'selected' : ''); ?>>
                            <?php echo e(class_basename($ent)); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-[11px] font-medium text-slate-600 mb-1"><?php echo e(__('audit.date_from')); ?></label>
                <input
                    type="date"
                    name="date_from"
                    value="<?php echo e(request('date_from')); ?>"
                    class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600"
                />
            </div>

            <!-- Date To & Submit -->
            <div class="flex items-end gap-2">
                <div class="flex-grow">
                    <label class="block text-[11px] font-medium text-slate-600 mb-1"><?php echo e(__('audit.date_to')); ?></label>
                    <input
                        type="date"
                        name="date_to"
                        value="<?php echo e(request('date_to')); ?>"
                        class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-600"
                    />
                </div>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer shrink-0">
                    <?php echo e(__('common.actions.filter')); ?>

                </button>
            </div>
        </form>
    </div>

    <!-- Audit Log Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-xs text-slate-700">
            <thead class="bg-slate-50 text-slate-600 uppercase tracking-wider text-[11px] border-b border-slate-200 font-semibold">
                <tr>
                    <th class="px-5 py-3.5"><?php echo e(__('audit.table.timestamp')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('audit.table.actor')); ?></th>
                    <th class="px-5 py-3.5 text-center"><?php echo e(__('audit.table.action')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('audit.table.target_entity')); ?></th>
                    <th class="px-5 py-3.5"><?php echo e(__('audit.table.ip_address')); ?></th>
                    <th class="px-5 py-3.5 text-right"><?php echo e(__('audit.table.diff')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <?php $__empty_1 = true; $__currentLoopData = $auditLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $actionColors = [
                            'created' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'updated' => 'bg-sky-50 text-sky-700 border-sky-200',
                            'deleted' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'status_updated' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'exception_logged' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'exception_resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'setting_updated' => 'bg-purple-50 text-purple-700 border-purple-200',
                        ];
                        $badge = $actionColors[$log->action] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                    ?>
                    <tr class="hover:bg-slate-50/80 transition">
                        <!-- Timestamp -->
                        <td class="px-5 py-3.5 font-mono text-[11px] whitespace-nowrap">
                            <span class="text-slate-800 font-semibold"><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></span>
                            <span class="text-slate-500 block text-[10px]"><?php echo e($log->created_at->diffForHumans()); ?></span>
                        </td>

                        <!-- Actor -->
                        <td class="px-5 py-3.5">
                            <?php if($log->user): ?>
                                <div class="font-medium text-slate-900"><?php echo e($log->user->name); ?></div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                    <span><?php echo e($log->user->email); ?></span>
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200"><?php echo e(\App\Enums\Role::tryFrom($log->user->role)?->label() ?? $log->user->role); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="italic text-slate-500"><?php echo e(__('audit.system_actor')); ?></span>
                            <?php endif; ?>
                        </td>

                        <!-- Action -->
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border <?php echo e($badge); ?>">
                                <?php echo e($log->action); ?>

                            </span>
                        </td>

                        <!-- Target Entity -->
                        <td class="px-5 py-3.5 font-mono text-xs">
                            <span class="text-blue-600 font-semibold"><?php echo e(class_basename($log->entity_type)); ?></span>
                            <span class="text-slate-500">#<?php echo e($log->entity_id); ?></span>
                        </td>

                        <!-- IP Address -->
                        <td class="px-5 py-3.5 font-mono text-[11px] text-slate-600">
                            <?php echo e($log->ip_address ?? '127.0.0.1'); ?>

                        </td>

                        <!-- Changes Inspector -->
                        <td class="px-5 py-3.5 text-right">
                            <?php if(!empty($log->old_values) || !empty($log->new_values)): ?>
                                <details class="group inline-block text-left">
                                    <summary class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 border border-slate-200 text-xs font-medium text-slate-700 cursor-pointer select-none transition">
                                        <?php echo __('audit.diff_modal.inspect_button'); ?>

                                    </summary>
                                    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4 group-open:flex hidden" onclick="if(event.target === this) this.parentElement.removeAttribute('open');">
                                        <div class="bg-white border border-slate-200 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto text-left" onclick="event.stopPropagation();">
                                            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                                                <div>
                                                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">
                                                        <?php echo e(__('audit.diff_modal.snapshot_title', ['entity' => class_basename($log->entity_type), 'id' => $log->entity_id])); ?>

                                                    </h3>
                                                    <p class="text-xs text-slate-500 mt-0.5"><?php echo __('audit.diff_modal.snapshot_subtitle', ['action' => '<span class="font-mono text-purple-700 font-bold uppercase">' . $log->action . '</span>', 'time' => $log->created_at->format('Y-m-d H:i:s')]); ?></p>
                                                </div>
                                                <button type="button" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 text-lg leading-none cursor-pointer" onclick="this.closest('details').removeAttribute('open');">
                                                    &times;
                                                </button>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-mono">
                                                <!-- Old Values -->
                                                <div class="space-y-1">
                                                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-700"><?php echo e(__('audit.diff_modal.previous_state')); ?></span>
                                                    <pre class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 overflow-x-auto text-[11px]"><?php echo e(json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>
                                                </div>

                                                <!-- New Values -->
                                                <div class="space-y-1">
                                                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700"><?php echo e(__('audit.diff_modal.new_state')); ?></span>
                                                    <pre class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 overflow-x-auto text-[11px]"><?php echo e(json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </details>
                            <?php else: ?>
                                <span class="text-slate-400 italic text-[11px]"><?php echo e(__('audit.diff_modal.no_state_change')); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                            <?php echo e(__('audit.empty')); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div>
        <?php echo e($auditLogs->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/admin/audit-logs/index.blade.php ENDPATH**/ ?>