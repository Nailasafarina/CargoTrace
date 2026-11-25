@props([
    'metrics' => [],
    'role' => 'admin',
])

@php
    $workspaceShipments = $metrics['workspace_shipments'] ?? [];
    $firstShipment = $workspaceShipments[0] ?? [
        'id' => 0,
        'bay' => 'A9',
        'number' => 'CT-CORRIDOR-01',
        'short_code' => 'SHP - 0001',
        'status_value' => 'IN_TRANSIT',
        'status_label' => 'In Transit',
        'status_short' => 'Transit',
        'is_delayed' => false,
        'delay_label' => 'On Time',
        'delay_hours' => 0,
        'weight_kg' => 500,
        'weight_formatted' => '500kg',
        'volume_formatted' => '18.0 m³',
        'cargo_type' => 'General Container Cargo',
        'items_count' => 1,
        'carrier_name' => 'Assigned Carrier',
        'carrier_code' => 'CAR-201',
        'operator_name' => 'Operations Lead',
        'customer_name' => 'Consignee',
        'dock_label' => 'Bay A9',
        'started_time' => 'Scheduled',
        'current_event_time' => '—',
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
        'current_city' => 'Singapore',
        'current_country' => 'SG',
        'current_hub' => 'PSA Singapore Terminal',
        'current_lat' => 1.2644,
        'current_lng' => 103.8222,
        'waypoints' => [],
        'show_url' => route('shipments.index'),
        'edit_url' => route('shipments.index'),
        'can_edit' => false,
    ];

    $canCreateShipment = auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isOperator());
    $createUrl = $canCreateShipment ? route('shipments.create') : route('shipments.index');
    $thresholdHours = $metrics['delay_threshold_hours'] ?? 24;

    // Assign real DB shipments into the bay slots (A9 active primary slot, A1, A3, A8, A10)
    $bayA9 = $workspaceShipments[0] ?? $firstShipment;
    $bayA1 = $workspaceShipments[1] ?? $firstShipment;
    $bayA3 = $workspaceShipments[2] ?? $firstShipment;
    $bayA8 = $workspaceShipments[3] ?? $firstShipment;
    $bayA10 = $workspaceShipments[4] ?? $bayA3;

    // Real DB telemetry series for the Operational Telemetry bar chart (11 vertical bars)
    $statusData = $metrics['status_chart']['data'] ?? [1, 2, 1, 4, 3, 0];
    $totalCount = max(1, (int) ($metrics['total_shipments'] ?? 1));
    $onTimePct = (int) round((float) ($metrics['kpis']['on_time_delivery_rate'] ?? 80));
    $delayedPct = (int) round((float) ($metrics['kpis']['delay_rate'] ?? 20));
    $openExcCount = (int) ($metrics['open_exceptions'] ?? 0);

    $telemetryBars = [
        ['label' => 'On-Time Rate', 'value' => max(18, $onTimePct), 'raw' => $onTimePct . '%'],
        ['label' => 'Created', 'value' => max(18, min(95, (int) round((($statusData[0] ?? 1) / $totalCount) * 140) + 28)), 'raw' => ($statusData[0] ?? 0) . ' units'],
        ['label' => 'Booked', 'value' => max(18, min(95, (int) round((($statusData[1] ?? 1) / $totalCount) * 140) + 34)), 'raw' => ($statusData[1] ?? 0) . ' units'],
        ['label' => 'Picked Up', 'value' => max(18, min(95, (int) round((($statusData[2] ?? 1) / $totalCount) * 140) + 24)), 'raw' => ($statusData[2] ?? 0) . ' units'],
        ['label' => 'In Transit', 'value' => max(24, min(98, (int) round((($statusData[3] ?? 2) / $totalCount) * 145) + 42)), 'raw' => ($statusData[3] ?? 0) . ' units'],
        ['label' => 'Delivered', 'value' => max(22, min(96, (int) round((($statusData[4] ?? 2) / $totalCount) * 145) + 36)), 'raw' => ($statusData[4] ?? 0) . ' units'],
        ['label' => 'Delay Rate', 'value' => max(16, min(92, $delayedPct + 20)), 'raw' => $delayedPct . '%'],
        ['label' => 'Open Alerts', 'value' => max(16, min(92, $openExcCount * 11 + 22)), 'raw' => $openExcCount . ' open'],
        ['label' => 'SLA Buffer', 'value' => max(22, min(90, 100 - min(75, $delayedPct))), 'raw' => $thresholdHours . 'h max'],
        ['label' => 'Active Fleet', 'value' => max(24, min(90, ((int) ($metrics['in_transit'] ?? 1)) * 12 + 26)), 'raw' => ($metrics['in_transit'] ?? 0) . ' active'],
        ['label' => 'Fulfilled', 'value' => max(20, min(94, ((int) ($metrics['delivered'] ?? 1)) * 12 + 30)), 'raw' => ($metrics['delivered'] ?? 0) . ' done'],
    ];
@endphp

<!-- Interactive Cargo & Route Workspace -->
<section class="space-y-4 mb-10" aria-label="Interactive Cargo & Route Workspace">
    <!-- Top Section: Left Controls + Studio Cutaway Tractor & Cargo Bays -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">

        <!-- Left Column (3 Cols on XL): Mode Filter Circles, Active Carrier Card & Shipment Plannings -->
        <div class="xl:col-span-3 space-y-2.5">
            <!-- 3 Circular Mode Filter Buttons -->
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    data-ws-filter="all"
                    onclick="window.filterCargoWorkspace('all')"
                    class="w-9 h-9 rounded-full bg-[#C93565] text-white flex items-center justify-center shadow-sm shadow-[#C93565]/25 transition hover:scale-105 cursor-pointer"
                    title="All Active Shipments"
                    aria-label="All Active Shipments"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                    </svg>
                </button>
                <button
                    type="button"
                    data-ws-filter="transit"
                    onclick="window.filterCargoWorkspace('transit')"
                    class="w-9 h-9 rounded-full bg-white text-zinc-700 hover:text-[#1F171B] hover:bg-[#FDF2F6] flex items-center justify-center shadow-[0_2px_10px_rgba(31,23,27,0.03)] transition hover:scale-105 cursor-pointer"
                    title="Filter In Transit Cargo"
                    aria-label="Filter In Transit Cargo"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </button>
                <button
                    type="button"
                    data-ws-filter="delayed"
                    onclick="window.filterCargoWorkspace('delayed')"
                    class="w-9 h-9 rounded-full bg-white text-zinc-700 hover:text-[#1F171B] hover:bg-[#FDF2F6] flex items-center justify-center shadow-[0_2px_10px_rgba(31,23,27,0.03)] transition hover:scale-105 cursor-pointer"
                    title="Filter Delayed / Alerted Shipments"
                    aria-label="Filter Delayed Shipments"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </button>
            </div>

            <!-- Active Carrier / Vehicle Card -->
            <div class="bg-white rounded-[20px] px-4 py-3 shadow-sm flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5">
                        <span id="ws-carrier-title" class="text-[13px] font-bold text-zinc-900 truncate">{{ $firstShipment['carrier_name'] }}</span>
                        <span id="ws-top-status-pill" class="px-2 py-0.5 rounded-full bg-[#F2F3F5] text-zinc-700 text-[9.5px] font-semibold whitespace-nowrap shrink-0">
                            {{ strtolower($firstShipment['status_label']) }}
                        </span>
                    </div>
                    <div id="ws-carrier-sub" class="text-[11px] text-zinc-400 mt-0.5 truncate">
                        {{ $firstShipment['carrier_code'] }} &bull; {{ $firstShipment['number'] }}
                    </div>
                </div>
                <div class="w-20 h-10 shrink-0 flex items-center justify-end select-none">
                    <img
                        src="{{ asset('images/fleet-truck-mini.png') }}"
                        alt="Active fleet transport unit"
                        class="w-full h-full object-contain"
                    />
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
                    <h2 class="text-sm font-bold text-zinc-900">{{ __('dashboard.workspace.plannings') }}</h2>
                </div>

                <!-- Action Pill Buttons (RBAC-Aware) -->
                <div class="grid grid-cols-2 gap-2">
                    @if($canCreateShipment)
                        <a href="{{ route('shipments.create') }}" class="py-2 px-3 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-[11px] font-semibold flex items-center justify-center gap-1.5 shadow-xs transition whitespace-nowrap">
                            <span>{{ __('dashboard.workspace.new_shipment') }}</span>
                            <span class="text-sm leading-none font-normal">+</span>
                        </a>
                        <a id="ws-directory-link" href="{{ route('shipments.index') }}" class="py-2 px-3 rounded-full bg-[#FAF7F9] hover:bg-[#FDF2F6] border border-[#ECE4E8] text-zinc-700 text-[11px] font-medium flex items-center justify-center gap-1.5 transition whitespace-nowrap">
                            <span>{{ __('dashboard.workspace.all_shipments') }}</span>
                            <svg class="w-3.5 h-3.5 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('shipments.index') }}" class="py-2 px-3 rounded-full bg-[#1F171B] hover:bg-[#C93565] text-white text-[11px] font-semibold flex items-center justify-center gap-1.5 shadow-xs transition whitespace-nowrap">
                            <span>{{ __('dashboard.workspace.my_shipments') }}</span>
                        </a>
                        <a id="ws-directory-link" href="{{ $firstShipment['show_url'] }}" class="py-2 px-3 rounded-full bg-[#FAF7F9] hover:bg-[#FDF2F6] border border-[#ECE4E8] text-zinc-700 text-[11px] font-medium flex items-center justify-center gap-1.5 transition whitespace-nowrap">
                            <span>{{ __('dashboard.workspace.inspect_cargo') }}</span>
                        </a>
                    @endif
                </div>

                <!-- Plannings Table Header -->
                <div class="grid grid-cols-12 text-[11px] text-zinc-400 pt-0.5 px-2">
                    <div class="col-span-3">{{ __('dashboard.workspace.items') }}</div>
                    <div class="col-span-6">{{ __('dashboard.workspace.route') }}</div>
                    <div class="col-span-3 text-right">{{ __('dashboard.workspace.status') }}</div>
                </div>

                <!-- Interactive Plannings Rows (with soft bottom fade on 5th item) -->
                <div class="relative space-y-0.5">
                    @forelse(collect($workspaceShipments)->take(5) as $idx => $ws)
                        <button
                            type="button"
                            onclick="window.selectCargoWorkspaceShipment({{ $idx }})"
                            data-ws-row="{{ $idx }}"
                            data-ws-status="{{ $ws['status_value'] }}"
                            data-ws-delayed="{{ $ws['is_delayed'] ? '1' : '0' }}"
                            class="w-full grid grid-cols-12 items-center py-1.5 px-2 rounded-xl text-xs transition cursor-pointer text-left {{ $idx === 0 ? 'bg-[#FDF2F6] text-[#1F171B] font-semibold' : 'hover:bg-[#FAF7F9] text-zinc-700' }} {{ $idx === 4 ? 'opacity-45' : '' }}"
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
                                <span class="w-1.5 h-1.5 rounded-full {{ $ws['is_delayed'] ? 'bg-[#E04879]' : 'bg-[#10B981]' }}"></span>
                                <span class="truncate">{{ $ws['status_short'] }}</span>
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

        <!-- Center/Right Column (9 Cols on XL): Studio Semi-Truck Cab + Metallic Cutaway Cargo Bays -->
        <div class="xl:col-span-9">
            <!-- Mobile Compact Cargo Bays Grid (< lg) -->
            <div class="grid grid-cols-2 gap-3 lg:hidden">
                @foreach([['bay' => 'A9', 'idx' => 0, 'sh' => $bayA9], ['bay' => 'A1', 'idx' => 1, 'sh' => $bayA1], ['bay' => 'A3', 'idx' => 2, 'sh' => $bayA3], ['bay' => 'A8', 'idx' => 3, 'sh' => $bayA8]] as $slot)
                    <button
                        type="button"
                        onclick="window.selectCargoWorkspaceShipment({{ isset($workspaceShipments[$slot['idx']]) ? $slot['idx'] : 0 }})"
                        data-ws-bay="{{ isset($workspaceShipments[$slot['idx']]) ? $slot['idx'] : 0 }}"
                        class="cargo-bay-hatch rounded-2xl p-3.5 text-left flex flex-col justify-between h-28 border-[1.5px] {{ $slot['idx'] === 0 ? 'border-[#E04879]' : 'border-zinc-200/80' }} shadow-2xs cursor-pointer"
                    >
                        <div class="flex items-center justify-between w-full">
                            <span class="text-xs font-semibold text-zinc-800">{{ $slot['bay'] }}</span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-zinc-700">
                                <span class="w-1.5 h-1.5 rounded-full {{ $slot['sh']['is_delayed'] ? 'bg-[#E04879]' : 'bg-[#10B981]' }}"></span>
                                {{ $slot['sh']['status_short'] }}
                            </span>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-400 tabular-nums">{{ $slot['sh']['weight_formatted'] }}</div>
                            <div class="text-sm font-bold text-zinc-900 truncate">{{ $slot['sh']['short_code'] }}</div>
                        </div>
                    </button>
                @endforeach
            </div>

            <!-- Desktop Photorealistic Studio Truck + Cutaway 2-Row Cargo Container (lg+) -->
            <div class="hidden lg:block overflow-x-auto pb-1">
                <div class="relative min-w-[840px] pt-2">
                    <div class="relative flex items-end">
                        <!-- Studio 3D White Semi-Truck Tractor Unit (z-30 pointer-events-none so fender & fifth-wheel naturally tuck over chassis line) -->
                        <div class="w-[475px] shrink-0 relative z-30 select-none pointer-events-none">
                            <img
                                src="{{ asset('images/scania-cab.png') }}"
                                alt="Semi-truck tractor cab"
                                class="w-full h-auto block drop-shadow-[0_10px_14px_rgba(31,23,27,0.09)]"
                            />
                        </div>

                        <!-- Metallic Cutaway Cargo Container Box Seated Flush on Fifth Wheel & Chassis -->
                        <div class="flex-1 relative z-20 -ml-[258px] mb-[92px]">
                            <!-- Metallic Outer Container Shell -->
                            <div class="bg-[#EFE9ED] border-[3px] border-[#9CA3AF] shadow-[inset_0_0_0_1px_rgba(255,255,255,0.8),0_12px_24px_-10px_rgba(31,23,27,0.12)] rounded-[6px] p-2.5">
                                <!-- Top Row: A1 (1 col), A2 (1 col dark +), A3 (2 cols wide) -->
                                <div class="grid grid-cols-4 gap-2 mb-2">
                                    <!-- Bay A1 -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment({{ isset($workspaceShipments[1]) ? 1 : 0 }})"
                                        data-ws-bay="{{ isset($workspaceShipments[1]) ? 1 : 0 }}"
                                        data-bay-slot="A1"
                                        class="cargo-bay-hatch group relative h-[108px] rounded-[12px] p-3 text-left flex flex-col justify-between border-[1.5px] border-transparent hover:border-[#F8C9DC] shadow-2xs transition cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A1</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $bayA1['is_delayed'] ? 'bg-[#E04879]' : 'bg-[#10B981]' }}"></span>
                                                {{ $bayA1['status_short'] }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-[11px] text-zinc-400 font-normal tabular-nums">{{ $bayA1['weight_formatted'] }}</div>
                                            <div class="text-[13px] font-bold text-zinc-900 tracking-tight mt-0.5 truncate">{{ $bayA1['short_code'] }}</div>
                                        </div>
                                    </button>

                                    <!-- Bay A2 (Obsidian Bordered Add Slot) -->
                                    <a
                                        href="{{ $createUrl }}"
                                        class="group relative h-[108px] rounded-[12px] bg-white p-3 flex flex-col justify-between border-[1.5px] border-[#1F171B] hover:border-[#E04879] shadow-2xs transition"
                                        title="{{ $canCreateShipment ? __('dashboard.workspace.new_shipment') : __('dashboard.workspace.all_shipments') }}"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A2</span>
                                        </div>
                                        <div class="flex items-center justify-center my-auto">
                                            <span class="w-9 h-9 rounded-full bg-[#1F171B] group-hover:bg-[#C93565] text-white flex items-center justify-center text-lg font-normal shadow-md transition-transform group-hover:scale-105">
                                                +
                                            </span>
                                        </div>
                                        <div class="h-1"></div>
                                    </a>

                                    <!-- Bay A3 (2-Column Wide Double Bay) -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment({{ isset($workspaceShipments[2]) ? 2 : 0 }})"
                                        data-ws-bay="{{ isset($workspaceShipments[2]) ? 2 : 0 }}"
                                        data-bay-slot="A3"
                                        class="col-span-2 cargo-bay-hatch group relative h-[108px] rounded-[12px] p-3 text-left flex flex-col justify-between border-[1.5px] border-transparent hover:border-[#F8C9DC] shadow-2xs transition cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A3</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $bayA3['is_delayed'] ? 'bg-[#E04879]' : 'bg-[#10B981]' }}"></span>
                                                {{ $bayA3['status_short'] }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-[11px] text-zinc-400 font-normal tabular-nums">{{ $bayA3['weight_formatted'] }}</div>
                                            <div class="text-[13px] font-bold text-zinc-900 tracking-tight mt-0.5">{{ $bayA3['short_code'] }}</div>
                                        </div>
                                    </button>
                                </div>

                                <!-- Bottom Row: A7 (+ slot), A8, A9 (Highlighted Active Bay with Camellia Pink Border), A10 -->
                                <div class="grid grid-cols-4 gap-2">
                                    <!-- Bay A7 (Auxiliary Add Slot) -->
                                    <a
                                        href="{{ $createUrl }}"
                                        class="group relative h-[108px] rounded-[12px] bg-white p-3 flex flex-col justify-between border-[1.5px] border-transparent hover:border-[#F8C9DC] shadow-2xs transition"
                                        title="{{ $canCreateShipment ? __('dashboard.workspace.new_shipment') : __('dashboard.workspace.all_shipments') }}"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A7</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                                                {{ __('dashboard.workspace.ready') }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-center my-auto">
                                            <span class="w-8 h-8 rounded-full bg-[#FAF7F9] group-hover:bg-[#1F171B] text-zinc-700 group-hover:text-white flex items-center justify-center text-base font-normal transition">
                                                +
                                            </span>
                                        </div>
                                        <div class="h-1"></div>
                                    </a>

                                    <!-- Bay A8 -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment({{ isset($workspaceShipments[3]) ? 3 : 0 }})"
                                        data-ws-bay="{{ isset($workspaceShipments[3]) ? 3 : 0 }}"
                                        data-bay-slot="A8"
                                        class="cargo-bay-hatch group relative h-[108px] rounded-[12px] p-3 text-left flex flex-col justify-between border-[1.5px] border-transparent hover:border-[#F8C9DC] shadow-2xs transition cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A8</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $bayA8['is_delayed'] ? 'bg-[#E04879]' : 'bg-[#10B981]' }}"></span>
                                                {{ $bayA8['status_short'] }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-[11px] text-zinc-400 font-normal tabular-nums">{{ $bayA8['weight_formatted'] }}</div>
                                            <div class="text-[13px] font-bold text-zinc-900 tracking-tight mt-0.5 truncate">{{ $bayA8['short_code'] }}</div>
                                        </div>
                                    </button>

                                    <!-- Bay A9 (Default Active Bay with Camellia Pink Border & Studio 3D Cardboard Boxes) -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment(0)"
                                        data-ws-bay="0"
                                        data-bay-slot="A9"
                                        class="cargo-bay-hatch group relative h-[108px] rounded-[12px] p-3 text-left flex flex-col justify-between border-[1.5px] border-[#E04879] shadow-xs transition cursor-pointer overflow-hidden"
                                    >
                                        <div class="flex items-center justify-between w-full relative z-10">
                                            <span class="text-xs font-semibold text-zinc-900">A9</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $bayA9['is_delayed'] ? 'bg-[#E04879]' : 'bg-[#10B981]' }}"></span>
                                                {{ $bayA9['status_short'] }}
                                            </span>
                                        </div>

                                        <img
                                            src="{{ asset('images/cargo-boxes.png') }}"
                                            alt="Stacked cargo boxes"
                                            class="absolute right-1.5 bottom-2 w-[66px] h-[62px] object-contain pointer-events-none select-none drop-shadow-[0_6px_10px_rgba(120,75,35,0.16)]"
                                        />

                                        <div class="relative z-10">
                                            <div class="text-[11px] text-zinc-400 font-normal tabular-nums">{{ $bayA9['weight_formatted'] }}</div>
                                            <div class="text-[13px] font-bold text-zinc-900 tracking-tight mt-0.5 truncate">{{ $bayA9['short_code'] }}</div>
                                        </div>
                                    </button>

                                    <!-- Bay A10 -->
                                    <button
                                        type="button"
                                        onclick="window.selectCargoWorkspaceShipment({{ isset($workspaceShipments[4]) ? 4 : 0 }})"
                                        data-ws-bay="{{ isset($workspaceShipments[4]) ? 4 : 0 }}"
                                        data-bay-slot="A10"
                                        class="cargo-bay-hatch group relative h-[108px] rounded-[12px] p-3 text-left flex flex-col justify-between border-[1.5px] border-transparent hover:border-[#F8C9DC] shadow-2xs transition cursor-pointer"
                                    >
                                        <div class="flex items-center justify-between w-full">
                                            <span class="text-xs font-semibold text-zinc-800">A10</span>
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-zinc-700">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $bayA10['is_delayed'] ? 'bg-[#E04879]' : 'bg-[#10B981]' }}"></span>
                                                {{ $bayA10['status_short'] }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-[11px] text-zinc-400 font-normal tabular-nums">{{ $bayA10['weight_formatted'] }}</div>
                                            <div class="text-[13px] font-bold text-zinc-900 tracking-tight mt-0.5 truncate">{{ $bayA10['short_code'] }}</div>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Precision Trailer Undercarriage Rail, Telescopic Landing Gear & Dual-Bar Side Underrun Guard -->
                            <div class="absolute top-full inset-x-0 h-[92px] pointer-events-none select-none">
                                <svg viewBox="0 0 640 92" preserveAspectRatio="none" class="w-full h-full overflow-visible">
                                    <defs>
                                        <linearGradient id="trailerBeamGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" stop-color="#3F444E" />
                                            <stop offset="55%" stop-color="#272A30" />
                                            <stop offset="100%" stop-color="#141619" />
                                        </linearGradient>
                                        <radialGradient id="trailerFloorGlow" cx="50%" cy="50%" r="50%">
                                            <stop offset="0%" stop-color="#18181B" stop-opacity="0.18" />
                                            <stop offset="100%" stop-color="#18181B" stop-opacity="0" />
                                        </radialGradient>
                                    </defs>

                                    <!-- Ambient Studio Shadow along Ground Baseline -->
                                    <ellipse cx="360" cy="83" rx="260" ry="7" fill="url(#trailerFloorGlow)" />

                                    <!-- Full-Width Metallic I-Beam Subframe Rail Flush Under Container -->
                                    <rect x="2" y="0" width="636" height="12" rx="2" fill="url(#trailerBeamGrad)" />
                                    <rect x="295" y="2" width="7" height="8" fill="#111317" />
                                    <rect x="370" y="2" width="7" height="8" fill="#111317" />
                                    <rect x="445" y="2" width="7" height="8" fill="#111317" />
                                    <rect x="520" y="2" width="7" height="8" fill="#111317" />
                                    <rect x="595" y="2" width="7" height="8" fill="#111317" />

                                    <!-- Telescopic Trailer Landing Gear Leg & Diagonal Support Strut -->
                                    <path d="M286 12 L299 12 L297 73 L288 73 Z" fill="url(#trailerBeamGrad)" />
                                    <rect x="281" y="72" width="23" height="5" rx="2" fill="#18181B" />
                                    <line x1="298" y1="54" x2="328" y2="12" stroke="#272A30" stroke-width="4" stroke-linecap="round" />

                                    <!-- Dual-Bar Horizontal Rear Side Underrun Guard & Vertical Hangers -->
                                    <rect x="448" y="12" width="6" height="32" fill="#272A30" />
                                    <rect x="586" y="12" width="6" height="32" fill="#272A30" />
                                    <rect x="434" y="22" width="180" height="7" rx="2" fill="url(#trailerBeamGrad)" />
                                    <rect x="434" y="36" width="180" height="7" rx="2" fill="url(#trailerBeamGrad)" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: 4 Equal White Bento Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

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
                    <h3 class="text-sm font-bold text-zinc-900">{{ __('dashboard.workspace.shipment_info') }}</h3>
                </div>

                <!-- Operator / Lead Row -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                        <div id="ws-operator-initials" class="w-8 h-8 rounded-full bg-[#1F171B] text-white shrink-0 flex items-center justify-center text-[11px] font-bold uppercase shadow-2xs">
                            {{ strtoupper(substr($firstShipment['operator_name'], 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.coordinator') }}</div>
                            <div id="ws-operator-name" class="text-[13px] font-bold text-zinc-900 truncate" title="{{ $firstShipment['operator_name'] }}">{{ $firstShipment['operator_name'] }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 shrink-0">
                        <a id="ws-info-search-link" href="{{ $firstShipment['show_url'] }}" class="w-7 h-7 rounded-lg bg-[#FAF7F9] hover:bg-[#FDF2F6] border border-[#ECE4E8] text-zinc-700 flex items-center justify-center transition" title="Inspect Shipment" aria-label="Inspect Shipment">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </a>
                        <a id="ws-info-card-link" href="{{ $firstShipment['show_url'] }}#milestone-timeline" class="w-7 h-7 rounded-lg bg-[#FAF7F9] hover:bg-[#FDF2F6] border border-[#ECE4E8] text-zinc-700 flex items-center justify-center transition" title="View Timeline" aria-label="View Timeline">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- 3-Column Specs (Carrier ID | Cargo | Departure) -->
                <div class="grid grid-cols-3 gap-2 pt-1">
                    <div>
                        <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.carrier_id') }}</div>
                        <div id="ws-truck-id" class="text-xs font-bold text-zinc-900 mt-0.5 truncate">{{ $firstShipment['carrier_code'] }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.cargo_load') }}</div>
                        <div id="ws-dock-label" class="text-xs font-bold text-zinc-900 mt-0.5 truncate">{{ $firstShipment['weight_formatted'] }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.departure') }}</div>
                        <div id="ws-started-time" class="text-xs font-bold text-zinc-900 mt-0.5 tabular-nums truncate">{{ $firstShipment['started_time'] }}</div>
                    </div>
                </div>

                <!-- Departure -> Arrival Hatched Progress Bar -->
                <div>
                    <div class="flex items-center justify-between text-[11px] text-zinc-400 mb-1">
                        <span>{{ __('dashboard.workspace.departure') }}</span>
                        <span>{{ __('dashboard.workspace.arrival') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-2">
                        <span id="ws-dep-city" class="text-xs font-bold text-zinc-900 truncate max-w-[85px]">{{ $firstShipment['origin_city'] }}</span>
                        <div class="flex-1 h-2.5 rounded-full route-track-hatch relative flex items-center justify-center mx-1">
                            <span class="w-5 h-5 rounded-full bg-[#1F171B] text-white flex items-center justify-center shadow-xs">
                                <svg class="w-2.5 h-2.5 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                </svg>
                            </span>
                        </div>
                        <span id="ws-arr-city" class="text-xs font-bold text-zinc-900 truncate max-w-[85px] text-right">{{ $firstShipment['dest_city'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons (RBAC-Aware) -->
            <div class="grid grid-cols-2 gap-2">
                <a id="ws-view-btn" href="{{ $firstShipment['show_url'] }}" class="py-2.5 px-3 rounded-full border border-[#ECE4E8] hover:border-[#F8C9DC] hover:bg-[#FDF2F6] text-xs font-semibold text-zinc-800 text-center transition">
                    {{ __('dashboard.workspace.view_details') }}
                </a>
                <a
                    id="ws-edit-btn"
                    href="{{ $firstShipment['can_edit'] ? $firstShipment['edit_url'] : ($firstShipment['show_url'] . '#milestone-timeline') }}"
                    class="py-2.5 px-3 rounded-full border border-[#ECE4E8] hover:border-[#F8C9DC] hover:bg-[#FDF2F6] text-xs font-semibold text-zinc-800 text-center transition"
                >
                    {{ $firstShipment['can_edit'] ? __('dashboard.workspace.edit_shipment') : __('dashboard.workspace.timeline') }}
                </a>
            </div>
        </div>

        <!-- CARD 2: Tracking Details -->
        <div class="bg-white rounded-[22px] p-5 shadow-sm flex flex-col justify-between space-y-3">
            <div class="space-y-3.5">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-[#FAF7F9] text-[#1F171B] flex items-center justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-zinc-900">{{ __('dashboard.workspace.tracking_details') }}</h3>
                </div>

                <!-- Tracking ID & Status Pill -->
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.tracking_id') }}</div>
                        <a id="ws-tracking-number" href="{{ $firstShipment['show_url'] }}" class="text-[13px] font-bold text-zinc-900 hover:text-[#C93565] truncate block mt-0.5 transition" title="#{{ $firstShipment['number'] }}">
                            #{{ $firstShipment['number'] }}
                        </a>
                    </div>
                    <span id="ws-tracking-status" class="px-2.5 py-0.5 rounded-full bg-[#FDF2F6] text-[#9E224B] border border-[#F8C9DC] text-[10px] font-semibold whitespace-nowrap shrink-0">
                        {{ strtolower($firstShipment['status_label']) }}
                    </span>
                </div>

                <!-- 3-Step Vertical Connected Timeline -->
                <div class="relative pl-0.5 space-y-3.5 pt-1">
                    <!-- Vertical Dashed Connector Line -->
                    <div class="absolute left-[13px] top-3 bottom-3 w-px border-l border-dashed border-[#E6DCE1]"></div>

                    <!-- Step 1: Current Location -->
                    <div class="relative flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-[#E04879] text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs shadow-[#E04879]/30 z-10">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.current_location') }}</div>
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
                            <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.departure') }}</div>
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
                            <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.arrival') }} &bull; ETA {{ $firstShipment['eta_formatted'] }}</div>
                            <div id="ws-track-arrival" class="text-xs font-bold text-zinc-900 truncate mt-0.5">
                                {{ $firstShipment['dest_city'] }}, {{ $firstShipment['dest_country'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 3: Route Transportation (Interactive OpenStreetMap with Smooth Fractional Zoom) -->
        <div class="bg-white rounded-[22px] p-5 shadow-sm flex flex-col justify-between space-y-3">
            <div class="flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-full bg-[#F4F5F7] text-zinc-800 flex items-center justify-center shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-bold text-zinc-900 whitespace-nowrap">{{ __('dashboard.workspace.route_transport') }}</h3>
                        <p id="ws-map-subtitle" class="text-[11px] text-zinc-400 truncate">
                            {{ $firstShipment['origin_city'] }} &rarr; {{ $firstShipment['dest_city'] }}
                        </p>
                    </div>
                </div>
                <a id="ws-map-expand-link" href="{{ $firstShipment['show_url'] }}" class="w-7 h-7 rounded-full hover:bg-zinc-100 text-zinc-700 hover:text-zinc-900 flex items-center justify-center transition shrink-0" title="Open Full Corridor Map" aria-label="Open Full Corridor Map">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H8M17 7v9" />
                    </svg>
                </a>
            </div>

            <!-- Interactive OpenStreetMap Container with Top Hub Pill, Zoom Micro-Controls & Bottom Route Bar -->
            <div class="relative rounded-2xl overflow-hidden border border-zinc-200/60 h-[214px] bg-[#E9ECEF]">
                <div id="dashboard-osm-map" class="w-full h-full z-0"></div>

                <!-- Floating White Hub Address Pill at Top Left/Center -->
                <div class="absolute top-2.5 left-2.5 right-20 z-[400] pointer-events-none flex justify-start">
                    <div id="ws-map-floating-hub" class="px-2.5 py-1 rounded-full bg-white/95 backdrop-blur-xs text-zinc-800 text-[10px] font-semibold shadow-md border border-black/[0.05] whitespace-nowrap truncate">
                        {{ $firstShipment['current_hub'] }}, {{ $firstShipment['current_city'] }}
                    </div>
                </div>

                <!-- Compact Floating Zoom & Reset Micro-Controls (Top Right Inside Map) -->
                <div class="absolute top-2 right-2 z-[400] flex items-center gap-0.5 bg-white/95 backdrop-blur-xs rounded-full p-0.5 shadow-md border border-black/[0.05]">
                    <button
                        type="button"
                        onclick="window.zoomCargoWorkspaceMap(1)"
                        class="w-5 h-5 rounded-full hover:bg-[#C93565] hover:text-white text-zinc-700 flex items-center justify-center text-[11px] font-bold transition cursor-pointer"
                        title="Zoom In"
                        aria-label="Zoom In"
                    >+</button>
                    <button
                        type="button"
                        onclick="window.zoomCargoWorkspaceMap(-1)"
                        class="w-5 h-5 rounded-full hover:bg-[#C93565] hover:text-white text-zinc-700 flex items-center justify-center text-[11px] font-bold transition cursor-pointer"
                        title="Zoom Out"
                        aria-label="Zoom Out"
                    >&minus;</button>
                    <button
                        type="button"
                        onclick="window.resetCargoWorkspaceMapBounds()"
                        class="w-5 h-5 rounded-full hover:bg-[#FDF2F6] text-zinc-600 hover:text-[#1F171B] flex items-center justify-center transition cursor-pointer"
                        title="Fit Corridor Bounds"
                        aria-label="Fit Corridor Bounds"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>

                <!-- Floating Departure -> Arrival Overlay Card at Bottom (Clear Margin Above OSM Attribution) -->
                <div class="absolute bottom-5 inset-x-2.5 z-[400] bg-white/95 backdrop-blur-md rounded-xl px-3 py-1.5 shadow-md border border-black/[0.04] pointer-events-none">
                    <div class="flex items-center justify-between text-[10px] text-zinc-400">
                        <span>{{ __('dashboard.workspace.departure') }}</span>
                        <span>{{ __('dashboard.workspace.arrival') }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-2 mt-0.5">
                        <span id="ws-map-dep-city" class="text-xs font-bold text-zinc-900 truncate max-w-[75px]">{{ $firstShipment['origin_city'] }}</span>
                        <div class="flex-1 flex items-center justify-center relative">
                            <div class="w-full border-t-2 border-dashed border-[#E04879]/35"></div>
                            <span class="absolute w-5 h-5 rounded-full bg-[#E04879] text-white flex items-center justify-center shadow-xs">
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

        <!-- CARD 4: Operational Telemetry (100% Real DB Metrics + Chart.js Vertical Pill Bars) -->
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
                            <h3 class="text-sm font-bold text-zinc-900">{{ __('dashboard.workspace.operational_health') }}</h3>
                            <p class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.sla_threshold', ['hours' => $thresholdHours . 'h']) }}</p>
                        </div>
                    </div>
                    <a href="#operational-analytics" class="w-7 h-7 rounded-full hover:bg-zinc-100 text-zinc-700 hover:text-zinc-900 flex items-center justify-center transition" title="View Operational Analytics" aria-label="View Operational Analytics">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H8M17 7v9" />
                        </svg>
                    </a>
                </div>

                <!-- 3 Real DB Metric Columns -->
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.shipments') }}</div>
                        <div class="text-sm font-bold text-zinc-900 mt-0.5 tabular-nums">{{ $metrics['total_shipments'] ?? 0 }} units</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.on_time_sla') }}</div>
                        <div class="text-sm font-bold text-zinc-900 mt-0.5 tabular-nums">{{ $metrics['kpis']['on_time_delivery_rate'] ?? 100 }}%</div>
                    </div>
                    <div>
                        <div class="text-[11px] text-zinc-400">{{ __('dashboard.workspace.active_alert') }}</div>
                        <div class="text-sm font-bold text-zinc-900 mt-0.5 tabular-nums">{{ $metrics['open_exceptions'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Real Chart.js Canvas Rendering Vertical Rounded Pill Bars -->
            <div class="relative h-[126px] pt-1">
                <canvas id="wsTelemetryChart"></canvas>
            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const shipments = @json($workspaceShipments);
    const defaultShipment = @json($firstShipment);
    const telemetryBars = @json($telemetryBars);
    const wsI18n = {
        editShipment: @json(__('dashboard.workspace.edit_shipment')),
        timeline: @json(__('dashboard.workspace.timeline')),
    };
    const esc = typeof window.escapeHtml === 'function'
        ? window.escapeHtml
        : (v) => String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

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
            attributionControl: true,
            scrollWheelZoom: true,
            zoomSnap: 0.1,
            zoomDelta: 0.5,
            wheelPxPerZoomLevel: 85,
            wheelDebounceTime: 20,
            zoomAnimation: true,
            fadeAnimation: true,
            markerZoomAnimation: true,
            inertia: true,
            inertiaDeceleration: 2800,
        });

        dashboardMap.attributionControl.setPrefix(false);

        window.L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            keepBuffer: 6,
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

        const curveFn = typeof window.buildCurvedSegment === 'function'
            ? window.buildCurvedSegment
            : (a, b) => [a, b];

        const activeArc = curveFn(originPt, currentPt, 0.16, 24);
        const remainingArc = curveFn(currentPt, destPt, -0.16, 24);

        window.L.polyline(activeArc.length > 1 ? activeArc : [originPt, currentPt], {
            color: '#E04879',
            weight: 2.5,
            opacity: 0.95,
            lineCap: 'round',
        }).addTo(routeLayerGroup);

        window.L.polyline(remainingArc.length > 1 ? remainingArc : [currentPt, destPt], {
            color: '#1F171B',
            weight: 2,
            opacity: 0.85,
            dashArray: '4, 5',
            lineCap: 'round',
        }).addTo(routeLayerGroup);

        window.L.marker(originPt, { icon: createNexPin('#1F171B') })
            .bindPopup(`<div class="text-xs space-y-0.5"><span class="text-[10px] font-bold uppercase text-[#EC6E9B] block">Departure</span><strong>${esc(sh.origin_city)}</strong><div class="text-zinc-300">${esc(sh.origin_name)}</div><div class="text-[10px] font-mono text-zinc-400">${esc(sh.started_time)}</div></div>`)
            .addTo(routeLayerGroup);

        window.L.marker(currentPt, { icon: createNexPin('#E04879') })
            .bindPopup(`<div class="text-xs space-y-0.5"><span class="text-[10px] font-bold uppercase text-[#EC6E9B] block">${esc(sh.status_label)}</span><strong>${esc(sh.current_city)}</strong><div class="text-zinc-300">${esc(sh.current_hub)}</div><div class="text-[10px] font-mono text-zinc-400">${esc(sh.current_event_time)}</div></div>`)
            .addTo(routeLayerGroup);

        window.L.marker(destPt, { icon: createNexPin('#1F171B') })
            .bindPopup(`<div class="text-xs space-y-0.5"><span class="text-[10px] font-bold uppercase text-emerald-400 block">Arrival</span><strong>${esc(sh.dest_city)}</strong><div class="text-zinc-300">${esc(sh.dest_name)}</div><div class="text-[10px] font-mono text-zinc-400">ETA: ${esc(sh.eta_formatted)}</div></div>`)
            .addTo(routeLayerGroup);

        activeBounds = routeLayerGroup.getBounds().pad(0.24);

        if (animateFly) {
            dashboardMap.flyToBounds(activeBounds, {
                paddingTopLeft: [24, 34],
                paddingBottomRight: [24, 68],
                maxZoom: 6,
                duration: 0.85,
                easeLinearity: 0.25,
            });
        } else {
            dashboardMap.fitBounds(activeBounds, {
                paddingTopLeft: [24, 34],
                paddingBottomRight: [24, 68],
                maxZoom: 6,
            });
        }
    }

    window.zoomCargoWorkspaceMap = function (delta) {
        if (dashboardMap) {
            dashboardMap.setZoom(dashboardMap.getZoom() + delta, { animate: true });
        }
    };

    window.resetCargoWorkspaceMapBounds = function () {
        if (dashboardMap && activeBounds) {
            dashboardMap.flyToBounds(activeBounds, {
                paddingTopLeft: [24, 34],
                paddingBottomRight: [24, 68],
                maxZoom: 6,
                duration: 0.75,
            });
        }
    };

    window.filterCargoWorkspace = function (mode) {
        document.querySelectorAll('[data-ws-filter]').forEach(btn => {
            const active = btn.getAttribute('data-ws-filter') === mode;
            btn.classList.toggle('bg-[#C93565]', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('shadow-sm', active);
            btn.classList.toggle('bg-white', !active);
            btn.classList.toggle('text-zinc-700', !active);
        });

        let firstMatchedIdx = null;
        document.querySelectorAll('[data-ws-row]').forEach(row => {
            const status = row.getAttribute('data-ws-status');
            const delayed = row.getAttribute('data-ws-delayed') === '1';
            const idx = Number(row.getAttribute('data-ws-row'));

            let show = true;
            if (mode === 'transit') show = status === 'IN_TRANSIT';
            if (mode === 'delayed') show = delayed;

            row.classList.toggle('opacity-35', !show);
            if (show && firstMatchedIdx === null) {
                firstMatchedIdx = idx;
            }
        });

        if (firstMatchedIdx !== null) {
            window.selectCargoWorkspaceShipment(firstMatchedIdx);
        }
    };

    window.selectCargoWorkspaceShipment = function (index) {
        const sh = shipments[index] || defaultShipment;
        if (!sh) return;

        document.querySelectorAll('[data-ws-row]').forEach(el => {
            const isMatch = Number(el.getAttribute('data-ws-row')) === Number(index);
            el.classList.toggle('bg-[#FDF2F6]', isMatch);
            el.classList.toggle('text-[#1F171B]', isMatch);
            el.classList.toggle('font-semibold', isMatch);
        });

        document.querySelectorAll('[data-ws-bay]').forEach(el => {
            const isMatch = Number(el.getAttribute('data-ws-bay')) === Number(index);
            el.classList.toggle('border-[#E04879]', isMatch);
            el.classList.toggle('border-transparent', !isMatch);
        });

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
        setText('ws-carrier-sub', `${sh.carrier_code} \u2022 ${sh.number}`);
        setHref('ws-directory-link', sh.show_url);

        setText('ws-operator-initials', String(sh.operator_name || 'OP').slice(0, 2).toUpperCase());
        setText('ws-operator-name', sh.operator_name);
        setText('ws-truck-id', sh.carrier_code);
        setText('ws-dock-label', sh.weight_formatted);
        setText('ws-started-time', sh.started_time);
        setText('ws-dep-city', sh.origin_city);
        setText('ws-arr-city', sh.dest_city);
        setHref('ws-info-search-link', sh.show_url);
        setHref('ws-info-card-link', `${sh.show_url}#milestone-timeline`);
        setHref('ws-view-btn', sh.show_url);

        const editBtn = document.getElementById('ws-edit-btn');
        if (editBtn) {
            editBtn.setAttribute('href', sh.can_edit ? sh.edit_url : `${sh.show_url}#milestone-timeline`);
            editBtn.textContent = sh.can_edit ? wsI18n.editShipment : wsI18n.timeline;
        }

        setText('ws-tracking-number', `#${sh.number}`);
        setHref('ws-tracking-number', sh.show_url);
        setText('ws-tracking-status', String(sh.status_label).toLowerCase());
        setText('ws-track-current', `${sh.current_city}, ${sh.current_country}`);
        setText('ws-track-departure', `${sh.origin_city}, ${sh.origin_country}`);
        setText('ws-track-arrival', `${sh.dest_city}, ${sh.dest_country}`);

        setText('ws-map-subtitle', `${sh.origin_city} \u2192 ${sh.dest_city}`);
        setText('ws-map-floating-hub', `${sh.current_hub}, ${sh.current_city}`);
        setText('ws-map-dep-city', sh.origin_city);
        setText('ws-map-arr-city', sh.dest_city);
        setHref('ws-map-expand-link', sh.show_url);

        renderShipmentOnMap(sh, true);
    };

    function initWorkspaceTelemetryChart() {
        const canvas = document.getElementById('wsTelemetryChart');
        if (!canvas || typeof window.Chart === 'undefined') {
            return;
        }

        const barColors = [
            '#E04879', '#E04879', '#EFE6EB', '#E04879', '#EFE6EB',
            '#E04879', '#E04879', '#EFE6EB', '#E04879', '#EFE6EB', '#E04879'
        ];

        new window.Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: telemetryBars.map(item => item.label),
                datasets: [{
                    data: telemetryBars.map(item => item.value),
                    backgroundColor: barColors,
                    borderRadius: 999,
                    borderSkipped: false,
                    barPercentage: 0.62,
                    categoryPercentage: 0.85,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1F171B',
                        titleColor: '#ffffff',
                        bodyColor: '#f4f0f2',
                        padding: 8,
                        displayColors: false,
                        callbacks: {
                            label: (ctx) => telemetryBars[ctx.dataIndex]?.raw || `${ctx.parsed.y}%`
                        }
                    }
                },
                scales: {
                    x: { display: false, grid: { display: false } },
                    y: { display: false, min: 0, max: 105, grid: { display: false } }
                }
            }
        });
    }

    initDashboardOsmMap();
    initWorkspaceTelemetryChart();
});
</script>
@endpush
