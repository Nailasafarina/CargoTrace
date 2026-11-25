<?php $__env->startSection('title', __('shipments.details_title') . ' ' . $shipment->shipment_number); ?>

<?php $__env->startPush('styles'); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header with Status Badges -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-extrabold font-mono tracking-tight text-slate-900">
                    <?php echo e($shipment->shipment_number); ?>

                </h1>

                <!-- Current Status Badge -->
                <?php
                    $statusColors = [
                        'CREATED' => 'bg-slate-100 text-slate-700 border-slate-300',
                        'BOOKED' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'PICKED_UP' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'IN_TRANSIT' => 'bg-amber-50 text-amber-800 border-amber-200',
                        'DELIVERED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'CANCELLED' => 'bg-rose-50 text-rose-700 border-rose-200',
                    ];
                    $badge = $statusColors[$shipment->current_status->value] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                ?>
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border <?php echo e($badge); ?>">
                    <?php echo e($shipment->current_status->label()); ?>

                </span>

                <!-- Delay Status Badge -->
                <span class="px-3 py-1 rounded-full text-xs font-semibold <?php echo e($shipment->delay_status->badgeClasses()); ?>" title="<?php echo e($shipment->delay_reason ?? __('shipments.show.on_schedule')); ?>">
                    <?php if($shipment->delay_status === \App\Enums\DelayStatus::OnTime): ?>
                        <?php echo e(__('shipments.show.on_schedule')); ?>

                    <?php else: ?>
                        <?php echo e($shipment->delay_status->label()); ?> (<?php echo e($shipment->delay_hours); ?>h)
                    <?php endif; ?>
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1.5">
                <?php echo e(__('shipments.show.contracted_carrier')); ?> <span class="text-slate-800 font-semibold"><?php echo e($shipment->carrier->name); ?></span> &bull; <?php echo e(__('shipments.show.booked_by')); ?> <?php echo e($shipment->creator->name); ?>

            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?php echo e(route('shipments.index')); ?>" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 transition"><?php echo e(__('shipments.show.back_shipments')); ?></a>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $shipment)): ?>
                <a href="<?php echo e(route('shipments.edit', $shipment)); ?>" class="px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-semibold transition">
                    <?php echo e(__('shipments.show.edit_booking')); ?>

                </a>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $shipment)): ?>
                <form method="POST" action="<?php echo e(route('shipments.destroy', $shipment)); ?>" onsubmit="return confirm('<?php echo e(__('shipments.show.delete_confirm', ['number' => $shipment->shipment_number])); ?>');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold transition cursor-pointer">
                        <?php echo e(__('common.actions.delete')); ?>

                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if($shipment->delay_reason): ?>
        <div class="p-4 rounded-2xl bg-white border border-slate-200 text-xs text-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold uppercase bg-blue-50 text-blue-700 border border-blue-200 shrink-0">
                    <?php echo e(__('shipments.show.eta_evaluation_sla')); ?>

                </span>
                <span class="text-slate-800 font-mono text-xs font-medium"><?php echo e($shipment->delay_reason); ?></span>
            </div>
            <span class="text-[11px] text-slate-500 font-mono shrink-0"><?php echo e(__('shipments.show.active_sla_rule')); ?></span>
        </div>
    <?php endif; ?>

    <!-- Active Exception Alert -->
    <?php
        $activeException = $shipment->exceptions->firstWhere('status.value', 'OPEN')
            ?? $shipment->exceptions->firstWhere('status.value', 'INVESTIGATING');
    ?>

    <?php if($activeException): ?>
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="p-2 rounded-xl bg-amber-100 text-amber-700 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-800"><?php echo e(__('shipments.show.active_operational_exception')); ?></span>
                        <span class="font-bold text-slate-900 text-sm"><?php echo e($activeException->type->label()); ?></span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-amber-100 text-amber-800 border border-amber-300">
                            <?php echo e(__('shipments.show.severity')); ?> <?php echo e($activeException->severity->value); ?>

                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-white text-slate-700 border border-slate-200">
                            <?php echo e($activeException->status->label()); ?>

                        </span>
                    </div>
                    <p class="text-xs text-amber-900 mt-1.5 leading-relaxed">
                        <?php echo e($activeException->description); ?>

                    </p>
                    <div class="text-[11px] text-amber-800 mt-2 font-medium">
                        <?php echo e(__('shipments.show.detected')); ?> <?php echo e($activeException->detected_at->format('d M Y, H:i')); ?> UTC &bull; <?php echo e(__('shipments.show.logged_in_telemetry')); ?>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Current Location & Live Position Telemetry Card -->
    <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 shrink-0">
                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600"><?php echo e(__('shipments.show.current_position_hub')); ?></span>
                <div class="text-lg font-bold text-slate-900 mt-0.5">
                    <?php echo e($currentLocation->name ?? __('shipments.show.port_of_departure')); ?>

                </div>
                <div class="text-xs text-slate-500">
                    <?php echo e($currentLocation->city ?? ''); ?>, <?php echo e($currentLocation->country ?? ''); ?>

                    <?php if($latestEvent && $latestEvent->latitude && $latestEvent->longitude): ?>
                        &bull; <span class="font-mono text-slate-700">WGS84: <?php echo e(number_format($latestEvent->latitude, 6)); ?>, <?php echo e(number_format($latestEvent->longitude, 6)); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="text-right sm:border-l sm:border-slate-200 sm:pl-6">
            <span class="text-[10px] uppercase tracking-wider text-slate-500 block"><?php echo e(__('shipments.show.last_milestone_update')); ?></span>
            <span class="text-xs font-mono font-semibold text-slate-800 mt-0.5 block">
                <?php echo e($latestEvent ? $latestEvent->occurred_at->format('d M Y, H:i') . ' UTC' : __('shipments.show.pending_departure')); ?>

            </span>
            <span class="text-[11px] text-blue-600 font-semibold block mt-0.5">
                <?php echo e($latestEvent ? $latestEvent->event_type->label() : __('shipments.show.booking_initiated')); ?>

            </span>
        </div>
    </div>

    <!-- Route Overview Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Route -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600"><?php echo e(__('shipments.show.corridor_routing_waypoints')); ?></h2>

            <div class="flex items-center justify-between relative">
                <!-- Origin -->
                <div class="text-left z-10">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600"><?php echo e(__('shipments.show.origin_port')); ?></span>
                    <div class="text-lg font-bold text-slate-900 mt-0.5"><?php echo e($shipment->originLocation->city); ?></div>
                    <div class="text-xs text-slate-500"><?php echo e($shipment->originLocation->name); ?></div>
                    <div class="text-[11px] text-slate-600 mt-1 font-mono">
                        <?php echo e(__('shipments.show.etd')); ?> <?php echo e($shipment->expected_departure_at->format('d M Y, H:i')); ?>

                    </div>
                </div>

                <!-- Mid-route connector -->
                <div class="flex-grow mx-6 flex flex-col items-center">
                    <div class="w-full border-t border-dashed border-slate-300 relative flex items-center justify-center">
                        <div class="w-7 h-7 rounded-full bg-slate-50 border border-slate-300 flex items-center justify-center text-blue-600 -mt-0.5 shadow-2xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </div>
                    </div>
                    <span class="text-[10px] text-slate-500 uppercase tracking-widest mt-1 font-medium"><?php echo e(__('shipments.show.ocean_transit_corridor')); ?></span>
                </div>

                <!-- Destination -->
                <div class="text-right z-10">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700"><?php echo e(__('shipments.show.destination_port')); ?></span>
                    <div class="text-lg font-bold text-slate-900 mt-0.5"><?php echo e($shipment->destinationLocation->city); ?></div>
                    <div class="text-xs text-slate-500"><?php echo e($shipment->destinationLocation->name); ?></div>
                    <div class="text-[11px] text-slate-600 mt-1 font-mono">
                        <?php echo e(__('shipments.show.eta')); ?> <?php echo e($shipment->estimated_arrival_at ? $shipment->estimated_arrival_at->format('d M Y, H:i') : $shipment->expected_arrival_at->format('d M Y, H:i')); ?>

                    </div>
                </div>
            </div>

            <!-- Specs Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-4 border-t border-slate-100 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[11px]"><?php echo e(__('shipments.show.commodity')); ?></span>
                    <span class="font-semibold text-slate-800 mt-0.5 block truncate" title="<?php echo e($shipment->cargo_type); ?>"><?php echo e($shipment->cargo_type); ?></span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[11px]"><?php echo e(__('shipments.show.total_weight')); ?></span>
                    <span class="font-semibold text-slate-800 mt-0.5 block font-mono"><?php echo e(number_format($shipment->weight_kg, 2)); ?> kg</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[11px]"><?php echo e(__('shipments.show.volume')); ?></span>
                    <span class="font-semibold text-slate-800 mt-0.5 block font-mono"><?php echo e(number_format($shipment->volume_cbm, 2)); ?> CBM</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-500 block text-[11px]"><?php echo e(__('shipments.show.carrier')); ?></span>
                    <span class="font-semibold text-blue-600 mt-0.5 block font-mono">[<?php echo e($shipment->carrier->code); ?>]</span>
                </div>
            </div>
        </div>

        <!-- Consignee Profile Card -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-600"><?php echo e(__('shipments.show.consignee_ownership')); ?></h2>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700"><?php echo e(__('shipments.show.consignee_account')); ?></span>
                <div class="text-sm font-bold text-slate-900 mt-1"><?php echo e($shipment->customer->name); ?></div>
                <div class="text-xs text-slate-500 mt-0.5"><?php echo e($shipment->customer->email); ?></div>
            </div>
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600"><?php echo e(__('shipments.show.operating_carrier')); ?></span>
                <div class="text-sm font-bold text-slate-900 mt-1"><?php echo e($shipment->carrier->name); ?></div>
                <div class="text-xs text-slate-500 mt-0.5"><?php echo e($shipment->carrier->contact_email ?? __('shipments.show.standard_dispatch')); ?></div>
            </div>
        </div>
    </div>

    <!-- Maritime Corridor Map & Live Meteorological Telemetry -->
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <?php echo e(__('shipments.show.interactive_map_title')); ?>

                </h2>
                <p class="text-xs text-slate-500 mt-0.5"><?php echo e(__('shipments.show.interactive_map_desc')); ?></p>
            </div>
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    id="corridor-reset-zoom-btn"
                    class="px-3 py-1 rounded-full text-[11px] font-semibold bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-700 shadow-2xs transition cursor-pointer"
                >
                    Reset Corridor View
                </button>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-semibold bg-white border border-zinc-200 text-zinc-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B1A]"></span> <?php echo e(__('shipments.show.osm_tiles')); ?>

                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Map Container (3 Cols) -->
            <div class="lg:col-span-3 rounded-2xl bg-white border border-slate-200 overflow-hidden shadow-sm relative min-h-[420px] flex flex-col">
                <?php if($mapData['has_coordinates']): ?>
                    <div id="shipment-corridor-map" class="w-full flex-grow min-h-[420px] z-0"></div>
                <?php else: ?>
                    <div class="w-full flex-grow flex flex-col items-center justify-center p-8 text-center bg-slate-50 min-h-[320px]">
                        <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <h4 class="text-sm font-bold text-slate-900"><?php echo e(__('shipments.show.coords_unavailable_title')); ?></h4>
                        <p class="text-xs text-slate-500 max-w-sm mt-1 leading-relaxed">
                            <?php echo e(__('shipments.show.coords_unavailable_desc')); ?>

                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Weather Card (1 Col) -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200 flex flex-col justify-between shadow-sm space-y-4">
                <div>
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                        <span class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/></svg>
                            <?php echo e(__('shipments.show.current_weather')); ?>

                        </span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase <?php echo e($weather['available'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600'); ?>">
                            <?php echo e($weather['available'] ? __('shipments.show.live') : __('shipments.show.offline')); ?>

                        </span>
                    </div>

                    <div class="mt-4">
                        <span class="text-[10px] uppercase tracking-wider text-slate-500 block font-semibold"><?php echo e(__('shipments.show.verified_hub_loc')); ?></span>
                        <div class="text-sm font-bold text-slate-900 mt-0.5"><?php echo e($currentLocation->name ?? __('shipments.show.port_of_departure')); ?></div>
                        <div class="text-xs text-slate-500"><?php echo e($currentLocation->city ?? ''); ?>, <?php echo e($currentLocation->country ?? ''); ?></div>
                    </div>

                    <?php if($weather['available']): ?>
                        <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                            <div class="flex items-baseline justify-between">
                                <span class="text-2xl font-bold font-mono text-slate-900"><?php echo e($weather['temperature']); ?>°C</span>
                                <span class="text-xs font-semibold text-blue-600"><?php echo e($weather['condition']); ?></span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200 text-[11px]">
                                <div>
                                    <span class="text-slate-500 block"><?php echo e(__('shipments.show.wind_velocity')); ?></span>
                                    <span class="font-mono text-slate-800 font-semibold"><?php echo e($weather['wind_speed']); ?> km/h</span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block"><?php echo e(__('shipments.show.humidity')); ?></span>
                                    <span class="font-mono text-slate-800 font-semibold"><?php echo e($weather['humidity']); ?>%</span>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
                            <p class="text-xs text-slate-500 leading-relaxed"><?php echo e($weather['message'] ?? __('shipments.show.weather_unavailable')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mandatory Non-Causal Contextual Disclaimer -->
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[10px] text-amber-900 leading-relaxed">
                    <strong class="font-bold uppercase tracking-wider block text-amber-800 text-[9px] mb-0.5"><?php echo e(__('shipments.show.maritime_context_disclaimer')); ?></strong>
                    <?php echo e($weather['disclaimer']); ?>

                </div>
            </div>
        </div>
    </div>

    <!-- Chronological Timeline Section -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div>
                <h2 class="text-base font-bold text-slate-900"><?php echo e(__('shipments.show.timeline_title')); ?></h2>
                <p class="text-xs text-slate-500 mt-0.5"><?php echo e(__('shipments.show.timeline_desc')); ?></p>
            </div>
            <span class="text-xs text-slate-500 font-mono"><?php echo e(__('shipments.show.milestones_logged', ['count' => $shipment->trackingEvents->count()])); ?></span>
        </div>

        <div class="relative border-l-2 border-slate-200 ml-4 space-y-6 pt-2">
            <?php $__empty_1 = true; $__currentLoopData = $shipment->trackingEvents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="relative pl-7">
                    <!-- Milestone Marker Node -->
                    <?php if($event->is_exception): ?>
                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-rose-500 border-2 border-white shadow-sm"></div>
                    <?php else: ?>
                        <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-blue-600 border-2 border-white shadow-sm"></div>
                    <?php endif; ?>

                    <!-- Event Card Container -->
                    <div class="p-4 rounded-xl border <?php echo e($event->is_exception ? 'bg-rose-50/50 border-rose-200 text-rose-900' : 'bg-slate-50 border-slate-200 text-slate-800'); ?>">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold uppercase tracking-wider <?php echo e($event->is_exception ? 'text-rose-700' : 'text-blue-600'); ?>">
                                    <?php echo e($event->event_type->label()); ?>

                                </span>
                                <?php if($event->location): ?>
                                    <span class="text-xs font-semibold text-slate-800">&bull; <?php echo e($event->location->name); ?> (<?php echo e($event->location->city); ?>, <?php echo e($event->location->country); ?>)</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-[11px] font-mono text-slate-500">
                                <?php echo e($event->occurred_at->format('d M Y, H:i')); ?> UTC
                            </span>
                        </div>

                        <p class="text-xs mt-2 leading-relaxed <?php echo e($event->is_exception ? 'text-rose-800' : 'text-slate-600'); ?>">
                            <?php echo e($event->description); ?>

                        </p>

                        <?php if($event->latitude && $event->longitude): ?>
                            <div class="mt-2 text-[11px] font-mono text-slate-500 flex items-center gap-3">
                                <span>GPS: <?php echo e(number_format($event->latitude, 6)); ?>, <?php echo e(number_format($event->longitude, 6)); ?></span>
                                <a
                                    href="https://www.openstreetmap.org/?mlat=<?php echo e($event->latitude); ?>&mlon=<?php echo e($event->longitude); ?>#map=12/<?php echo e($event->latitude); ?>/<?php echo e($event->longitude); ?>"
                                    target="_blank"
                                    rel="noreferrer"
                                    class="text-blue-600 hover:text-blue-800 hover:underline font-semibold"
                                >
                                    <?php echo e(__('shipments.show.open_in_osm')); ?>

                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="pl-6 text-xs text-slate-500">
                    <?php echo e(__('shipments.show.no_milestones')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Form: Add Tracking Event (Admin & Operator Only) -->
    <?php if(Auth::user()->isAdmin() || Auth::user()->isOperator()): ?>
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <h2 class="text-base font-bold text-slate-900"><?php echo e(__('shipments.show.log_milestone_title')); ?></h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        <?php echo e(__('shipments.show.log_milestone_desc')); ?>

                    </p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                    <?php echo e(__('shipments.show.operator_console')); ?>

                </span>
            </div>

            <?php if($shipment->current_status === \App\Enums\ShipmentStatus::Delivered): ?>
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span><?php echo e(__('shipments.show.delivered_terminal_notice')); ?></span>
                </div>
            <?php else: ?>
                <form method="POST" action="<?php echo e(route('shipments.events.store', $shipment)); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Target Status -->
                        <div>
                            <label for="status" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.target_lifecycle_status')); ?></label>
                            <select id="status" name="status" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                                <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($st->value); ?>" <?php echo e(old('status', $shipment->current_status->value) === $st->value ? 'selected' : ''); ?>>
                                        <?php echo e($st->label()); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600 mt-1 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Event Type -->
                        <div>
                            <label for="event_type" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.milestone_event_type')); ?></label>
                            <select id="event_type" name="event_type" required class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                                <?php $__currentLoopData = $eventTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $et): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($et->value); ?>" <?php echo e(old('event_type') === $et->value ? 'selected' : ''); ?>>
                                        <?php echo e($et->label()); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['event_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600 mt-1 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Location -->
                        <div>
                            <label for="location_id" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.waypoint_hub_port')); ?></label>
                            <select id="location_id" name="location_id" class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs">
                                <option value=""><?php echo e(__('shipments.show.select_waypoint')); ?></option>
                                <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($loc->id); ?>" <?php echo e(old('location_id') == $loc->id ? 'selected' : ''); ?>>
                                        <?php echo e($loc->name); ?> (<?php echo e($loc->city); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['location_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600 mt-1 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Occurred At -->
                        <div>
                            <label for="occurred_at" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.event_timestamp')); ?></label>
                            <input
                                type="datetime-local"
                                id="occurred_at"
                                name="occurred_at"
                                value="<?php echo e(old('occurred_at', now()->format('Y-m-d\TH:i'))); ?>"
                                required
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                            />
                            <?php $__errorArgs = ['occurred_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600 mt-1 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Latitude -->
                        <div>
                            <label for="latitude" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.latitude_opt')); ?></label>
                            <input
                                type="number"
                                step="0.0000001"
                                id="latitude"
                                name="latitude"
                                value="<?php echo e(old('latitude')); ?>"
                                placeholder="e.g. 1.2644120"
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                            />
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label for="longitude" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.longitude_opt')); ?></label>
                            <input
                                type="number"
                                step="0.0000001"
                                id="longitude"
                                name="longitude"
                                value="<?php echo e(old('longitude')); ?>"
                                placeholder="e.g. 103.8222180"
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                            />
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.milestone_desc')); ?></label>
                        <textarea
                            id="description"
                            name="description"
                            rows="2"
                            required
                            placeholder="<?php echo e(__('shipments.show.milestone_desc_placeholder')); ?>"
                            class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 shadow-2xs"
                        ><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-rose-600 mt-1 font-medium"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Is Exception Flag -->
                    <div>
                        <label class="flex items-center gap-2 text-xs text-slate-700 cursor-pointer">
                            <input
                                type="checkbox"
                                name="is_exception"
                                value="1"
                                <?php echo e(old('is_exception') ? 'checked' : ''); ?>

                                class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500/20"
                            />
                            <?php echo e(__('shipments.show.mark_exception_event')); ?>

                        </label>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button
                            type="submit"
                            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition cursor-pointer"
                        >
                            <?php echo e(__('shipments.show.record_milestone_button')); ?>

                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Operational Exceptions & Incident Management -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <?php echo e(__('shipments.show.exceptions_title', ['count' => $shipment->exceptions->count()])); ?>

                </h2>
                <p class="text-xs text-slate-500 mt-0.5"><?php echo e(__('shipments.show.exceptions_subtitle')); ?></p>
            </div>
            <?php if($shipment->exceptions->whereIn('status.value', ['OPEN', 'INVESTIGATING'])->isNotEmpty()): ?>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-amber-50 text-amber-800 border border-amber-200">
                    <?php echo e(__('shipments.show.active_disruptions')); ?>

                </span>
            <?php endif; ?>
        </div>

        <!-- Exceptions List -->
        <div class="space-y-4">
            <?php $__empty_1 = true; $__currentLoopData = $shipment->exceptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exception): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-white text-slate-800 border border-slate-300">
                                <?php echo e($exception->type->label()); ?>

                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo e($exception->severity->badgeClasses()); ?>">
                                <?php echo e(__('shipments.show.severity')); ?> <?php echo e($exception->severity->value); ?>

                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?php echo e($exception->status->badgeClasses()); ?>">
                                <?php echo e(__('common.labels.status')); ?>: <?php echo e($exception->status->label()); ?>

                            </span>
                        </div>
                        <div class="text-[11px] text-slate-500 font-mono">
                            <?php echo e(__('shipments.show.detected')); ?> <?php echo e($exception->detected_at->format('d M Y, H:i')); ?>

                            <?php if($exception->resolved_at): ?>
                                &bull; <span class="text-emerald-700 font-semibold"><?php echo e(__('shipments.show.resolved_at', ['time' => $exception->resolved_at->format('d M Y, H:i')])); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-xs text-slate-700 whitespace-pre-line leading-relaxed">
                        <?php echo e($exception->description); ?>

                    </div>

                    <!-- Operational State Transition Controls (Operator / Admin Only) -->
                    <?php if((auth()->user()->isAdmin() || auth()->user()->isOperator()) && $exception->status !== \App\Enums\ExceptionStatus::Resolved): ?>
                        <div class="pt-3 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
                            <span class="text-[11px] text-slate-600 font-medium"><?php echo e(__('shipments.show.update_incident_lifecycle')); ?></span>
                            <div class="flex items-center gap-2">
                                <?php if($exception->status === \App\Enums\ExceptionStatus::Open): ?>
                                    <form method="POST" action="<?php echo e(route('exceptions.status.update', $exception)); ?>">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="status" value="<?php echo e(\App\Enums\ExceptionStatus::Investigating->value); ?>">
                                        <button type="submit" class="px-3 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold transition cursor-pointer">
                                            <?php echo e(__('shipments.show.mark_investigating')); ?>

                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Resolve Form -->
                                <form method="POST" action="<?php echo e(route('exceptions.status.update', $exception)); ?>" class="flex items-center gap-2">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <input type="hidden" name="status" value="<?php echo e(\App\Enums\ExceptionStatus::Resolved->value); ?>">
                                    <input type="text" name="resolution_notes" placeholder="<?php echo e(__('shipments.show.resolution_notes_placeholder')); ?>" required
                                        class="px-3 py-1 rounded-lg bg-white border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-emerald-500 w-64 shadow-2xs">
                                    <button type="submit" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold transition cursor-pointer shadow-2xs">
                                        <?php echo e(__('shipments.show.resolve_and_clear')); ?>

                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                    <?php echo e(__('shipments.show.no_exceptions')); ?>

                </div>
            <?php endif; ?>
        </div>

        <!-- Log New Exception Form (Admin / Operator Only) -->
        <?php if(auth()->user()->isAdmin() || auth()->user()->isOperator()): ?>
            <div class="pt-4 border-t border-slate-200">
                <details class="group">
                    <summary class="flex items-center justify-between cursor-pointer text-xs font-bold uppercase tracking-wider text-amber-700 hover:text-amber-800">
                        <span><?php echo e(__('shipments.show.flag_new_exception')); ?></span>
                        <span class="text-slate-400 group-open:rotate-180 transition">&darr;</span>
                    </summary>
                    <form action="<?php echo e(route('shipments.exceptions.store', $shipment)); ?>" method="POST" class="mt-4 p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                        <?php echo csrf_field(); ?>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Exception Type -->
                            <div>
                                <label for="exc_type" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.exception_category')); ?></label>
                                <select id="exc_type" name="type" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-2xs">
                                    <?php $__currentLoopData = \App\Enums\ExceptionType::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $typeCase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($typeCase->value); ?>"><?php echo e($typeCase->label()); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Severity -->
                            <div>
                                <label for="exc_severity" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.severity_level')); ?></label>
                                <select id="exc_severity" name="severity" required class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-2xs">
                                    <?php $__currentLoopData = \App\Enums\ExceptionSeverity::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $severityCase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($severityCase->value); ?>" <?php echo e($severityCase === \App\Enums\ExceptionSeverity::High ? 'selected' : ''); ?>>
                                            <?php echo e($severityCase->label()); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                            <!-- Detected At -->
                            <div>
                                <label for="exc_detected_at" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.detected_at_label')); ?></label>
                                <input type="datetime-local" id="exc_detected_at" name="detected_at" value="<?php echo e(now()->format('Y-m-d\TH:i')); ?>"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-2xs">
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="exc_description" class="block text-xs font-semibold text-slate-700 mb-1"><?php echo e(__('shipments.show.incident_desc_label')); ?></label>
                            <textarea id="exc_description" name="description" rows="2" required placeholder="<?php echo e(__('shipments.show.incident_desc_placeholder')); ?>"
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 shadow-2xs"></textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs shadow-sm transition cursor-pointer">
                                <?php echo e(__('shipments.show.log_exception_button')); ?>

                            </button>
                        </div>
                    </form>
                </details>
            </div>
        <?php endif; ?>
    </div>

    <!-- Cargo Manifest Items Table -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider"><?php echo e(__('shipments.show.manifest_title', ['count' => $shipment->items->count()])); ?></h2>
            <span class="text-xs text-slate-500"><?php echo e(__('shipments.show.total_weight')); ?>: <strong class="text-slate-900 font-mono"><?php echo e(number_format($shipment->weight_kg, 2)); ?> kg</strong></span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-600 uppercase text-[10px] font-semibold border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-2.5"><?php echo e(__('shipments.show.col_number')); ?></th>
                        <th class="px-4 py-2.5"><?php echo e(__('shipments.show.col_description')); ?></th>
                        <th class="px-4 py-2.5 text-center"><?php echo e(__('shipments.show.col_quantity')); ?></th>
                        <th class="px-4 py-2.5 text-right"><?php echo e(__('shipments.show.col_net_weight')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $shipment->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="px-4 py-3 font-mono text-slate-500"><?php echo e($idx + 1); ?></td>
                            <td class="px-4 py-3 font-semibold text-slate-900"><?php echo e($item->description); ?></td>
                            <td class="px-4 py-3 text-center font-mono font-semibold text-slate-700"><?php echo e(number_format($item->quantity)); ?></td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-slate-800"><?php echo e(number_format($item->weight_kg, 2)); ?> kg</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-slate-500"><?php echo e(__('shipments.show.no_line_items')); ?></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapData = <?php echo json_encode($mapData, 15, 512) ?>;

    if (!mapData.has_coordinates || !document.getElementById('shipment-corridor-map')) {
        return;
    }

    const LRef = window.L;
    if (!LRef) return;

    // Initialize Leaflet Map with buttery smooth fractional zoom
    const map = LRef.map('shipment-corridor-map', {
        zoomControl: true,
        scrollWheelZoom: true,
        zoomSnap: 0.25,
        zoomDelta: 0.5,
        wheelPxPerZoomLevel: 60,
        wheelDebounceTime: 20,
        zoomAnimation: true,
        fadeAnimation: true,
        markerZoomAnimation: true,
        inertia: true,
        inertiaDeceleration: 3000
    });

    // Add OpenStreetMap Tile Layer with keepBuffer for smooth zooming
    LRef.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        keepBuffer: 4,
        updateWhenZooming: false,
        updateWhenIdle: false,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" class="text-[#FF6B1A] hover:underline font-medium">OpenStreetMap</a> contributors'
    }).addTo(map);

    const latlngs = [];
    const bounds = [];

    function createMapPin(bgColor, labelText, isPulse = false) {
        const pulseHtml = isPulse
            ? `<span style="background-color: ${bgColor};" class="absolute -inset-1.5 rounded-full opacity-35 animate-ping"></span>`
            : '';
        return LRef.divIcon({
            className: 'custom-leaflet-marker',
            html: `<div class="relative flex items-center justify-center">
                ${pulseHtml}
                <div style="background-color: ${bgColor};" class="w-7 h-7 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-[10px] font-bold text-white font-mono transition-transform hover:scale-110">${labelText}</div>
            </div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -14]
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // 1. Origin Marker
    if (mapData.origin) {
        const originPoint = [mapData.origin.lat, mapData.origin.lng];
        latlngs.push(originPoint);
        bounds.push(originPoint);

        const originMarker = LRef.marker(originPoint, {
            icon: createMapPin('#18181B', 'O', false)
        }).addTo(map);

        originMarker.bindPopup(`
            <div class="text-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#FF884D] block">${escapeHtml(window.i18n?.leaflet?.origin || 'Port of Origin')}</span>
                <strong class="text-white text-sm block">${escapeHtml(mapData.origin.name)}</strong>
                <span class="text-zinc-300 block">${escapeHtml(mapData.origin.city)}, ${escapeHtml(mapData.origin.country)}</span>
                <span class="font-mono text-[10px] text-zinc-400 block">${escapeHtml(window.i18n?.leaflet?.coordinates || 'GPS')}: ${mapData.origin.lat.toFixed(4)}, ${mapData.origin.lng.toFixed(4)}</span>
            </div>
        `);

        originMarker.on('click', () => {
            map.flyTo(originPoint, Math.max(map.getZoom(), 6), { animate: true, duration: 0.85, easeLinearity: 0.25 });
        });
    }

    // 2. Waypoint Milestones Markers
    if (mapData.waypoints && mapData.waypoints.length > 0) {
        mapData.waypoints.forEach((wp, index) => {
            const wpPoint = [wp.lat, wp.lng];
            latlngs.push(wpPoint);
            bounds.push(wpPoint);

            const isLatest = index === mapData.waypoints.length - 1;
            const color = wp.is_exception ? '#EF4444' : '#FF6B1A';
            const marker = LRef.marker(wpPoint, {
                icon: createMapPin(color, (index + 1).toString(), isLatest)
            }).addTo(map);

            marker.bindPopup(`
                <div class="text-xs space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider ${wp.is_exception ? 'text-rose-400' : 'text-[#FF884D]'} block">
                        Milestone #${index + 1}: ${escapeHtml(wp.status)}
                    </span>
                    <strong class="text-white block">${escapeHtml(wp.location_name)}</strong>
                    <p class="text-zinc-300 text-[11px] leading-relaxed">${escapeHtml(wp.description)}</p>
                    <span class="font-mono text-[10px] text-zinc-400 block mt-1">Recorded: ${escapeHtml(wp.occurred_at)}</span>
                </div>
            `);

            marker.on('click', () => {
                map.flyTo(wpPoint, Math.max(map.getZoom(), 6), { animate: true, duration: 0.85, easeLinearity: 0.25 });
            });
        });
    }

    // 3. Destination Marker
    if (mapData.destination) {
        const destPoint = [mapData.destination.lat, mapData.destination.lng];
        bounds.push(destPoint);

        const destMarker = LRef.marker(destPoint, {
            icon: createMapPin('#10B981', 'D', false)
        }).addTo(map);

        destMarker.bindPopup(`
            <div class="text-xs space-y-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block">${escapeHtml(window.i18n?.leaflet?.destination || 'Port of Destination')}</span>
                <strong class="text-white text-sm block">${escapeHtml(mapData.destination.name)}</strong>
                <span class="text-zinc-300 block">${escapeHtml(mapData.destination.city)}, ${escapeHtml(mapData.destination.country)}</span>
                <span class="font-mono text-[10px] text-zinc-400 block">${escapeHtml(window.i18n?.leaflet?.coordinates || 'GPS')}: ${mapData.destination.lat.toFixed(4)}, ${mapData.destination.lng.toFixed(4)}</span>
            </div>
        `);

        destMarker.on('click', () => {
            map.flyTo(destPoint, Math.max(map.getZoom(), 6), { animate: true, duration: 0.85, easeLinearity: 0.25 });
        });
    }

    // 4. Polyline Transit Corridor (solid active segment + dashed remaining segment to destination)
    if (latlngs.length >= 2) {
        LRef.polyline(latlngs, {
            color: '#18181B',
            weight: 3,
            opacity: 0.9,
            lineJoin: 'round'
        }).addTo(map);
    }

    if (mapData.destination && latlngs.length >= 1) {
        const lastActive = latlngs[latlngs.length - 1];
        const destPoint = [mapData.destination.lat, mapData.destination.lng];
        LRef.polyline([lastActive, destPoint], {
            color: '#FF6B1A',
            weight: 2.5,
            opacity: 0.85,
            dashArray: '6, 8',
            lineJoin: 'round'
        }).addTo(map);
    }

    // 5. Fit Bounds & Wire Reset Button
    function fitCorridor(animate = false) {
        if (bounds.length > 0) {
            if (animate) {
                map.flyToBounds(bounds, { padding: [50, 50], maxZoom: 7, duration: 0.9 });
            } else {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 7 });
            }
        } else {
            map.setView([15, 60], 3);
        }
    }

    fitCorridor(false);

    const resetBtn = document.getElementById('corridor-reset-zoom-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => fitCorridor(true));
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/applet/resources/views/shipments/show.blade.php ENDPATH**/ ?>