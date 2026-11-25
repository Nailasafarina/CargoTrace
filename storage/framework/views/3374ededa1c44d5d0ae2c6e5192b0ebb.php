<?php $__env->startSection('title', __('locations.details_title') . ' - ' . $location->name); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="pb-4 border-b border-slate-200 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900"><?php echo e($location->name); ?></h1>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                    <?php echo e($location->type->label()); ?>

                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1"><?php echo e($location->city); ?>, <?php echo e($location->country); ?></p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('locations.index')); ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition">&larr; <?php echo e(__('common.actions.back')); ?></a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $location)): ?>
                <a href="<?php echo e(route('locations.edit', $location)); ?>" class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold transition"><?php echo e(__('common.actions.edit')); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Metadata & Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500"><?php echo e(__('locations.departing_shipments')); ?></span>
            <div class="text-2xl font-bold text-blue-700 mt-1"><?php echo e($location->origin_shipments_count); ?></div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500"><?php echo e(__('locations.arriving_shipments')); ?></span>
            <div class="text-2xl font-bold text-emerald-700 mt-1"><?php echo e($location->destination_shipments_count); ?></div>
        </div>
        <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-sm">
            <span class="text-xs text-slate-500"><?php echo e(__('locations.milestone_events')); ?></span>
            <div class="text-2xl font-bold text-indigo-700 mt-1"><?php echo e($location->tracking_events_count); ?></div>
        </div>
    </div>

    <!-- Geographic Coordinates (WGS84 Format) -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 mb-2"><?php echo e(__('locations.coords_title')); ?></h2>
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 font-mono text-xs text-slate-800 flex items-center justify-between flex-wrap gap-2">
            <div>
                <span class="text-slate-500"><?php echo e(__('locations.fields.latitude')); ?>:</span>
                <span class="text-blue-700 font-semibold"><?php echo e($location->latitude !== null ? number_format($location->latitude, 7) : '—'); ?></span>
                <span class="text-slate-300 px-3">|</span>
                <span class="text-slate-500"><?php echo e(__('locations.fields.longitude')); ?>:</span>
                <span class="text-emerald-700 font-semibold"><?php echo e($location->longitude !== null ? number_format($location->longitude, 7) : '—'); ?></span>
            </div>
            <?php if($location->latitude !== null && $location->longitude !== null): ?>
                <a
                    href="https://www.openstreetmap.org/?mlat=<?php echo e($location->latitude); ?>&mlon=<?php echo e($location->longitude); ?>#map=12/<?php echo e($location->latitude); ?>/<?php echo e($location->longitude); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-blue-600 hover:text-blue-800 hover:underline font-semibold text-[11px] inline-flex items-center gap-1"
                >
                    <span><?php echo e(__('locations.view_osm')); ?></span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Interactive Location Map Card (Leaflet + OpenStreetMap) -->
    <?php if (isset($component)) { $__componentOriginalef01a91168a4da2e34093d402e425370 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalef01a91168a4da2e34093d402e425370 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.map','data' => ['id' => 'location-detail-map','mode' => 'single','latitude' => $location->latitude,'longitude' => $location->longitude,'zoom' => 12,'height' => '400px','title' => __('locations.map.title'),'subtitle' => __('locations.map.subtitle'),'marker' => [
            'title' => $location->name,
            'type' => $location->type->value,
            'typeLabel' => $location->type->label(),
            'city' => $location->city,
            'country' => $location->country,
        ],'editUrl' => auth()->user()?->can('update', $location) ? route('locations.edit', $location) : null,'osmUrl' => $location->latitude !== null && $location->longitude !== null ? 'https://www.openstreetmap.org/?mlat=' . $location->latitude . '&mlon=' . $location->longitude . '#map=12/' . $location->latitude . '/' . $location->longitude : null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('map'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'location-detail-map','mode' => 'single','latitude' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($location->latitude),'longitude' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($location->longitude),'zoom' => 12,'height' => '400px','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('locations.map.title')),'subtitle' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('locations.map.subtitle')),'marker' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([
            'title' => $location->name,
            'type' => $location->type->value,
            'typeLabel' => $location->type->label(),
            'city' => $location->city,
            'country' => $location->country,
        ]),'edit-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()?->can('update', $location) ? route('locations.edit', $location) : null),'osm-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($location->latitude !== null && $location->longitude !== null ? 'https://www.openstreetmap.org/?mlat=' . $location->latitude . '&mlon=' . $location->longitude . '#map=12/' . $location->latitude . '/' . $location->longitude : null)]); ?>
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
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/locations/show.blade.php ENDPATH**/ ?>