<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'metrics' => [],
    'role' => 'admin',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'metrics' => [],
    'role' => 'admin',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $workspaceShipments = $metrics['workspace_shipments'] ?? [];
    $firstShipment = $workspaceShipments[0] ?? [
        'id' => 0,
        'bay' => 'A9',
        'number' => 'SHP-5430',
        'short_code' => 'SHP - 5430',
        'status_value' => 'IN_TRANSIT',
        'status_label' => 'In Transit',
        'status_short' => 'Transit',
        'delay_label' => 'On Time',
        'delay_hours' => 0,
        'weight_kg' => 500,
        'weight_formatted' => '500kg',
        'cargo_type' => 'General Container Cargo',
        'items_count' => 1,
        'carrier_name' => 'Scania E3360',
        'carrier_code' => 'TRC-204',
        'operator_name' => 'Clark Kent',
        'customer_name' => 'Global Freight Partner',
        'dock_label' => 'Dock #3',
        'started_time' => '08:30 AM',
        'eta_formatted' => 'Scheduled',
        'origin_city' => 'Jakarta',
        'origin_country' => 'ID',
        'origin_name' => 'Port of Tanjung Priok',
        'origin_lat' => -6.1045,
        'origin_lng' => 106.8827,
        'dest_city' => 'Rotterdam',
        'dest_country' => 'NL',
        'dest_name' => 'Port of Rotterdam',
        'dest_lat' => 51.9500,
        'dest_lng' => 4.1333,
        'current_city' => 'Colombo',
        'current_country' => 'LK',
        'current_hub' => 'Port of Colombo Hub',
        'current_lat' => 6.9497,
        'current_lng' => 79.8428,
        'waypoints' => [],
        'show_url' => route('shipments.index'),
        'edit_url' => route('shipments.index'),
    ];

    $canCreateShipment = auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator());
    $createUrl = $canCreateShipment ? route('shipments.create') : route('shipments.index');

    // Map up to 5 shipments into the exact bay slots shown in the reference screenshot: A1, A3, A8, A9, A10
    $bayA1 = $workspaceShipments[1] ?? $firstShipment;
    $bayA3 = $workspaceShipments[2] ?? $firstShipment;
    $bayA8 = $workspaceShipments[3] ?? $firstShipment;
    $bayA9 = $workspaceShipments[0] ?? $firstShipment; // Default active slot with 3D parcel boxes
    $bayA10 = $workspaceShipments[4] ?? $bayA3;
?>

<!-- Visual Freight Command Center (Matching Reference Screenshot) -->
<section class="space-y-6" aria-label="Interactive Cargo & Route Workspace">
    <!-- Top Section: Left Controls & Cutaway Commercial Freight Truck with Cargo Bays -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        <!-- Left Column (3 Cols on XL): Mode Pills, Vehicle Header Card & Shipment Plannings -->
        <div class="xl:col-span-3 space-y-4">
            <!-- Quick Action Mode Circles -->
            <div class="flex items-center gap-2.5">
                <a href="<?php echo e(route('shipments.index')); ?>" class="w-10 h-10 rounded-full bg-[#FF6B1A] text-white flex items-center justify-center shadow-sm shadow-[#FF6B1A]/30 transition hover:scale-105" title="Active Fleet Transport" aria-label="Active Fleet Transport">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </a>
                <a href="<?php echo e(route('shipments.index')); ?>" class="w-10 h-10 rounded-full bg-white text-zinc-700 hover:text-zinc-950 flex items-center justify-center shadow-2xs transition hover:scale-105" title="Cargo Packages" aria-label="Cargo Packages">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </a>
                <a href="<?php echo e(auth()->user()->isCustomer() ? route('shipments.index') : route('locations.index')); ?>" class="w-10 h-10 rounded-full bg-white text-zinc-700 hover:text-zinc-950 flex items-center justify-center shadow-2xs transition hover:scale-105" title="Route Corridors" aria-label="Route Corridors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </a>
            </div>

            <!-- Active Carrier / Vehicle Card -->
            <div class="bg-white rounded-[24px] p-4 shadow-sm border border-black/[0.03] flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span id="ws-carrier-title" class="text-sm font-bold text-zinc-900 truncate"><?php echo e($firstShipment['carrier_name']); ?></span>
                        <span id="ws-top-status-pill" class="px-2.5 py-0.5 rounded-full bg-zinc-100 text-zinc-700 text-[11px] font-semibold whitespace-nowrap">
                            <?php echo e(strtolower($firstShipment['status_label'])); ?>

                        </span>
                    </div>
                    <div id="ws-carrier-sub" class="text-xs text-zinc-500 mt-0.5 font-mono">
                        <?php echo e($firstShipment['carrier_code']); ?> &bull; Volvo F-243
                    </div>
                </div>
                <!-- Mini Side-Profile Truck Illustration -->
                <div class="w-24 h-12 shrink-0 flex items-center justify-end">
                    <svg viewBox="0 0 140 64" class="w-full h-full drop-shadow-xs">
                        <!-- Trailer Box -->
                        <rect x="38" y="8" width="96" height="40" rx="3" fill="#F8FAFC" stroke="#CBD5E1" stroke-width="1.5"/>
                        <line x1="65" y1="8" x2="65" y2="48" stroke="#E2E8F0" stroke-width="1"/>
                        <line x1="95" y1="8" x2="95" y2="48" stroke="#E2E8F0" stroke-width="1"/>
                        <!-- Truck Cab -->
                        <path d="M12 24 L24 14 L36 14 L36 48 L8 48 L8 32 Q8 26 12 24 Z" fill="#FFFFFF" stroke="#94A3B8" stroke-width="1.5"/>
                        <!-- Windshield -->
                        <path d="M13 25 L22 17 L28 17 L28 28 L11 28 Z" fill="#1E293B"/>
                        <!-- Chassis -->
                        <rect x="8" y="46" width="124" height="4" rx="1" fill="#334155"/>
                        <!-- Wheels -->
                        <circle cx="22" cy="51" r="7" fill="#1E293B"/><circle cx="22" cy="51" r="3.5" fill="#CBD5E1"/>
                        <circle cx="54" cy="51" r="7" fill="#1E293B"/><circle cx="54" cy="51" r="3.5" fill="#CBD5E1"/>
                        <circle cx="114" cy="51" r="7" fill="#1E293B"/><circle cx="114" cy="51" r="3.5" fill="#CBD5E1"/>
                    </svg>
                </div>
            </div>

            <!-- Shipment Plannings Card -->
            <div class="bg-white rounded-[24px] p-5 shadow-sm border border-black/[0.03] space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h2 class="text-sm font-bold text-zinc-900">Shipment Plannings</h2>
                    </div>
                </div>

                <!-- Primary & Secondary Pill Buttons -->
                <div class="grid grid-cols-2 gap-2">
                    <a href="<?php echo e($createUrl); ?>" class="py-2.5 px-3 rounded-full bg-[#18181B] hover:bg-zinc-800 text-white text-xs font-semibold flex items-center justify-center gap-1.5 shadow-xs transition whitespace-nowrap">
                        <span>New Shipment</span>
                        <span class="text-sm leading-none">+</span>
                    </a>
                    <a href="<?php echo e(route('shipments.index')); ?>" class="py-2.5 px-3 rounded-full bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 text-zinc-700 text-xs font-medium flex items-center justify-center gap-1.5 transition whitespace-nowrap">
                        <span>All Directory</span>
                        <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </a>
                </div>

                <!-- Plannings Table Header -->
                <div class="grid grid-cols-12 text-[11px] font-medium text-zinc-400 pt-1 pb-1 border-b border-zinc-100">
                    <div class="col-span-3">Items</div>
                    <div class="col-span-6">Route</div>
                    <div class="col-span-3 text-right">Status</div>
                </div>

                <!-- Interactive Plannings Rows -->
                <div class="space-y-1.5">
                    <?php $__empty_1 = true; $__currentLoopData = $workspaceShipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <button
                            type="button"
                            onclick="window.selectCargoWorkspaceShipment(<?php echo e($idx); ?>)"
                            data-ws-row="<?php echo e($idx); ?>"
                            class="w-full grid grid-cols-12 items-center py-2 px-2.5 rounded-xl text-xs transition cursor-pointer text-left <?php echo e($idx === 0 ? 'bg-orange-50/70 text-zinc-900 font-semibold' : 'hover:bg-zinc-50 text-zinc-700'); ?>"
                        >
                            <div class="col-span-3 flex items-center gap-1.5 font-mono text-zinc-800">
                                <svg class="w-3.5 h-3.5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span class="tabular-nums"><?php echo e($ws['items_count']); ?></span>
                            </div>
                            <div class="col-span-6 truncate pr-2 text-zinc-800 font-medium" title="<?php echo e($ws['origin_city']); ?> to <?php echo e($ws['dest_city']); ?>">
                                <?php echo e($ws['origin_city']); ?> to <?php echo e($ws['dest_city']); ?>

                            </div>
                            <div class="col-span-3 flex items-center justify-end gap-1.5 text-[11px] font-medium text-zinc-700">
                                <span class="w-1.5 h-1.5 rounded-full <?php echo e($ws['status_value'] === 'IN_TRANSIT' ? 'bg-emerald-500' : ($ws['status_value'] === 'DELIVERED' ? 'bg-blue-500' : 'bg-emerald-400')); ?>"></span>
                                <span class="truncate"><?php echo e($ws['status_short']); ?></span>
                            </div>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="py-6 text-center text-xs text-zinc-400">
                            No active shipments planned yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Center/Right Column (9 Cols on XL): Cutaway Commercial Freight Truck & Interactive Cargo Bay Bento Grid -->
        <div class="xl:col-span-9">
            <div class="relative pt-2 pb-12 overflow-x-auto">
                <div class="min-w-[820px] flex items-end">

                    <!-- Left: Detailed White European Semi-Truck Cab (SVG Cutaway) -->
                    <div class="w-[245px] shrink-0 relative z-10 -mr-2 select-none">
                        <svg viewBox="0 0 260 365" class="w-full h-auto drop-shadow-[0_14px_24px_rgba(24,24,27,0.10)]">
                            <defs>
                                <linearGradient id="cabBodyGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#FFFFFF" />
                                    <stop offset="65%" stop-color="#F4F5F7" />
                                    <stop offset="100%" stop-color="#E2E5EC" />
                                </linearGradient>
                                <linearGradient id="cabPanelShade" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#E8EBF0" />
                                    <stop offset="100%" stop-color="#D0D5DF" />
                                </linearGradient>
                                <linearGradient id="windshieldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#1E293B" />
                                    <stop offset="50%" stop-color="#334155" />
                                    <stop offset="100%" stop-color="#0F172A" />
                                </linearGradient>
                                <linearGradient id="chassisGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#3F3F46" />
                                    <stop offset="100%" stop-color="#18181B" />
                                </linearGradient>
                            </defs>

                            <!-- Rear Cab Air Deflector / Fairing connecting to Trailer -->
                            <path d="M215 18 L248 12 L248 275 L215 275 Z" fill="#D4D8E1" />

                            <!-- Main Aerodynamic High-Roof Cab Body -->
                            <path d="M98 32 Q125 18 182 18 L232 18 L232 276 L26 276 L26 172 Q26 146 38 118 L72 48 Q82 36 98 32 Z" fill="url(#cabBodyGrad)" stroke="#CBD5E1" stroke-width="1.5" />

                            <!-- Roof Spoiler Visor Detail -->
                            <path d="M72 48 Q105 32 195 32 L228 32 L228 52 L68 54 Z" fill="#F8FAFC" stroke="#E2E8F0" stroke-width="1" />
                            <path d="M58 64 L82 60 L82 68 L55 72 Z" fill="#1E293B" opacity="0.85" />

                            <!-- Side Window & Dark B-Pillar Frame -->
                            <path d="M44 126 L75 68 L175 68 Q185 68 185 78 L185 144 L44 144 Z" fill="#18181B" />
                            <path d="M52 126 L79 74 L165 74 Q173 74 173 82 L173 138 L52 138 Z" fill="url(#windshieldGrad)" />
                            <!-- Window Glass Specular Reflection -->
                            <path d="M68 138 L104 74 L126 74 L90 138 Z" fill="#FFFFFF" opacity="0.12" />

                            <!-- Exterior Heavy-Duty Side Mirror Assembly -->
                            <path d="M62 76 L38 76 Q32 76 32 82 L32 132 Q32 138 38 138 L58 138" fill="none" stroke="#18181B" stroke-width="4" stroke-linecap="round" />
                            <rect x="22" y="84" width="15" height="44" rx="5" fill="#18181B" stroke="#475569" stroke-width="1" />
                            <rect x="24" y="66" width="12" height="15" rx="3" fill="#18181B" />

                            <!-- Sculpted Door Contour & Handle -->
                            <path d="M46 152 L185 152 L185 245 L46 245 Z" fill="url(#cabPanelShade)" opacity="0.35" stroke="#CBD5E1" stroke-width="1" />
                            <path d="M30 165 L195 165" stroke="#CBD5E1" stroke-width="1.5" />
                            <!-- Recessed Door Handle -->
                            <rect x="148" y="186" width="24" height="9" rx="4" fill="#CBD5E1" stroke="#94A3B8" stroke-width="1" />
                            <rect x="152" y="189" width="16" height="3" rx="1.5" fill="#475569" />

                            <!-- Front Lower Grille & Headlight Bumper Unit -->
                            <path d="M22 218 L48 218 L48 278 L18 278 L18 232 Q18 220 22 218 Z" fill="#E2E8F0" stroke="#CBD5E1" stroke-width="1" />
                            <!-- LED Headlight Lamp -->
                            <rect x="18" y="236" width="12" height="18" rx="2" fill="#FFFBEB" stroke="#94A3B8" stroke-width="1.5" />
                            <rect x="20" y="248" width="8" height="4" rx="1" fill="#FF6B1A" />

                            <!-- Heavy-Duty Chassis Skirt & Boarding Steps -->
                            <rect x="16" y="268" width="244" height="38" rx="4" fill="url(#chassisGrad)" />
                            <rect x="34" y="274" width="32" height="6" rx="2" fill="#71717A" />
                            <rect x="34" y="286" width="32" height="6" rx="2" fill="#71717A" />

                            <!-- Front Wheel Arch Fender -->
                            <path d="M74 308 A56 56 0 0 1 186 308 L194 308 A64 64 0 0 0 66 308 Z" fill="#18181B" />

                            <!-- Front Steer Commercial Wheel & Multi-Lug Alloy Rim -->
                            <g transform="translate(130, 308)">
                                <circle cx="0" cy="0" r="50" fill="#18181B" />
                                <circle cx="0" cy="0" r="46" fill="none" stroke="#27272A" stroke-width="3" stroke-dasharray="6 4" />
                                <circle cx="0" cy="0" r="37" fill="#27272A" />
                                <circle cx="0" cy="0" r="28" fill="#D4D4D8" stroke="#71717A" stroke-width="2" />
                                <circle cx="0" cy="0" r="20" fill="#9CA3AF" />
                                <!-- 10 Wheel Lug Bolts -->
                                <circle cx="0" cy="-15" r="2.2" fill="#18181B" />
                                <circle cx="9" cy="-12" r="2.2" fill="#18181B" />
                                <circle cx="14" cy="-5" r="2.2" fill="#18181B" />
                                <circle cx="14" cy="5" r="2.2" fill="#18181B" />
                                <circle cx="9" cy="12" r="2.2" fill="#18181B" />
                                <circle cx="0" cy="15" r="2.2" fill="#18181B" />
                                <circle cx="-9" cy="12" r="2.2" fill="#18181B" />
                                <circle cx="-14" cy="5" r="2.2" fill="#18181B" />
                                <circle cx="-14" cy="-5" r="2.2" fill="#18181B" />
                                <circle cx="-9" cy="-12" r="2.2" fill="#18181B" />
                                <circle cx="0" cy="0" r="9" fill="#3F3F46" stroke="#E4E4E7" stroke-width="1.5" />
                            </g>
                        </svg>
                    </div>

                    <!-- Right: Cutaway Annotated Cargo Trailer Container with 2-Row Bento Grid -->
                    <div class="flex-1 relative">
                        <!-- Outer Metallic Trailer Frame -->
                        <div class="bg-[#ECEEF2] border-[3px] border-zinc-300 rounded-2xl p-3.5 shadow-[0_20px_40px_-15px_rgba(24,24,27,0.12)] relative z-20">
                            <!-- Top Row of Cargo Bays: A1 (1 col), A2 (1 col + button), A3 (2 cols) -->
                            <div class="grid grid-cols-4 gap-3 mb-3">
                                <!-- Bay A1 -->
                                <button
                                    type="button"
                                    onclick="window.selectCargoWorkspaceShipment(<?php echo e(isset($workspaceShipments[1]) ? 1 : 0); ?>)"
                                    data-ws-bay="<?php echo e(isset($workspaceShipments[1]) ? 1 : 0); ?>"
                                    class="cargo-bay-hatch group relative h-36 rounded-2xl bg-white p-3.5 text-left flex flex-col justify-between border-2 border-transparent hover:border-zinc-300 shadow-2xs transition cursor-pointer"
                                >
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-xs font-bold text-zinc-700">A1</span>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <?php echo e($bayA1['status_short']); ?>

                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-xs text-zinc-400 font-medium tabular-nums"><?php echo e($bayA1['weight_formatted']); ?></div>
                                        <div class="text-sm font-bold text-zinc-900 font-mono tracking-tight mt-0.5 truncate"><?php echo e($bayA1['short_code']); ?></div>
                                    </div>
                                </button>

                                <!-- Bay A2 (Interactive Add Shipment Slot with Dark Border & Charcoal + Button) -->
                                <a
                                    href="<?php echo e($createUrl); ?>"
                                    class="group relative h-36 rounded-2xl bg-white p-3.5 flex flex-col justify-between border-2 border-zinc-800 hover:border-[#FF6B1A] shadow-2xs transition"
                                    title="Allocate New Cargo to Bay A2"
                                >
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-xs font-bold text-zinc-800">A2</span>
                                    </div>
                                    <div class="flex items-center justify-center my-auto">
                                        <span class="w-10 h-10 rounded-full bg-[#18181B] group-hover:bg-[#FF6B1A] text-white flex items-center justify-center text-lg font-medium shadow-md transition-transform group-hover:scale-110">
                                            +
                                        </span>
                                    </div>
                                    <div class="h-3"></div>
                                </a>

                                <!-- Bay A3 (Wide 2-Column Double Bay) -->
                                <button
                                    type="button"
                                    onclick="window.selectCargoWorkspaceShipment(<?php echo e(isset($workspaceShipments[2]) ? 2 : 0); ?>)"
                                    data-ws-bay="<?php echo e(isset($workspaceShipments[2]) ? 2 : 0); ?>"
                                    class="col-span-2 cargo-bay-hatch group relative h-36 rounded-2xl bg-white p-3.5 text-left flex flex-col justify-between border-2 border-transparent hover:border-zinc-300 shadow-2xs transition cursor-pointer"
                                >
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-xs font-bold text-zinc-700">A3</span>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <?php echo e($bayA3['status_short']); ?>

                                        </span>
                                    </div>
                                    <div class="flex items-end justify-between w-full">
                                        <div>
                                            <div class="text-xs text-zinc-400 font-medium tabular-nums"><?php echo e($bayA3['weight_formatted']); ?></div>
                                            <div class="text-sm font-bold text-zinc-900 font-mono tracking-tight mt-0.5"><?php echo e($bayA3['short_code']); ?></div>
                                        </div>
                                        <span class="text-[11px] text-zinc-400 font-mono"><?php echo e($bayA3['origin_city']); ?> &rarr; <?php echo e($bayA3['dest_city']); ?></span>
                                    </div>
                                </button>
                            </div>

                            <!-- Bottom Row of Cargo Bays: A7 (+ slot), A8, A9 (Highlighted Active Orange Slot with 3D Boxes), A10 -->
                            <div class="grid grid-cols-4 gap-3">
                                <!-- Bay A7 (Auxiliary Add Slot) -->
                                <a
                                    href="<?php echo e($createUrl); ?>"
                                    class="group relative h-36 rounded-2xl bg-white p-3.5 flex flex-col justify-between border-2 border-transparent hover:border-zinc-300 shadow-2xs transition"
                                    title="Allocate Cargo to Bay A7"
                                >
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-xs font-bold text-zinc-700">A7</span>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Loading
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-center my-auto">
                                        <span class="w-9 h-9 rounded-full bg-zinc-100 group-hover:bg-[#18181B] text-zinc-700 group-hover:text-white flex items-center justify-center text-base font-medium transition">
                                            +
                                        </span>
                                    </div>
                                    <div class="h-3"></div>
                                </a>

                                <!-- Bay A8 -->
                                <button
                                    type="button"
                                    onclick="window.selectCargoWorkspaceShipment(<?php echo e(isset($workspaceShipments[3]) ? 3 : 0); ?>)"
                                    data-ws-bay="<?php echo e(isset($workspaceShipments[3]) ? 3 : 0); ?>"
                                    class="cargo-bay-hatch group relative h-36 rounded-2xl bg-white p-3.5 text-left flex flex-col justify-between border-2 border-transparent hover:border-zinc-300 shadow-2xs transition cursor-pointer"
                                >
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-xs font-bold text-zinc-700">A8</span>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <?php echo e($bayA8['status_short']); ?>

                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-xs text-zinc-400 font-medium tabular-nums"><?php echo e($bayA8['weight_formatted']); ?></div>
                                        <div class="text-sm font-bold text-zinc-900 font-mono tracking-tight mt-0.5 truncate"><?php echo e($bayA8['short_code']); ?></div>
                                    </div>
                                </button>

                                <!-- Bay A9 (Default Active Bay with Orange Border & 3D Stacked Cardboard Parcel Boxes!) -->
                                <button
                                    type="button"
                                    onclick="window.selectCargoWorkspaceShipment(0)"
                                    data-ws-bay="0"
                                    class="cargo-bay-hatch group relative h-36 rounded-2xl bg-white p-3.5 text-left flex flex-col justify-between border-2 border-[#FF6B1A] shadow-sm transition cursor-pointer overflow-hidden"
                                >
                                    <div class="flex items-center justify-between w-full relative z-10">
                                        <span class="text-xs font-bold text-zinc-900">A9</span>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-700">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <?php echo e($bayA9['status_short']); ?>

                                        </span>
                                    </div>

                                    <!-- 3D Isometric Stacked Cardboard Boxes Graphic -->
                                    <div class="absolute right-1.5 bottom-7 w-28 h-20 pointer-events-none">
                                        <svg viewBox="0 0 140 100" class="w-full h-full drop-shadow-sm">
                                            <!-- Rear Large Box -->
                                            <g transform="translate(38, 8)">
                                                <polygon points="30,0 60,14 30,28 0,14" fill="#F3D5B5" />
                                                <polygon points="0,14 30,28 30,62 0,48" fill="#DEB68B" />
                                                <polygon points="30,28 60,14 60,48 30,62" fill="#C69C6D" />
                                                <line x1="15" y1="7" x2="45" y2="21" stroke="#FFF3E6" stroke-width="2" opacity="0.7" />
                                            </g>
                                            <!-- Left Front Parcel Box -->
                                            <g transform="translate(8, 36)">
                                                <polygon points="24,0 48,11 24,22 0,11" fill="#F5DEC3" />
                                                <polygon points="0,11 24,22 24,50 0,39" fill="#E0B98F" />
                                                <polygon points="24,22 48,11 48,39 24,50" fill="#C89D70" />
                                            </g>
                                            <!-- Right Front Parcel Box -->
                                            <g transform="translate(56, 34)">
                                                <polygon points="28,0 56,13 28,26 0,13" fill="#F6E0C6" />
                                                <polygon points="0,13 28,26 28,56 0,43" fill="#DFB78C" />
                                                <polygon points="28,26 56,13 56,43 28,56" fill="#C49768" />
                                                <rect x="34" y="28" width="10" height="6" transform="skewY(-24)" fill="#FFFFFF" opacity="0.65" />
                                            </g>
                                        </svg>
                                    </div>

                                    <div class="relative z-10">
                                        <div class="text-xs text-zinc-500 font-medium tabular-nums"><?php echo e($bayA9['weight_formatted']); ?></div>
                                        <div class="text-sm font-bold text-zinc-900 font-mono tracking-tight mt-0.5 truncate"><?php echo e($bayA9['short_code']); ?></div>
                                    </div>
                                </button>

                                <!-- Bay A10 -->
                                <button
                                    type="button"
                                    onclick="window.selectCargoWorkspaceShipment(<?php echo e(isset($workspaceShipments[4]) ? 4 : 0); ?>)"
                                    data-ws-bay="<?php echo e(isset($workspaceShipments[4]) ? 4 : 0); ?>"
                                    class="cargo-bay-hatch group relative h-36 rounded-2xl bg-white p-3.5 text-left flex flex-col justify-between border-2 border-transparent hover:border-zinc-300 shadow-2xs transition cursor-pointer"
                                >
                                    <div class="flex items-center justify-between w-full">
                                        <span class="text-xs font-bold text-zinc-700">A10</span>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <?php echo e($bayA10['status_short']); ?>

                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-xs text-zinc-400 font-medium tabular-nums"><?php echo e($bayA10['weight_formatted']); ?></div>
                                        <div class="text-sm font-bold text-zinc-900 font-mono tracking-tight mt-0.5 truncate"><?php echo e($bayA10['short_code']); ?></div>
                                    </div>
                                </button>
                            </div>
                        </div>

                        <!-- Trailer Undercarriage Chassis Beam, Drive Axle Wheels & Landing Gear Legs -->
                        <div class="relative h-14 -mt-2 z-10 flex items-start justify-between px-6 select-none pointer-events-none">
                            <!-- Tandem Drive Axles & Undercarriage -->
                            <div class="flex items-center gap-4">
                                <svg viewBox="0 0 240 75" class="w-56 h-16 -mt-3">
                                    <!-- Chassis Subframe -->
                                    <rect x="0" y="0" width="235" height="22" rx="3" fill="#27272A" />
                                    <rect x="135" y="6" width="85" height="26" rx="3" fill="#3F3F46" stroke="#52525B" stroke-width="1" />
                                    <!-- Drive Wheel 1 -->
                                    <g transform="translate(65, 22)">
                                        <circle cx="0" cy="0" r="46" fill="#18181B" />
                                        <circle cx="0" cy="0" r="34" fill="#27272A" />
                                        <circle cx="0" cy="0" r="24" fill="#A1A1AA" stroke="#52525B" stroke-width="2" />
                                        <circle cx="0" cy="0" r="11" fill="#27272A" />
                                    </g>
                                </svg>
                            </div>

                            <!-- Trailer Support Landing Gear & Rear Underrun Bar -->
                            <div class="flex items-start gap-12 pr-8">
                                <div class="flex flex-col items-center">
                                    <div class="w-4 h-10 bg-zinc-700 rounded-xs"></div>
                                    <div class="w-8 h-2 bg-zinc-800 rounded-full"></div>
                                </div>
                                <div class="w-48 h-3 bg-zinc-700 rounded-xs mt-2"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: 4 Equal Cards Matching Reference Screenshot -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

        <!-- CARD 1: Shipment Informations -->
        <div class="bg-white rounded-[24px] p-5 shadow-sm border border-black/[0.03] flex flex-col justify-between space-y-5">
            <div class="space-y-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900">Shipment Informations</h3>
                </div>

                <!-- Driver / Operator Row -->
                <div class="flex items-center justify-between gap-2 pt-1">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FF6B1A] to-amber-600 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                            CK
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] text-zinc-400">Operator</div>
                            <div id="ws-operator-name" class="text-sm font-bold text-zinc-900 truncate"><?php echo e($firstShipment['operator_name']); ?></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a id="ws-info-search-link" href="<?php echo e($firstShipment['show_url']); ?>" class="w-8 h-8 rounded-full bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 text-zinc-700 flex items-center justify-center transition" title="Inspect Shipment" aria-label="Inspect Shipment">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </a>
                        <a id="ws-info-card-link" href="<?php echo e($firstShipment['show_url']); ?>" class="w-8 h-8 rounded-full bg-zinc-50 hover:bg-zinc-100 border border-zinc-200/80 text-zinc-700 flex items-center justify-center transition" title="Open Telemetry" aria-label="Open Telemetry">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- 3-Column Specs (Truck ID | Dock | Started) -->
                <div class="grid grid-cols-3 gap-2 pt-3 border-t border-zinc-100">
                    <div>
                        <div class="text-[11px] text-zinc-400">Truck ID</div>
                        <div id="ws-truck-id" class="text-xs font-bold text-zinc-900 font-mono mt-0.5"><?php echo e($firstShipment['carrier_code']); ?></div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">Dock</div>
                        <div id="ws-dock-label" class="text-xs font-bold text-zinc-900 mt-0.5"><?php echo e($firstShipment['dock_label']); ?></div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">Started</div>
                        <div id="ws-started-time" class="text-xs font-bold text-zinc-900 font-mono mt-0.5 tabular-nums"><?php echo e($firstShipment['started_time']); ?></div>
                    </div>
                </div>

                <!-- Departure -> Arrival Hatched Progress Bar -->
                <div class="pt-2">
                    <div class="flex items-center justify-between text-[11px] text-zinc-400 mb-1">
                        <span>Departure</span>
                        <span>Arrival</span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span id="ws-dep-city" class="text-xs font-bold text-zinc-900 truncate max-w-[90px]"><?php echo e($firstShipment['origin_city']); ?></span>
                        <div class="flex-1 h-3 rounded-full route-track-hatch relative flex items-center justify-center mx-1">
                            <span class="w-6 h-6 rounded-full bg-[#18181B] text-white flex items-center justify-center shadow-xs">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                </svg>
                            </span>
                        </div>
                        <span id="ws-arr-city" class="text-xs font-bold text-zinc-900 truncate max-w-[90px] text-right"><?php echo e($firstShipment['dest_city']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Change Route | Edit Route) -->
            <div class="grid grid-cols-2 gap-2.5 pt-2">
                <a id="ws-view-btn" href="<?php echo e($firstShipment['show_url']); ?>" class="py-2.5 px-3 rounded-full border border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 text-xs font-semibold text-zinc-800 text-center transition">
                    View Details
                </a>
                <a id="ws-edit-btn" href="<?php echo e($canCreateShipment ? $firstShipment['edit_url'] : $firstShipment['show_url']); ?>" class="py-2.5 px-3 rounded-full border border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 text-xs font-semibold text-zinc-800 text-center transition">
                    <?php echo e($canCreateShipment ? 'Edit Route' : 'Timeline'); ?>

                </a>
            </div>
        </div>

        <!-- CARD 2: Tracking Details -->
        <div class="bg-white rounded-[24px] p-5 shadow-sm border border-black/[0.03] flex flex-col justify-between space-y-4">
            <div class="space-y-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900">Tracking Details</h3>
                </div>

                <!-- Tracking ID & Status Pill -->
                <div class="flex items-center justify-between gap-2 pt-1 pb-3 border-b border-zinc-100">
                    <div class="min-w-0">
                        <div class="text-[11px] text-zinc-400">Tracking ID</div>
                        <a id="ws-tracking-number" href="<?php echo e($firstShipment['show_url']); ?>" class="text-sm font-bold text-zinc-900 hover:text-[#FF6B1A] font-mono truncate block mt-0.5 transition">
                            #<?php echo e($firstShipment['number']); ?>

                        </a>
                    </div>
                    <span id="ws-tracking-status" class="px-3 py-1 rounded-full bg-zinc-100 text-zinc-700 text-xs font-semibold whitespace-nowrap">
                        <?php echo e(strtolower($firstShipment['status_label'])); ?>

                    </span>
                </div>

                <!-- 3-Step Vertical Connected Timeline -->
                <div class="relative pl-1 space-y-4 pt-1">
                    <!-- Vertical Connector Line -->
                    <div class="absolute left-[13px] top-3 bottom-3 w-px border-l border-dashed border-zinc-300"></div>

                    <!-- Step 1: Current Location (Active Orange Check) -->
                    <div class="relative flex items-start gap-3.5">
                        <span class="w-5 h-5 rounded-full bg-[#FF6B1A] text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs shadow-[#FF6B1A]/30 z-10">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[11px] text-zinc-400">Current Location</div>
                            <div id="ws-track-current" class="text-xs font-bold text-zinc-900 truncate mt-0.5">
                                <?php echo e($firstShipment['current_city']); ?>, <?php echo e($firstShipment['current_country']); ?>

                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Departure -->
                    <div class="relative flex items-start gap-3.5">
                        <span class="w-5 h-5 rounded-full bg-zinc-100 text-zinc-500 flex items-center justify-center shrink-0 mt-0.5 z-10">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[11px] text-zinc-400">Departure</div>
                            <div id="ws-track-departure" class="text-xs font-bold text-zinc-900 truncate mt-0.5">
                                <?php echo e($firstShipment['origin_city']); ?>, <?php echo e($firstShipment['origin_country']); ?>

                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Arrival -->
                    <div class="relative flex items-start gap-3.5">
                        <span class="w-5 h-5 rounded-full bg-zinc-100 text-zinc-500 flex items-center justify-center shrink-0 mt-0.5 z-10">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[11px] text-zinc-400">Arrival</div>
                            <div id="ws-track-arrival" class="text-xs font-bold text-zinc-900 truncate mt-0.5">
                                <?php echo e($firstShipment['dest_city']); ?>, <?php echo e($firstShipment['dest_country']); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-1 flex items-center justify-between text-[11px] text-zinc-400 border-t border-zinc-100">
                <span>ETA Schedule</span>
                <span id="ws-track-eta" class="font-mono font-semibold text-zinc-700 tabular-nums"><?php echo e($firstShipment['eta_formatted']); ?></span>
            </div>
        </div>

        <!-- CARD 3: Route Transportation (Interactive OpenStreetMap with Smooth Zoom!) -->
        <div class="bg-white rounded-[24px] p-5 shadow-sm border border-black/[0.03] flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-zinc-900">Route Transportation</h3>
                        <p id="ws-map-subtitle" class="text-[11px] text-zinc-400 truncate">
                            <?php echo e($firstShipment['origin_city']); ?> &rarr; <?php echo e($firstShipment['dest_city']); ?>

                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-1 shrink-0">
                    <button
                        type="button"
                        onclick="window.resetCargoWorkspaceMapBounds()"
                        class="w-7 h-7 rounded-full hover:bg-zinc-100 text-zinc-500 hover:text-zinc-900 flex items-center justify-center transition cursor-pointer"
                        title="Reset Smooth Map Zoom"
                        aria-label="Reset Smooth Map Zoom"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                    <a id="ws-map-expand-link" href="<?php echo e($firstShipment['show_url']); ?>" class="w-7 h-7 rounded-full hover:bg-zinc-100 text-zinc-600 hover:text-zinc-900 flex items-center justify-center transition" title="Open Full Corridor Map" aria-label="Open Full Corridor Map">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H8M17 7v9" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Interactive OpenStreetMap Container with Floating Bottom Overlay -->
            <div class="relative rounded-2xl overflow-hidden border border-zinc-200/80 h-[215px] bg-zinc-100">
                <div id="dashboard-osm-map" class="w-full h-full z-0"></div>

                <!-- Floating Hub Callout Pill at Top Center -->
                <div class="absolute top-2.5 left-1/2 -translate-x-1/2 z-[400] pointer-events-none">
                    <div id="ws-map-floating-hub" class="px-3 py-1 rounded-full bg-[#18181B]/90 backdrop-blur-xs text-white text-[10px] font-medium shadow-md whitespace-nowrap max-w-[210px] truncate">
                        <?php echo e($firstShipment['current_hub']); ?>

                    </div>
                </div>

                <!-- Floating Departure -> Arrival Overlay Pill at Bottom -->
                <div class="absolute bottom-2.5 inset-x-2.5 z-[400] bg-white/95 backdrop-blur-md rounded-xl px-3.5 py-2 shadow-md border border-black/[0.04]">
                    <div class="flex items-center justify-between text-[10px] text-zinc-400">
                        <span>Departure</span>
                        <span>Arrival</span>
                    </div>
                    <div class="flex items-center justify-between gap-2 mt-0.5">
                        <span id="ws-map-dep-city" class="text-xs font-bold text-zinc-900 truncate max-w-[75px]"><?php echo e($firstShipment['origin_city']); ?></span>
                        <div class="flex-1 flex items-center justify-center relative">
                            <div class="w-full border-t-2 border-dashed border-[#FF6B1A]/40"></div>
                            <span class="absolute w-5 h-5 rounded-full bg-[#FF6B1A] text-white flex items-center justify-center shadow-xs">
                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                </svg>
                            </span>
                        </div>
                        <span id="ws-map-arr-city" class="text-xs font-bold text-zinc-900 truncate max-w-[75px] text-right"><?php echo e($firstShipment['dest_city']); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 4: Vehicle & Fleet Health -->
        <div class="bg-white rounded-[24px] p-5 shadow-sm border border-black/[0.03] flex flex-col justify-between space-y-4">
            <div class="space-y-4">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900">Vehicle Health</h3>
                            <p class="text-[11px] text-zinc-400">5,000 miles &bull; Active Corridor</p>
                        </div>
                    </div>
                    <a href="#operational-analytics" class="w-7 h-7 rounded-full hover:bg-zinc-100 text-zinc-600 hover:text-zinc-900 flex items-center justify-center transition" title="View Full Analytics" aria-label="View Full Analytics">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H8M17 7v9" />
                        </svg>
                    </a>
                </div>

                <!-- 3 Metric Columns (Services / Fleet | Engine / SLA Health | Active Alert) -->
                <div class="grid grid-cols-3 gap-2 pt-1">
                    <div>
                        <div class="text-[11px] text-zinc-400">Services</div>
                        <div class="text-sm font-bold text-zinc-900 mt-0.5 tabular-nums"><?php echo e($metrics['total_shipments'] ?? 0); ?> units</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">Engine Health</div>
                        <div class="text-sm font-bold text-zinc-900 mt-0.5 tabular-nums"><?php echo e($metrics['kpis']['on_time_delivery_rate'] ?? 100); ?>%</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">Active Alert</div>
                        <div class="text-sm font-bold text-zinc-900 mt-0.5 tabular-nums"><?php echo e($metrics['open_exceptions'] ?? 0); ?></div>
                    </div>
                </div>
            </div>

            <!-- Iconic Alternating Orange & Sage Vertical Pill Bars (Matching Reference Screenshot) -->
            <div class="h-32 flex items-end justify-between gap-2 pt-4 px-1">
                <div class="w-2.5 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 76%"></div>
                <div class="w-2.5 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 48%"></div>
                <div class="w-2.5 rounded-full bg-[#E3EBE6] transition-all duration-300" style="height: 68%"></div>
                <div class="w-2.5 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 36%"></div>
                <div class="w-2.5 rounded-full bg-[#E3EBE6] transition-all duration-300" style="height: 96%"></div>
                <div class="w-2.5 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 82%"></div>
                <div class="w-2.5 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 38%"></div>
                <div class="w-2.5 rounded-full bg-[#E3EBE6] transition-all duration-300" style="height: 88%"></div>
                <div class="w-2.5 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 62%"></div>
                <div class="w-2.5 rounded-full bg-[#E3EBE6] transition-all duration-300" style="height: 44%"></div>
                <div class="w-2.5 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 58%"></div>
            </div>
        </div>

    </div>
</section>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const shipments = <?php echo json_encode($workspaceShipments, 15, 512) ?>;
    const defaultShipment = <?php echo json_encode($firstShipment, 15, 512) ?>;
    const canEdit = <?php echo json_encode($canCreateShipment, 15, 512) ?>;
    let dashboardMap = null;
    let routeLayerGroup = null;
    let activeBounds = null;

    function initDashboardOsmMap() {
        const container = document.getElementById('dashboard-osm-map');
        if (!container || typeof window.L === 'undefined') {
            return;
        }

        dashboardMap = window.L.map(container, {
            zoomControl: false,
            scrollWheelZoom: true,
            zoomSnap: 0.25,
            zoomDelta: 0.5,
            wheelPxPerZoomLevel: 60,
            wheelDebounceTime: 20,
            zoomAnimation: true,
            fadeAnimation: true,
            markerZoomAnimation: true,
            inertia: true,
        });

        window.L.control.zoom({ position: 'bottomright' }).addTo(dashboardMap);

        window.L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a>'
        }).addTo(dashboardMap);

        routeLayerGroup = window.L.featureGroup().addTo(dashboardMap);

        renderShipmentOnMap(shipments.length > 0 ? shipments[0] : defaultShipment, false);

        setTimeout(() => {
            try { dashboardMap.invalidateSize(); } catch (e) {}
        }, 250);
    }

    function createNexPin(color, isPulse = false) {
        const pulse = isPulse
            ? `<span class="absolute -inset-2 rounded-full bg-[#FF6B1A]/35 animate-ping"></span>`
            : '';
        return window.L.divIcon({
            className: 'cargotrace-pin-wrapper',
            html: `
                <div class="relative flex items-center justify-center">
                    ${pulse}
                    <div style="background-color: ${color};" class="w-5 h-5 rounded-full border-2 border-white shadow-md flex items-center justify-center">
                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                    </div>
                </div>
            `,
            iconSize: [20, 20],
            iconAnchor: [10, 10],
            popupAnchor: [0, -12],
        });
    }

    function renderShipmentOnMap(sh, animateFly = true) {
        if (!dashboardMap || !routeLayerGroup) return;

        routeLayerGroup.clearLayers();

        const originPt = [Number(sh.origin_lat), Number(sh.origin_lng)];
        const currentPt = [Number(sh.current_lat), Number(sh.current_lng)];
        const destPt = [Number(sh.dest_lat), Number(sh.dest_lng)];

        const pts = [originPt];
        if (Array.isArray(sh.waypoints) && sh.waypoints.length > 0) {
            sh.waypoints.forEach(wp => {
                pts.push([Number(wp.lat), Number(wp.lng)]);
            });
        } else {
            pts.push(currentPt);
        }
        pts.push(destPt);

        // 1. Completed solid orange segment
        window.L.polyline([originPt, currentPt], {
            color: '#FF6B1A',
            weight: 3,
            opacity: 0.95,
            lineCap: 'round',
        }).addTo(routeLayerGroup);

        // 2. Remaining dashed dark segment
        window.L.polyline([currentPt, destPt], {
            color: '#18181B',
            weight: 2.2,
            opacity: 0.75,
            dashArray: '5, 6',
            lineCap: 'round',
        }).addTo(routeLayerGroup);

        // Origin Marker
        window.L.marker(originPt, { icon: createNexPin('#18181B', false) })
            .bindPopup(`<strong>Departure: ${sh.origin_city}</strong><br><span class="text-xs">${sh.origin_name}</span>`)
            .addTo(routeLayerGroup);

        // Current Position Marker (Pulsing Orange)
        window.L.marker(currentPt, { icon: createNexPin('#FF6B1A', true) })
            .bindPopup(`<strong>Current: ${sh.current_city}</strong><br><span class="text-xs">${sh.current_hub}</span>`)
            .addTo(routeLayerGroup);

        // Destination Marker
        window.L.marker(destPt, { icon: createNexPin('#18181B', false) })
            .bindPopup(`<strong>Arrival: ${sh.dest_city}</strong><br><span class="text-xs">${sh.dest_name}</span>`)
            .addTo(routeLayerGroup);

        activeBounds = routeLayerGroup.getBounds().pad(0.22);

        if (animateFly) {
            dashboardMap.flyToBounds(activeBounds, {
                padding: [20, 20],
                maxZoom: 6,
                duration: 0.85,
                easeLinearity: 0.25,
            });
        } else {
            dashboardMap.fitBounds(activeBounds, {
                padding: [20, 20],
                maxZoom: 6,
            });
        }
    }

    window.resetCargoWorkspaceMapBounds = function () {
        if (dashboardMap && activeBounds) {
            dashboardMap.flyToBounds(activeBounds, {
                padding: [20, 20],
                maxZoom: 6,
                duration: 0.75,
            });
        }
    };

    window.selectCargoWorkspaceShipment = function (index) {
        const sh = shipments[index] || defaultShipment;
        if (!sh) return;

        // Highlight active row in Shipment Plannings
        document.querySelectorAll('[data-ws-row]').forEach(el => {
            const isMatch = Number(el.getAttribute('data-ws-row')) === Number(index);
            el.classList.toggle('bg-orange-50/70', isMatch);
            el.classList.toggle('font-semibold', isMatch);
        });

        // Highlight active bay card in Truck Trailer
        document.querySelectorAll('[data-ws-bay]').forEach(el => {
            const isMatch = Number(el.getAttribute('data-ws-bay')) === Number(index);
            el.classList.toggle('border-[#FF6B1A]', isMatch);
            el.classList.toggle('border-transparent', !isMatch);
        });

        // Update Top Carrier Card & Bottom 4 Cards
        const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };
        const setHref = (id, val) => {
            const el = document.getElementById(id);
            if (el && val) el.setAttribute('href', val);
        };

        setText('ws-carrier-title', sh.carrier_name);
        setText('ws-top-status-pill', String(sh.status_label).toLowerCase());
        setText('ws-carrier-sub', `${sh.carrier_code} • Volvo F-243`);

        setText('ws-operator-name', sh.operator_name);
        setText('ws-truck-id', sh.carrier_code);
        setText('ws-dock-label', sh.dock_label);
        setText('ws-started-time', sh.started_time);
        setText('ws-dep-city', sh.origin_city);
        setText('ws-arr-city', sh.dest_city);
        setHref('ws-info-search-link', sh.show_url);
        setHref('ws-info-card-link', sh.show_url);
        setHref('ws-view-btn', sh.show_url);
        setHref('ws-edit-btn', canEdit ? sh.edit_url : sh.show_url);

        setText('ws-tracking-number', `#${sh.number}`);
        setHref('ws-tracking-number', sh.show_url);
        setText('ws-tracking-status', String(sh.status_label).toLowerCase());
        setText('ws-track-current', `${sh.current_city}, ${sh.current_country}`);
        setText('ws-track-departure', `${sh.origin_city}, ${sh.origin_country}`);
        setText('ws-track-arrival', `${sh.dest_city}, ${sh.dest_country}`);
        setText('ws-track-eta', sh.eta_formatted);

        setText('ws-map-subtitle', `${sh.origin_city} → ${sh.dest_city}`);
        setText('ws-map-floating-hub', sh.current_hub);
        setText('ws-map-dep-city', sh.origin_city);
        setText('ws-map-arr-city', sh.dest_city);
        setHref('ws-map-expand-link', sh.show_url);

        renderShipmentOnMap(sh, true);
    };

    initDashboardOsmMap();
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /app/applet/resources/views/components/nex-workspace.blade.php ENDPATH**/ ?>