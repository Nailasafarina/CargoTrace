@props([
    'metrics' => [],
    'role' => 'admin',
])

@php
    $workspaceShipments = $metrics['workspace_shipments'] ?? [];
    $firstShipment = $workspaceShipments[0] ?? [
        'id' => 0,
        'bay' => 'A9',
        'number' => '530-245-IP',
        'short_code' => 'SHP - 5430',
        'status_value' => 'IN_TRANSIT',
        'status_label' => 'in transit',
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

    // Map shipments into the exact bay slots shown in the reference screenshot: A1, A3, A8, A9, A10
    $bayA1 = $workspaceShipments[1] ?? $firstShipment;
    $bayA3 = $workspaceShipments[2] ?? array_merge($firstShipment, ['weight_formatted' => '1000kg', 'short_code' => 'SHP - 5431']);
    $bayA8 = $workspaceShipments[3] ?? $firstShipment;
    $bayA9 = $workspaceShipments[0] ?? $firstShipment; // Highlighted orange active slot with 3D stacked cardboard boxes
    $bayA10 = $workspaceShipments[4] ?? array_merge($bayA3, ['weight_formatted' => '1000kg']);
@endphp

<!-- NexCargo / CargoTrace Interactive Freight Command Center (Matching Reference Screenshot) -->
<section class="space-y-4 mb-14" aria-label="Interactive Cargo & Route Workspace">
    <!-- Top Section: Left Controls & Studio Cutaway Semi-Truck with 2-Row Cargo Bays -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">

        <!-- Left Column (3 Cols on XL): Mode Circles, Vehicle Header Card & Shipment Plannings -->
        <div class="xl:col-span-3 space-y-2.5">
            <!-- 3 Circular Mode Buttons -->
            <div class="flex items-center gap-2.5">
                <a href="{{ route('shipments.index') }}" class="w-9 h-9 rounded-full bg-[#FF6B1A] text-white flex items-center justify-center shadow-sm shadow-[#FF6B1A]/25 transition hover:scale-105" title="Active Fleet Transport" aria-label="Active Fleet Transport">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                    </svg>
                </a>
                <a href="{{ route('shipments.index') }}" class="w-9 h-9 rounded-full bg-white text-zinc-700 hover:text-zinc-950 flex items-center justify-center shadow-[0_2px_10px_rgba(0,0,0,0.03)] transition hover:scale-105" title="Cargo Packages" aria-label="Cargo Packages">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </a>
                <a href="{{ auth()->user()->isCustomer() ? route('shipments.index') : route('locations.index') }}" class="w-9 h-9 rounded-full bg-white text-zinc-700 hover:text-zinc-950 flex items-center justify-center shadow-[0_2px_10px_rgba(0,0,0,0.03)] transition hover:scale-105" title="Route Corridors" aria-label="Route Corridors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </a>
            </div>

            <!-- Active Carrier / Vehicle Card (Scania E3360 + Side-Profile Box Truck) -->
            <div class="bg-white rounded-[20px] px-4 py-3 shadow-sm flex items-center justify-between gap-2">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span id="ws-carrier-title" class="text-[14px] font-bold text-zinc-900 truncate">Scania E3360</span>
                        <span id="ws-top-status-pill" class="px-2.5 py-0.5 rounded-full bg-[#F2F3F5] text-zinc-700 text-[10px] font-semibold whitespace-nowrap">
                            {{ strtolower($firstShipment['status_label']) }}
                        </span>
                    </div>
                    <div id="ws-carrier-sub" class="text-xs text-zinc-400 mt-0.5 truncate">
                        Volvo F-243 &bull; {{ $firstShipment['carrier_name'] }}
                    </div>
                </div>
                <!-- Realistic Side-Profile White Box Truck Illustration -->
                <div class="w-28 h-11 shrink-0 flex items-center justify-end select-none">
                    <svg viewBox="0 0 156 64" class="w-full h-full">
                        <defs>
                            <linearGradient id="miniTrailerGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#FFFFFF" />
                                <stop offset="80%" stop-color="#F4F5F7" />
                                <stop offset="100%" stop-color="#E2E5EC" />
                            </linearGradient>
                            <linearGradient id="miniCabGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#FFFFFF" />
                                <stop offset="100%" stop-color="#DCE0E8" />
                            </linearGradient>
                        </defs>
                        <!-- Ground Shadow -->
                        <ellipse cx="80" cy="56" rx="70" ry="3.5" fill="#18181B" opacity="0.14" />
                        <!-- Trailer Container Box -->
                        <rect x="42" y="8" width="108" height="38" rx="2" fill="url(#miniTrailerGrad)" stroke="#CBD0D8" stroke-width="1.2"/>
                        <line x1="68" y1="9" x2="68" y2="45" stroke="#E4E7EC" stroke-width="1"/>
                        <line x1="94" y1="9" x2="94" y2="45" stroke="#E4E7EC" stroke-width="1"/>
                        <line x1="120" y1="9" x2="120" y2="45" stroke="#E4E7EC" stroke-width="1"/>
                        <!-- Dark Undercarriage & Chassis -->
                        <rect x="14" y="45" width="134" height="5" rx="1.5" fill="#27272A"/>
                        <!-- Semi Cab -->
                        <path d="M16 20 Q18 13 26 12 L39 12 L39 46 L10 46 L10 31 Q10 24 16 20 Z" fill="url(#miniCabGrad)" stroke="#BFC5D0" stroke-width="1.2"/>
                        <!-- Cab Windshield & Side Window -->
                        <path d="M14 28 L18 18 L31 18 L31 28 Z" fill="#1E293B"/>
                        <!-- Wheels -->
                        <circle cx="24" cy="50" r="6.5" fill="#18181B"/><circle cx="24" cy="50" r="3" fill="#D4D4D8"/>
                        <circle cx="55" cy="50" r="6.5" fill="#18181B"/><circle cx="55" cy="50" r="3" fill="#D4D4D8"/>
                        <circle cx="126" cy="50" r="6.5" fill="#18181B"/><circle cx="126" cy="50" r="3" fill="#D4D4D8"/>
                        <circle cx="140" cy="50" r="6.5" fill="#18181B"/><circle cx="140" cy="50" r="3" fill="#D4D4D8"/>
                    </svg>
                </div>
            </div>

            <!-- Shipment Plannings Card -->
            <div class="bg-white rounded-[22px] p-4 shadow-sm space-y-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-[#F4F5F7] text-zinc-800 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-zinc-900">Shipment Plannings</h2>
                </div>

                <!-- Primary & Secondary Pill Buttons (New Shipment + | Remove Shipment) -->
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ $createUrl }}" class="py-2 px-3 rounded-full bg-[#1C1D21] hover:bg-zinc-800 text-white text-[11px] font-semibold flex items-center justify-center gap-1.5 shadow-xs transition whitespace-nowrap">
                        <span>New Shipment</span>
                        <span class="text-sm leading-none font-normal">+</span>
                    </a>
                    <a id="ws-remove-shipment-link" href="{{ $firstShipment['show_url'] }}" class="py-2 px-3 rounded-full bg-[#F7F8FA] hover:bg-zinc-100 border border-zinc-200/70 text-zinc-700 text-[11px] font-medium flex items-center justify-center gap-1.5 transition whitespace-nowrap">
                        <span>Remove Shipment</span>
                        <svg class="w-3.5 h-3.5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </a>
                </div>

                <!-- Plannings Table Header -->
                <div class="grid grid-cols-12 text-[11px] text-zinc-400 pt-0.5">
                    <div class="col-span-3">Items</div>
                    <div class="col-span-6">Route</div>
                    <div class="col-span-3 text-right">Status</div>
                </div>

                <!-- Interactive Plannings Rows (with bottom fade effect on last item matching screenshot) -->
                <div class="relative space-y-0.5">
                    @forelse(collect($workspaceShipments)->take(5) as $idx => $ws)
                        <button
                            type="button"
                            onclick="window.selectCargoWorkspaceShipment({{ $idx }})"
                            data-ws-row="{{ $idx }}"
                            class="w-full grid grid-cols-12 items-center py-1.5 px-2 rounded-xl text-xs transition cursor-pointer text-left {{ $idx === 0 ? 'bg-[#FFF5F0] text-zinc-900 font-semibold' : 'hover:bg-zinc-50 text-zinc-700' }} {{ $idx === 4 ? 'opacity-45' : '' }}"
                        >
                            <div class="col-span-3 flex items-center gap-1.5 text-zinc-800">
                                <svg class="w-3.5 h-3.5 text-zinc-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span class="tabular-nums font-medium">{{ $ws['items_count'] }}</span>
                            </div>
                            <div class="col-span-6 truncate pr-2 text-zinc-800 font-medium" title="{{ $ws['origin_city'] }} to {{ $ws['dest_city'] }}">
                                {{ $ws['origin_city'] }} to {{ $ws['dest_city'] }}
                            </div>
                            <div class="col-span-3 flex items-center justify-end gap-1.5 text-[11px] font-medium text-zinc-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E]"></span>
                                <span class="truncate">{{ $idx === 0 ? 'Transit' : $ws['status_short'] }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="py-5 text-center text-xs text-zinc-400">
                            No active shipments planned yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Center/Right Column (9 Cols on XL): Cutaway Semi-Truck Cab + 2-Row Cargo Bay Trailer + Dual Commercial Axles -->
        <div class="xl:col-span-9">
            <div class="overflow-x-auto">
                <div class="min-w-[850px]">

                    <!-- Upper Assembly: Semi-Truck Cab (Left) + Metallic Cutaway Cargo Container (Right) -->
                    <div class="flex items-end">

                        <!-- Left: Studio-Shaded White European Semi-Truck Cab (Scania R-Series Profile) -->
                        <div class="w-[244px] shrink-0 relative z-20 -mr-1 flex flex-col justify-end select-none pointer-events-none">
                            <svg viewBox="0 0 256 290" class="w-full h-auto overflow-visible">
                                <defs>
                                    <!-- Studio Multi-Stop White Body Shading -->
                                    <linearGradient id="scaniaCabMain" x1="15%" y1="5%" x2="85%" y2="95%">
                                        <stop offset="0%" stop-color="#FFFFFF" />
                                        <stop offset="48%" stop-color="#F7F8FA" />
                                        <stop offset="82%" stop-color="#E5E8EF" />
                                        <stop offset="100%" stop-color="#CFD5E0" />
                                    </linearGradient>
                                    <linearGradient id="scaniaRoofGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#FFFFFF" />
                                        <stop offset="70%" stop-color="#F0F2F6" />
                                        <stop offset="100%" stop-color="#DCE1EA" />
                                    </linearGradient>
                                    <linearGradient id="scaniaDoorRecess" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#DFE3EC" />
                                        <stop offset="50%" stop-color="#F3F5F8" />
                                        <stop offset="100%" stop-color="#CFD5E0" />
                                    </linearGradient>
                                    <linearGradient id="scaniaGlassGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#0F172A" />
                                        <stop offset="45%" stop-color="#1E293B" />
                                        <stop offset="100%" stop-color="#090D16" />
                                    </linearGradient>
                                    <linearGradient id="scaniaArchGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                        <stop offset="0%" stop-color="#4B5563" />
                                        <stop offset="100%" stop-color="#1F242D" />
                                    </linearGradient>
                                </defs>

                                <!-- Rear Black Rubber Air Deflector Seal Between Cab and Trailer -->
                                <path d="M234 6 L254 2 L254 288 L234 288 Z" fill="#27272A" />
                                <path d="M226 10 L242 6 L242 288 L226 288 Z" fill="#BFC6D2" />

                                <!-- High-Roof Aerodynamic Spoiler & Crown -->
                                <path d="M88 42 Q116 10 188 8 L232 8 L232 60 L68 60 Q76 48 88 42 Z" fill="url(#scaniaRoofGrad)" stroke="#CBD0D8" stroke-width="1" />
                                <!-- Sculpted Roof Air Duct Contour -->
                                <path d="M108 28 Q142 15 216 15 L228 15 L228 41 L92 44 Z" fill="#FFFFFF" opacity="0.7" />
                                <!-- Exterior Sun Visor Above Windshield -->
                                <path d="M58 60 L86 53 L86 67 L53 71 Q53 63 58 60 Z" fill="#D5DAE3" stroke="#9CA3AF" stroke-width="1" />
                                <path d="M55 64 L74 60 L74 67 L53 70 Z" fill="#18181B" />

                                <!-- Main Cab Body Shell -->
                                <path d="M68 58 L232 58 L232 286 L26 286 L26 180 Q26 152 36 124 L68 58 Z" fill="url(#scaniaCabMain)" stroke="#C4CAD4" stroke-width="1.2" />

                                <!-- Vertical Panel Seam Behind Door -->
                                <line x1="202" y1="58" x2="202" y2="266" stroke="#B8BFC9" stroke-width="1.2" />
                                <!-- Rear Sleeper Side Panel Emboss -->
                                <rect x="208" y="78" width="17" height="72" rx="3" fill="#E1E5EE" stroke="#CBD0D8" stroke-width="1" />

                                <!-- Raked Front Windshield Edge (Left Profile) -->
                                <path d="M37 128 L66 68 L75 68 L46 131 Z" fill="#18181B" />

                                <!-- Side Door Window & Dark B-Pillar Surround -->
                                <path d="M52 131 L78 72 Q81 67 88 67 L184 67 Q192 67 192 75 L192 148 L52 148 Z" fill="#18181B" />
                                <path d="M59 129 L82 74 Q84 71 89 71 L178 71 Q184 71 184 77 L184 139 L59 141 Z" fill="url(#scaniaGlassGrad)" />
                                <!-- Diagonal Specular Glass Reflections -->
                                <polygon points="74,140 108,71 128,71 94,140" fill="#FFFFFF" opacity="0.14" />
                                <polygon points="104,140 138,71 147,71 113,140" fill="#FFFFFF" opacity="0.08" />
                                <!-- Interior Steering Wheel & Seat Silhouette Hint -->
                                <path d="M68 124 L84 110" stroke="#64748B" stroke-width="3" stroke-linecap="round" opacity="0.55" />
                                <path d="M138 139 L146 98 Q148 90 156 92 L162 139 Z" fill="#334155" opacity="0.5" />

                                <!-- Heavy-Duty Exterior Side Mirror & Twin Bracket Arms -->
                                <path d="M66 79 L38 79 Q32 79 32 85 L32 136 Q32 142 38 142 L58 142" fill="none" stroke="#18181B" stroke-width="3.8" stroke-linecap="round" />
                                <!-- Wide-Angle Upper Mirror & Main Lower Mirror Housing -->
                                <rect x="23" y="64" width="13" height="15" rx="3" fill="#27272A" stroke="#090D16" stroke-width="1" />
                                <rect x="21" y="86" width="16" height="46" rx="5" fill="#1F242D" stroke="#090D16" stroke-width="1" />
                                <path d="M24 90 L33 90 L33 128 L24 128 Z" fill="#374151" opacity="0.45" />

                                <!-- Sculpted Lower Door Panel Recess & Beltline Creases -->
                                <path d="M46 160 L192 160 L192 234 L46 234 Z" fill="url(#scaniaDoorRecess)" stroke="#C6CCD6" stroke-width="1" />
                                <line x1="28" y1="174" x2="228" y2="174" stroke="#B8BFC9" stroke-width="1.5" />
                                <line x1="28" y1="208" x2="196" y2="208" stroke="#CFD5DF" stroke-width="1.2" />

                                <!-- Recessed Black & Silver Door Handle -->
                                <rect x="154" y="188" width="26" height="11" rx="4" fill="#18181B" />
                                <rect x="157" y="191" width="20" height="4" rx="2" fill="#D4D8E1" />

                                <!-- Front Left Grille Louvre Ribs & Bumper Corner -->
                                <path d="M26 180 L44 180 L44 282 L20 282 L20 192 Q20 182 26 180 Z" fill="#E3E7EF" stroke="#BFC6D2" stroke-width="1" />
                                <line x1="22" y1="192" x2="38" y2="192" stroke="#9CA3AF" stroke-width="2" />
                                <line x1="22" y1="201" x2="38" y2="201" stroke="#9CA3AF" stroke-width="2" />
                                <line x1="22" y1="210" x2="38" y2="210" stroke="#9CA3AF" stroke-width="2" />
                                <line x1="22" y1="219" x2="38" y2="219" stroke="#9CA3AF" stroke-width="2" />

                                <!-- Front Bumper & Clear Projector Headlight Unit -->
                                <path d="M16 240 L54 240 L54 288 L16 288 Q12 288 12 280 L12 248 Q12 240 16 240 Z" fill="#DCE1EA" stroke="#B4BCC8" stroke-width="1.2" />
                                <rect x="14" y="248" width="15" height="22" rx="3" fill="#FFFFFF" stroke="#94A3B8" stroke-width="1.5" />
                                <rect x="16" y="263" width="11" height="4" rx="1" fill="#FF6B1A" />
                                <!-- Recessed Boarding Step Slots in Lower Bumper -->
                                <rect x="32" y="256" width="18" height="7" rx="2" fill="#3F3F46" />
                                <rect x="32" y="271" width="18" height="7" rx="2" fill="#3F3F46" />

                                <!-- Molded Dark Metallic Front Wheel Arch Fender -->
                                <path d="M56 290 L48 290 L56 250 Q134 216 214 248 L224 290 L206 290 A72 72 0 0 0 56 290 Z" fill="url(#scaniaArchGrad)" />
                            </svg>
                        </div>

                        <!-- Right: Metallic Cutaway Cargo Container Frame with 2-Row Bento Grid -->
                        <div class="flex-1 relative z-20">
                            <div class="bg-[#ECEEF2] border-[3px] border-[#9CA3AF] shadow-[inset_0_0_0_1px_rgba(255,255,255,0.7),0_12px_28px_-10px_rgba(24,24,27,0.1)] rounded-[6px] p-3">

                                <!-- Top Row of Cargo Bays: A1 (1 col), A2 (1 col dark border + button), A3 (2 cols wide) -->
                                <div class="grid grid-cols-4 gap-2.5 mb-2.5">
                                    <!-- Bay A1 -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment({{ isset($workspaceShipments[1]) ? 1 : 0 }})"
                                        data-ws-bay="{{ isset($workspaceShipments[1]) ? 1 : 0 }}"
                                        data-bay-slot="A1"
                                        class="cargo-bay-hatch group relative h-[126px] rounded-[14px] p-3.5 text-left flex flex-col justify-between border-[1.5px] border-transparent hover:border-zinc-300 shadow-2xs transition cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A1</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E]"></span>
                                                Loading
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-xs text-zinc-400 font-normal tabular-nums">{{ $bayA1['weight_formatted'] }}</div>
                                            <div class="text-[14px] font-bold text-zinc-900 tracking-tight mt-0.5 truncate">{{ $bayA1['short_code'] }}</div>
                                        </div>
                                    </button>

                                    <!-- Bay A2 (Interactive Add Slot with Dark Charcoal Border & Centered Dark + Button) -->
                                    <a
                                        href="{{ $createUrl }}"
                                        class="group relative h-[126px] rounded-[14px] bg-white p-3.5 flex flex-col justify-between border-[1.5px] border-[#27272A] hover:border-[#FF6B1A] shadow-2xs transition"
                                        title="Allocate New Cargo to Bay A2"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A2</span>
                                        </div>
                                        <div class="flex items-center justify-center my-auto">
                                            <span class="w-10 h-10 rounded-full bg-[#1C1D21] group-hover:bg-[#FF6B1A] text-white flex items-center justify-center text-lg font-normal shadow-md transition-transform group-hover:scale-105">
                                                +
                                            </span>
                                        </div>
                                        <div class="h-2"></div>
                                    </a>

                                    <!-- Bay A3 (Wide 2-Column Double Bay) -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment({{ isset($workspaceShipments[2]) ? 2 : 0 }})"
                                        data-ws-bay="{{ isset($workspaceShipments[2]) ? 2 : 0 }}"
                                        data-bay-slot="A3"
                                        class="col-span-2 cargo-bay-hatch group relative h-[126px] rounded-[14px] p-3.5 text-left flex flex-col justify-between border-[1.5px] border-transparent hover:border-zinc-300 shadow-2xs transition cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A3</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E]"></span>
                                                {{ $bayA3['status_short'] }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-xs text-zinc-400 font-normal tabular-nums">{{ $bayA3['weight_formatted'] }}</div>
                                            <div class="text-[14px] font-bold text-zinc-900 tracking-tight mt-0.5">{{ $bayA3['short_code'] }}</div>
                                        </div>
                                    </button>
                                </div>

                                <!-- Bottom Row of Cargo Bays: A7 (+ slot), A8, A9 (Highlighted Orange Slot with 3D Stacked Parcel Boxes), A10 -->
                                <div class="grid grid-cols-4 gap-2.5">
                                    <!-- Bay A7 (Auxiliary Add Slot with Soft Gray Circle + Button) -->
                                    <a
                                        href="{{ $createUrl }}"
                                        class="group relative h-[126px] rounded-[14px] bg-white p-3.5 flex flex-col justify-between border-[1.5px] border-transparent hover:border-zinc-300 shadow-2xs transition"
                                        title="Allocate Cargo to Bay A7"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A7</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E]"></span>
                                                Loading
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-center my-auto">
                                            <span class="w-9 h-9 rounded-full bg-[#F3F4F6] group-hover:bg-[#1C1D21] text-zinc-700 group-hover:text-white flex items-center justify-center text-base font-normal transition">
                                                +
                                            </span>
                                        </div>
                                        <div class="h-2"></div>
                                    </a>

                                    <!-- Bay A8 -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment({{ isset($workspaceShipments[3]) ? 3 : 0 }})"
                                        data-ws-bay="{{ isset($workspaceShipments[3]) ? 3 : 0 }}"
                                        data-bay-slot="A8"
                                        class="cargo-bay-hatch group relative h-[126px] rounded-[14px] p-3.5 text-left flex flex-col justify-between border-[1.5px] border-transparent hover:border-zinc-300 shadow-2xs transition cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A8</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E]"></span>
                                                Loading
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-xs text-zinc-400 font-normal tabular-nums">{{ $bayA8['weight_formatted'] }}</div>
                                            <div class="text-[14px] font-bold text-zinc-900 tracking-tight mt-0.5 truncate">{{ $bayA8['short_code'] }}</div>
                                        </div>
                                    </button>

                                    <!-- Bay A9 (Default Active Bay with Vibrant Orange Border & 3D Stacked Cardboard Parcel Boxes!) -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment(0)"
                                        data-ws-bay="0"
                                        data-bay-slot="A9"
                                        class="cargo-bay-hatch group relative h-[126px] rounded-[14px] p-3.5 text-left flex flex-col justify-between border-[1.5px] border-[#FF6B1A] shadow-xs transition cursor-pointer overflow-hidden"
                                    >
                                        <div class="flex items-center justify-between w-full relative z-10">
                                            <span class="text-xs font-semibold text-zinc-900">A9</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#22C55E]"></span>
                                                Loading
                                            </span>
                                        </div>

                                        <!-- Realistic 3D Stacked Cardboard Parcel Boxes Graphic (Matching Screenshot A9) -->
                                        <div class="absolute right-0.5 bottom-3 w-[106px] h-[74px] pointer-events-none select-none">
                                            <svg viewBox="0 0 155 110" class="w-full h-full drop-shadow-[0_6px_10px_rgba(120,75,35,0.18)]">
                                                <!-- Top/Back Tall Box -->
                                                <g transform="translate(52, 6)">
                                                    <polygon points="32,0 64,13 32,26 0,13" fill="#F2D7B9" />
                                                    <polygon points="0,13 32,26 32,64 0,51" fill="#DDB68C" />
                                                    <polygon points="32,26 64,13 64,51 32,64" fill="#C4996C" />
                                                    <line x1="16" y1="6.5" x2="48" y2="19.5" stroke="#FFF6EC" stroke-width="2.5" opacity="0.75" />
                                                </g>
                                                <!-- Front Left Medium Box -->
                                                <g transform="translate(12, 38)">
                                                    <polygon points="28,0 56,12 28,24 0,12" fill="#F5DFC6" />
                                                    <polygon points="0,12 28,24 28,58 0,46" fill="#E2BC93" />
                                                    <polygon points="28,24 56,12 56,46 28,58" fill="#C89D70" />
                                                    <line x1="14" y1="6" x2="42" y2="18" stroke="#FFF6EC" stroke-width="2" opacity="0.7" />
                                                </g>
                                                <!-- Front Right Main Parcel Box with Barcode Label -->
                                                <g transform="translate(64, 34)">
                                                    <polygon points="36,0 72,15 36,30 0,15" fill="#F6E1C8" />
                                                    <polygon points="0,15 36,30 36,70 0,55" fill="#DFB88E" />
                                                    <polygon points="36,30 72,15 72,55 36,70" fill="#C29566" />
                                                    <line x1="18" y1="7.5" x2="54" y2="22.5" stroke="#FFF6EC" stroke-width="2.5" opacity="0.75" />
                                                    <!-- White Shipping Barcode Sticker -->
                                                    <polygon points="44,36 60,29 60,41 44,48" fill="#FFFFFF" opacity="0.88" />
                                                </g>
                                            </svg>
                                        </div>

                                        <div class="relative z-10">
                                            <div class="text-xs text-zinc-400 font-normal tabular-nums">{{ $bayA9['weight_formatted'] }}</div>
                                            <div class="text-[14px] font-bold text-zinc-900 tracking-tight mt-0.5 truncate">{{ $bayA9['short_code'] }}</div>
                                        </div>
                                    </button>

                                    <!-- Bay A10 -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment({{ isset($workspaceShipments[4]) ? 4 : 0 }})"
                                        data-ws-bay="{{ isset($workspaceShipments[4]) ? 4 : 0 }}"
                                        data-bay-slot="A10"
                                        class="cargo-bay-hatch group relative h-[126px] rounded-[14px] p-3.5 text-left flex flex-col justify-between border-[1.5px] border-transparent hover:border-zinc-300 shadow-2xs transition cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A10</span>
                                        </div>
                                        <div>
                                            <div class="text-xs text-zinc-400 font-normal tabular-nums">{{ $bayA10['weight_formatted'] }}</div>
                                            <div class="text-[14px] font-bold text-zinc-900 tracking-tight mt-0.5 truncate">{{ $bayA10['short_code'] }}</div>
                                        </div>
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- Unified Lower Chassis, Dual Commercial Tires (Front Steer + Rear Tractor Drive), Landing Gear & Studio Ground Shadow -->
                    <div class="relative -mt-1 z-30 select-none pointer-events-none">
                        <svg viewBox="0 0 920 96" class="w-full h-auto overflow-visible">
                            <defs>
                                <radialGradient id="truckFloorShadow" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="#18181B" stop-opacity="0.34" />
                                    <stop offset="55%" stop-color="#18181B" stop-opacity="0.14" />
                                    <stop offset="100%" stop-color="#18181B" stop-opacity="0" />
                                </radialGradient>
                                <linearGradient id="chassisBeamGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                    <stop offset="0%" stop-color="#3F444E" />
                                    <stop offset="50%" stop-color="#272A30" />
                                    <stop offset="100%" stop-color="#141619" />
                                </linearGradient>
                                <linearGradient id="tireRubberGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#32353C" />
                                    <stop offset="50%" stop-color="#1E2024" />
                                    <stop offset="100%" stop-color="#0E1013" />
                                </linearGradient>
                                <linearGradient id="steelRimGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#E4E7EC" />
                                    <stop offset="50%" stop-color="#9CA3AF" />
                                    <stop offset="100%" stop-color="#4B5563" />
                                </linearGradient>
                            </defs>

                            <!-- Studio Ground Shadow Under Tires & Chassis -->
                            <ellipse cx="265" cy="86" rx="255" ry="10" fill="url(#truckFloorShadow)" />
                            <ellipse cx="640" cy="86" rx="240" ry="7" fill="url(#truckFloorShadow)" />

                            <!-- Continuous Horizontal Trailer Subframe Beam Under Container -->
                            <rect x="235" y="2" width="675" height="16" rx="2" fill="url(#chassisBeamGrad)" />
                            <!-- Crossmembers along Trailer Frame -->
                            <rect x="460" y="4" width="8" height="12" fill="#18181B" />
                            <rect x="530" y="4" width="8" height="12" fill="#18181B" />
                            <rect x="600" y="4" width="8" height="12" fill="#18181B" />
                            <rect x="670" y="4" width="8" height="12" fill="#18181B" />
                            <rect x="740" y="4" width="8" height="12" fill="#18181B" />
                            <rect x="810" y="4" width="8" height="12" fill="#18181B" />

                            <!-- Trailer Telescopic Landing Gear Leg & Footplate (Under Bay A8/A9) -->
                            <path d="M512 14 L530 14 L526 70 L516 70 Z" fill="url(#chassisBeamGrad)" />
                            <rect x="506" y="69" width="30" height="5" rx="2" fill="#18181B" />
                            <!-- Diagonal Support Strut -->
                            <line x1="528" y1="54" x2="558" y2="18" stroke="#272A30" stroke-width="4.5" stroke-linecap="round" />

                            <!-- Rear Underrun Protection Bar Ensemble (Under Bay A9/A10) -->
                            <path d="M685 18 L695 52 L895 52 L895 45 L702 45 L694 18 Z" fill="#272A30" />
                            <rect x="710" y="18" width="6" height="28" fill="#3F444E" />
                            <rect x="815" y="18" width="6" height="28" fill="#3F444E" />

                            <!-- Ribbed Tractor Side Skirt & Fuel Tank Assembly Between Front and Rear Axles -->
                            <rect x="188" y="8" width="128" height="46" rx="4" fill="url(#chassisBeamGrad)" stroke="#4B5563" stroke-width="0.8" />
                            <line x1="194" y1="20" x2="310" y2="20" stroke="#18181B" stroke-width="2" />
                            <line x1="194" y1="32" x2="310" y2="32" stroke="#18181B" stroke-width="2" />
                            <line x1="194" y1="44" x2="310" y2="44" stroke="#18181B" stroke-width="2" />
                            <!-- Side Amber LED Marker Dot -->
                            <rect x="246" y="30" width="7" height="3.5" rx="1" fill="#FF6B1A" />

                            <!-- Rear Drive Wheel Full Quarter Fender Arch & Mudflap -->
                            <path d="M312 46 A56 56 0 0 1 424 46 L434 46 L434 66 L425 66 L425 46 A48 48 0 0 0 320 46 Z" fill="#1F242D" />

                            <!-- WHEEL 1: Front Steer Commercial Tire & 10-Hole Steel Rim (Under Cab) -->
                            <g transform="translate(132, 38)">
                                <circle cx="0" cy="0" r="46" fill="url(#tireRubberGrad)" />
                                <circle cx="0" cy="0" r="43" fill="none" stroke="#2A2D34" stroke-width="2.5" stroke-dasharray="7 4" />
                                <circle cx="0" cy="0" r="34" fill="#141619" />
                                <circle cx="0" cy="0" r="26" fill="url(#steelRimGrad)" stroke="#374151" stroke-width="1.5" />
                                <circle cx="0" cy="0" r="20" fill="#6B7280" stroke="#D1D5DB" stroke-width="1" />
                                <!-- 10 Commercial Ventilation Holes & Lug Ring -->
                                <circle cx="0" cy="-14.5" r="2.2" fill="#1F242D" />
                                <circle cx="8.5" cy="-11.8" r="2.2" fill="#1F242D" />
                                <circle cx="13.8" cy="-4.5" r="2.2" fill="#1F242D" />
                                <circle cx="13.8" cy="4.5" r="2.2" fill="#1F242D" />
                                <circle cx="8.5" cy="11.8" r="2.2" fill="#1F242D" />
                                <circle cx="0" cy="14.5" r="2.2" fill="#1F242D" />
                                <circle cx="-8.5" cy="11.8" r="2.2" fill="#1F242D" />
                                <circle cx="-13.8" cy="4.5" r="2.2" fill="#1F242D" />
                                <circle cx="-13.8" cy="-4.5" r="2.2" fill="#1F242D" />
                                <circle cx="-8.5" cy="-11.8" r="2.2" fill="#1F242D" />
                                <circle cx="0" cy="0" r="9.5" fill="#272A30" stroke="#9CA3AF" stroke-width="1.5" />
                                <circle cx="0" cy="0" r="3.5" fill="#141619" />
                            </g>

                            <!-- WHEEL 2: Rear Tractor Drive Commercial Tire & Deep-Dish Hub (Under Front of Trailer) -->
                            <g transform="translate(368, 38)">
                                <circle cx="0" cy="0" r="46" fill="url(#tireRubberGrad)" />
                                <circle cx="0" cy="0" r="43" fill="none" stroke="#2A2D34" stroke-width="2.5" stroke-dasharray="7 4" />
                                <circle cx="0" cy="0" r="34" fill="#141619" />
                                <circle cx="0" cy="0" r="26" fill="url(#steelRimGrad)" stroke="#374151" stroke-width="1.5" />
                                <circle cx="0" cy="0" r="19.5" fill="#4B5563" stroke="#9CA3AF" stroke-width="1" />
                                <!-- 10 Commercial Drive Wheel Holes -->
                                <circle cx="0" cy="-14.5" r="2.2" fill="#141619" />
                                <circle cx="8.5" cy="-11.8" r="2.2" fill="#141619" />
                                <circle cx="13.8" cy="-4.5" r="2.2" fill="#141619" />
                                <circle cx="13.8" cy="4.5" r="2.2" fill="#141619" />
                                <circle cx="8.5" cy="11.8" r="2.2" fill="#141619" />
                                <circle cx="0" cy="14.5" r="2.2" fill="#141619" />
                                <circle cx="-8.5" cy="11.8" r="2.2" fill="#141619" />
                                <circle cx="-13.8" cy="4.5" r="2.2" fill="#141619" />
                                <circle cx="-13.8" cy="-4.5" r="2.2" fill="#141619" />
                                <circle cx="-8.5" cy="-11.8" r="2.2" fill="#141619" />
                                <!-- Heavy Planetary Rear Axle Hub -->
                                <circle cx="0" cy="0" r="10.5" fill="#1F242D" stroke="#D1D5DB" stroke-width="1.2" />
                                <circle cx="0" cy="0" r="4.5" fill="#374151" />
                            </g>
                        </svg>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: 4 Equal White Cards Matching Reference Screenshot -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 pt-0.5">

        <!-- CARD 1: Shipment Informations -->
        <div class="bg-white rounded-[22px] p-5 shadow-sm flex flex-col justify-between space-y-4">
            <div class="space-y-3.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-[#F4F5F7] text-zinc-800 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900">Shipment Informations</h3>
                </div>

                <!-- Driver Row -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <!-- Illustrated Driver Portrait Avatar -->
                        <div class="w-9 h-9 rounded-full bg-[#F2F3F5] overflow-hidden shrink-0 border border-zinc-200 flex items-center justify-center">
                            <svg viewBox="0 0 40 40" class="w-full h-full">
                                <rect width="40" height="40" fill="#E5E7EB" />
                                <circle cx="20" cy="15" r="7" fill="#D97757" />
                                <path d="M13 13 Q20 6 27 13 Q25 9 20 9 Q15 9 13 13 Z" fill="#18181B" />
                                <path d="M8 38 Q20 25 32 38 Z" fill="#18181B" />
                                <path d="M15 29 L20 35 L25 29 Z" fill="#FF6B1A" />
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[11px] text-zinc-400">Driver</div>
                            <div id="ws-operator-name" class="text-sm font-bold text-zinc-900 truncate">{{ $firstShipment['operator_name'] }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a id="ws-info-search-link" href="{{ $firstShipment['show_url'] }}" class="w-8 h-8 rounded-lg bg-[#F7F8FA] hover:bg-zinc-100 border border-zinc-200/70 text-zinc-700 flex items-center justify-center transition" title="Inspect Shipment" aria-label="Inspect Shipment">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </a>
                        <a id="ws-info-card-link" href="{{ $firstShipment['show_url'] }}" class="w-8 h-8 rounded-lg bg-[#F7F8FA] hover:bg-zinc-100 border border-zinc-200/70 text-zinc-700 flex items-center justify-center transition" title="Open Telemetry" aria-label="Open Telemetry">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- 3-Column Specs (Truck ID | Dock | Started) -->
                <div class="grid grid-cols-3 gap-2 pt-1">
                    <div>
                        <div class="text-[11px] text-zinc-400">Truck ID</div>
                        <div id="ws-truck-id" class="text-xs font-bold text-zinc-900 mt-0.5">{{ $firstShipment['carrier_code'] }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">Dock</div>
                        <div id="ws-dock-label" class="text-xs font-bold text-zinc-900 mt-0.5">{{ $firstShipment['dock_label'] }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">Started</div>
                        <div id="ws-started-time" class="text-xs font-bold text-zinc-900 mt-0.5 tabular-nums">{{ $firstShipment['started_time'] }}</div>
                    </div>
                </div>

                <!-- Departure -> Arrival Hatched Progress Bar -->
                <div>
                    <div class="flex items-center justify-between text-[11px] text-zinc-400 mb-1">
                        <span>Departure</span>
                        <span>Arrival</span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span id="ws-dep-city" class="text-xs font-bold text-zinc-900 truncate max-w-[85px]">{{ $firstShipment['origin_city'] }}</span>
                        <div class="flex-1 h-2.5 rounded-full route-track-hatch relative flex items-center justify-center mx-1">
                            <span class="w-5 h-5 rounded-full bg-[#1C1D21] text-white flex items-center justify-center shadow-xs">
                                <svg class="w-2.5 h-2.5 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                </svg>
                            </span>
                        </div>
                        <span id="ws-arr-city" class="text-xs font-bold text-zinc-900 truncate max-w-[85px] text-right">{{ $firstShipment['dest_city'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (Change Route | Edit Route) -->
            <div class="grid grid-cols-2 gap-2">
                <a id="ws-view-btn" href="{{ $firstShipment['show_url'] }}" class="py-2.5 px-3 rounded-full border border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 text-xs font-semibold text-zinc-800 text-center transition">
                    Change Route
                </a>
                <a id="ws-edit-btn" href="{{ $canCreateShipment ? $firstShipment['edit_url'] : $firstShipment['show_url'] }}" class="py-2.5 px-3 rounded-full border border-zinc-200 hover:border-zinc-300 hover:bg-zinc-50 text-xs font-semibold text-zinc-800 text-center transition">
                    Edit Route
                </a>
            </div>
        </div>

        <!-- CARD 2: Tracking Details -->
        <div class="bg-white rounded-[22px] p-5 shadow-sm flex flex-col justify-between space-y-3">
            <div class="space-y-3.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-[#F4F5F7] text-zinc-800 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900">Tracking Details</h3>
                </div>

                <!-- Tracking ID & Status Pill -->
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <div class="text-[11px] text-zinc-400">Tracking ID</div>
                        <a id="ws-tracking-number" href="{{ $firstShipment['show_url'] }}" class="text-sm font-bold text-zinc-900 hover:text-[#FF6B1A] truncate block mt-0.5 transition">
                            #{{ $firstShipment['number'] }}
                        </a>
                    </div>
                    <span id="ws-tracking-status" class="px-3 py-1 rounded-full bg-[#F4F5F7] text-zinc-700 text-[11px] font-semibold whitespace-nowrap">
                        {{ strtolower($firstShipment['status_label']) }}
                    </span>
                </div>

                <!-- 3-Step Vertical Connected Timeline -->
                <div class="relative pl-0.5 space-y-4 pt-1">
                    <!-- Vertical Dashed Connector Line -->
                    <div class="absolute left-[13px] top-3 bottom-3 w-px border-l border-dashed border-zinc-300"></div>

                    <!-- Step 1: Current Location (Active Orange Check) -->
                    <div class="relative flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#FF6B1A] text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs shadow-[#FF6B1A]/30 z-10">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[11px] text-zinc-400">Current Location</div>
                            <div id="ws-track-current" class="text-xs font-bold text-zinc-900 truncate mt-0.5">
                                {{ $firstShipment['current_city'] }}, {{ $firstShipment['current_country'] }}
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Departure -->
                    <div class="relative flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#F4F5F7] text-zinc-500 flex items-center justify-center shrink-0 mt-0.5 z-10">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[11px] text-zinc-400">Departure</div>
                            <div id="ws-track-departure" class="text-xs font-bold text-zinc-900 truncate mt-0.5">
                                {{ $firstShipment['origin_city'] }}, {{ $firstShipment['origin_country'] }}
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Arrival -->
                    <div class="relative flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#F4F5F7] text-zinc-500 flex items-center justify-center shrink-0 mt-0.5 z-10">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[11px] text-zinc-400">Arrival</div>
                            <div id="ws-track-arrival" class="text-xs font-bold text-zinc-900 truncate mt-0.5">
                                {{ $firstShipment['dest_city'] }}, {{ $firstShipment['dest_country'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3: Route Transportation (Interactive OpenStreetMap with Smooth Fractional Zoom!) -->
        <div class="bg-white rounded-[22px] p-5 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-full bg-[#F4F5F7] text-zinc-800 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-zinc-900">Route Transportation</h3>
                        <p id="ws-map-subtitle" class="text-[11px] text-zinc-400 truncate">
                            {{ $firstShipment['origin_city'] }} &rarr; {{ $firstShipment['dest_city'] }}
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
                    <a id="ws-map-expand-link" href="{{ $firstShipment['show_url'] }}" class="w-7 h-7 rounded-full hover:bg-zinc-100 text-zinc-700 hover:text-zinc-900 flex items-center justify-center transition" title="Open Full Corridor Map" aria-label="Open Full Corridor Map">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H8M17 7v9" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Interactive OpenStreetMap Container with Top & Bottom Floating Overlays -->
            <div class="relative rounded-2xl overflow-hidden border border-zinc-200/60 h-[204px] bg-[#E9ECEF]">
                <div id="dashboard-osm-map" class="w-full h-full z-0"></div>

                <!-- Floating White Hub Address Pill at Top Center (Matching Screenshot) -->
                <div class="absolute top-2.5 left-1/2 -translate-x-1/2 z-[400] pointer-events-none">
                    <div id="ws-map-floating-hub" class="px-3 py-1 rounded-full bg-white/95 backdrop-blur-xs text-zinc-800 text-[10px] font-semibold shadow-md border border-black/[0.05] whitespace-nowrap max-w-[215px] truncate">
                        {{ $firstShipment['current_hub'] }}, {{ $firstShipment['current_city'] }}
                    </div>
                </div>

                <!-- Floating Departure -> Arrival Overlay Card at Bottom -->
                <div class="absolute bottom-2 inset-x-2 z-[400] bg-white/95 backdrop-blur-md rounded-xl px-3 py-1.5 shadow-md border border-black/[0.04]">
                    <div class="flex items-center justify-between text-[10px] text-zinc-400">
                        <span>Departure</span>
                        <span>Arrival</span>
                    </div>
                    <div class="flex items-center justify-between gap-2 mt-0.5">
                        <span id="ws-map-dep-city" class="text-xs font-bold text-zinc-900 truncate max-w-[75px]">{{ $firstShipment['origin_city'] }}</span>
                        <div class="flex-1 flex items-center justify-center relative">
                            <div class="w-full border-t-2 border-dashed border-[#FF6B1A]/35"></div>
                            <span class="absolute w-5 h-5 rounded-full bg-[#FF6B1A] text-white flex items-center justify-center shadow-xs">
                                <svg class="w-2.5 h-2.5 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                </svg>
                            </span>
                        </div>
                        <span id="ws-map-arr-city" class="text-xs font-bold text-zinc-900 truncate max-w-[75px] text-right">{{ $firstShipment['dest_city'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 4: Vehicle Health -->
        <div class="bg-white rounded-[22px] p-5 shadow-sm flex flex-col justify-between space-y-3">
            <div class="space-y-3">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-full bg-[#F4F5F7] text-zinc-800 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="8" stroke-width="1.8" />
                                <circle cx="12" cy="12" r="3" stroke-width="1.8" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-zinc-900">Vehicle Health</h3>
                            <p class="text-[11px] text-zinc-400">5000 miles</p>
                        </div>
                    </div>
                    <a href="#operational-analytics" class="w-7 h-7 rounded-full hover:bg-zinc-100 text-zinc-700 hover:text-zinc-900 flex items-center justify-center transition" title="View Operational Analytics" aria-label="View Operational Analytics">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H8M17 7v9" />
                        </svg>
                    </a>
                </div>

                <!-- 3 Metric Columns (Services | Engine Health | Active Alert) -->
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <div class="text-[11px] text-zinc-400">Services</div>
                        <div class="text-sm font-bold text-zinc-900 mt-0.5 tabular-nums">1,200 miles</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">Engine Health</div>
                        <div class="text-sm font-bold text-zinc-900 mt-0.5 tabular-nums">{{ $metrics['kpis']['on_time_delivery_rate'] ?? 89 }}%</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">Active Alert</div>
                        <div class="text-sm font-bold text-zinc-900 mt-0.5 tabular-nums">{{ $metrics['open_exceptions'] ?? 12 }}</div>
                    </div>
                </div>
            </div>

            <!-- 11 Alternating Orange & Sage Vertical Pill Bars (Exact Match to Reference Screenshot) -->
            <div class="h-30 flex items-end justify-between gap-2 pt-2 px-1">
                <div class="w-3 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 76%"></div>
                <div class="w-3 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 46%"></div>
                <div class="w-3 rounded-full bg-[#DFEAE4] transition-all duration-300" style="height: 68%"></div>
                <div class="w-3 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 36%"></div>
                <div class="w-3 rounded-full bg-[#DFEAE4] transition-all duration-300" style="height: 98%"></div>
                <div class="w-3 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 82%"></div>
                <div class="w-3 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 34%"></div>
                <div class="w-3 rounded-full bg-[#DFEAE4] transition-all duration-300" style="height: 88%"></div>
                <div class="w-3 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 64%"></div>
                <div class="w-3 rounded-full bg-[#DFEAE4] transition-all duration-300" style="height: 42%"></div>
                <div class="w-3 rounded-full bg-[#FF6B1A] transition-all duration-300" style="height: 56%"></div>
            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const shipments = @json($workspaceShipments);
    const defaultShipment = @json($firstShipment);
    const canEdit = @json($canCreateShipment);
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
            attributionControl: false,
            scrollWheelZoom: true,
            zoomSnap: 0.25,
            zoomDelta: 0.5,
            wheelPxPerZoomLevel: 60,
            wheelDebounceTime: 18,
            zoomAnimation: true,
            fadeAnimation: true,
            markerZoomAnimation: true,
            inertia: true,
            inertiaDeceleration: 3000,
        });

        window.L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            keepBuffer: 4,
            updateWhenZooming: false,
            updateWhenIdle: false,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer">OpenStreetMap</a>'
        }).addTo(dashboardMap);

        routeLayerGroup = window.L.featureGroup().addTo(dashboardMap);

        renderShipmentOnMap(shipments.length > 0 ? shipments[0] : defaultShipment, false);

        setTimeout(() => {
            try { dashboardMap.invalidateSize(); } catch (e) {}
        }, 250);
    }

    function createNexPin(color) {
        return window.L.divIcon({
            className: 'cargotrace-pin-wrapper',
            html: `
                <div class="relative flex items-center justify-center">
                    <div style="background-color: ${color};" class="w-4 h-4 rounded-full border-2 border-white shadow-md flex items-center justify-center">
                        <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                    </div>
                </div>
            `,
            iconSize: [16, 16],
            iconAnchor: [8, 8],
            popupAnchor: [0, -10],
        });
    }

    function renderShipmentOnMap(sh, animateFly = true) {
        if (!dashboardMap || !routeLayerGroup) return;

        routeLayerGroup.clearLayers();

        const originPt = [Number(sh.origin_lat), Number(sh.origin_lng)];
        const currentPt = [Number(sh.current_lat), Number(sh.current_lng)];
        const destPt = [Number(sh.dest_lat), Number(sh.dest_lng)];

        // Build smooth curved S-curve segments like the reference screenshot
        const curveFn = typeof window.buildCurvedSegment === 'function'
            ? window.buildCurvedSegment
            : (a, b) => [a, b];

        const activeArc = curveFn(originPt, currentPt, 0.16, 24);
        const remainingArc = curveFn(currentPt, destPt, -0.16, 24);

        // 1. Completed solid orange segment
        window.L.polyline(activeArc.length > 1 ? activeArc : [originPt, currentPt], {
            color: '#FF6B1A',
            weight: 2.5,
            opacity: 0.95,
            lineCap: 'round',
        }).addTo(routeLayerGroup);

        // 2. Remaining dashed dark segment
        window.L.polyline(remainingArc.length > 1 ? remainingArc : [currentPt, destPt], {
            color: '#18181B',
            weight: 2,
            opacity: 0.85,
            dashArray: '4, 5',
            lineCap: 'round',
        }).addTo(routeLayerGroup);

        // Origin Marker
        window.L.marker(originPt, { icon: createNexPin('#18181B') })
            .bindPopup(`<strong>Departure: ${sh.origin_city}</strong><br><span class="text-xs">${sh.origin_name}</span>`)
            .addTo(routeLayerGroup);

        // Current Position Marker (Orange)
        window.L.marker(currentPt, { icon: createNexPin('#FF6B1A') })
            .bindPopup(`<strong>Current: ${sh.current_city}</strong><br><span class="text-xs">${sh.current_hub}</span>`)
            .addTo(routeLayerGroup);

        // Destination Marker
        window.L.marker(destPt, { icon: createNexPin('#18181B') })
            .bindPopup(`<strong>Arrival: ${sh.dest_city}</strong><br><span class="text-xs">${sh.dest_name}</span>`)
            .addTo(routeLayerGroup);

        activeBounds = routeLayerGroup.getBounds().pad(0.22);

        if (animateFly) {
            dashboardMap.flyToBounds(activeBounds, {
                paddingTopLeft: [24, 34],
                paddingBottomRight: [24, 62],
                maxZoom: 6,
                duration: 0.85,
                easeLinearity: 0.25,
            });
        } else {
            dashboardMap.fitBounds(activeBounds, {
                paddingTopLeft: [24, 34],
                paddingBottomRight: [24, 62],
                maxZoom: 6,
            });
        }
    }

    window.resetCargoWorkspaceMapBounds = function () {
        if (dashboardMap && activeBounds) {
            dashboardMap.flyToBounds(activeBounds, {
                paddingTopLeft: [24, 34],
                paddingBottomRight: [24, 62],
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
            el.classList.toggle('bg-[#FFF5F0]', isMatch);
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

        setText('ws-top-status-pill', String(sh.status_label).toLowerCase());
        setText('ws-carrier-sub', `Volvo F-243 • ${sh.carrier_name}`);
        setHref('ws-remove-shipment-link', sh.show_url);

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

        setText('ws-map-subtitle', `${sh.origin_city} → ${sh.dest_city}`);
        setText('ws-map-floating-hub', `${sh.current_hub}, ${sh.current_city}`);
        setText('ws-map-dep-city', sh.origin_city);
        setText('ws-map-arr-city', sh.dest_city);
        setHref('ws-map-expand-link', sh.show_url);

        renderShipmentOnMap(sh, true);
    };

    initDashboardOsmMap();
});
</script>
@endpush
