<?php

namespace Database\Seeders;

use App\Enums\DelayStatus;
use App\Enums\ExceptionSeverity;
use App\Enums\ExceptionStatus;
use App\Enums\ExceptionType;
use App\Enums\ShipmentStatus;
use App\Enums\TrackingEventType;
use App\Models\AuditLog;
use App\Models\Carrier;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Shipment;
use App\Models\ShipmentException;
use App\Models\ShipmentItem;
use App\Models\TrackingEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Seed 25 realistic shipments with items, tracking events, exceptions,
     * notifications, and audit logs along the Southeast Asia -> Europe route corridor.
     */
    public function run(): void
    {
        $operator = User::where('email', 'operator@cargotrace.test')->first() ?? User::factory()->operator()->create();
        $customer1 = User::where('email', 'customer1@cargotrace.test')->first() ?? User::factory()->customer()->create();
        $customer2 = User::where('email', 'customer2@cargotrace.test')->first() ?? User::factory()->customer()->create();

        $carriers = Carrier::all()->keyBy('code');
        $locations = Location::all()->keyBy('name');

        // Shorthand location getters
        $priok = $locations['Port of Tanjung Priok (Jakarta)'] ?? Location::first();
        $singapore = $locations['Port of Singapore'] ?? Location::first();
        $klang = $locations['Port Klang Terminal'] ?? Location::first();
        $colombo = $locations['Port of Colombo'] ?? Location::first();
        $salalah = $locations['Port of Salalah'] ?? Location::first();
        $portSaid = $locations['Port Said (Suez Canal Hub)'] ?? Location::first();
        $piraeus = $locations['Port of Piraeus'] ?? Location::first();
        $valencia = $locations['Port of Valencia'] ?? Location::first();
        $rotterdam = $locations['Port of Rotterdam (Maasvlakte)'] ?? Location::first();
        $antwerp = $locations['Port of Antwerp-Bruges'] ?? Location::first();
        $hamburg = $locations['Port of Hamburg'] ?? Location::first();
        $felixstowe = $locations['Port of Felixstowe'] ?? Location::first();

        // 25 Structured Shipments Definition
        $shipmentsData = [
            // 1. IN_TRANSIT with CUSTOMS_HOLD (Main Exception Scenario - Delayed > 24h threshold)
            [
                'number' => 'CT-SEA-EUR-2026-001',
                'customer' => $customer1,
                'carrier' => $carriers['MSK'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $rotterdam,
                'cargo_type' => 'Specialty Coffee & Raw Spices (FCL)',
                'weight' => 24500.00,
                'volume' => 58.50,
                'dep_days' => -14,
                'arr_days' => 6,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::Delayed,
                'delay_hours' => 36, // Exceeds 24h group threshold!
                'exception' => [
                    'type' => ExceptionType::CustomsHold,
                    'severity' => ExceptionSeverity::High,
                    'status' => ExceptionStatus::Open,
                    'description' => 'Container placed under CUSTOMS_HOLD by Rotterdam Port Customs for phytosanitary certificate verification and physical sampling.',
                    'detected_hours_ago' => 28,
                ],
                'items' => [
                    ['description' => 'Sumatra Mandheling Green Coffee Beans', 'qty' => 320, 'weight' => 19200.00],
                    ['description' => 'Maluku Organic Whole Cloves & Nutmeg', 'qty' => 110, 'weight' => 5300.00],
                ],
            ],

            // 2. IN_TRANSIT with PORT_CONGESTION (Delayed 26h at Suez/Port Said)
            [
                'number' => 'CT-SEA-EUR-2026-002',
                'customer' => $customer2,
                'carrier' => $carriers['MSC'] ?? Carrier::first(),
                'origin' => $singapore,
                'destination' => $antwerp,
                'cargo_type' => 'High-Tech Semiconductors & Wafers (Reefer)',
                'weight' => 14200.00,
                'volume' => 45.00,
                'dep_days' => -10,
                'arr_days' => 8,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::Delayed,
                'delay_hours' => 26,
                'exception' => [
                    'type' => ExceptionType::PortCongestion,
                    'severity' => ExceptionSeverity::Medium,
                    'status' => ExceptionStatus::Investigating,
                    'description' => 'Berth congestion and convoy delay at Port Said northern canal approach.',
                    'detected_hours_ago' => 18,
                ],
                'items' => [
                    ['description' => 'Automotive MCU Microcontroller Trays', 'qty' => 450, 'weight' => 9800.00],
                    ['description' => 'Power Management Integrated Circuits', 'qty' => 220, 'weight' => 4400.00],
                ],
            ],

            // 3. IN_TRANSIT with WEATHER_DELAY (Critical Delay 48h - Bay of Bengal monsoon storm)
            [
                'number' => 'CT-SEA-EUR-2026-003',
                'customer' => $customer1,
                'carrier' => $carriers['CMA'] ?? Carrier::first(),
                'origin' => $klang,
                'destination' => $hamburg,
                'cargo_type' => 'Industrial Solar PV Inverter Modules',
                'weight' => 26800.00,
                'volume' => 64.00,
                'dep_days' => -18,
                'arr_days' => 4,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::CriticalDelay,
                'delay_hours' => 48,
                'exception' => [
                    'type' => ExceptionType::WeatherDelay,
                    'severity' => ExceptionSeverity::Critical,
                    'status' => ExceptionStatus::Open,
                    'description' => 'Vessel reduced cruising speed to 9 knots due to severe cyclone front across Northern Indian Ocean.',
                    'detected_hours_ago' => 38,
                ],
                'items' => [
                    ['description' => 'Utility-Scale Solar Inverters (150kW)', 'qty' => 60, 'weight' => 18000.00],
                    ['description' => 'High-Voltage DC Combiner Boxes', 'qty' => 140, 'weight' => 8800.00],
                ],
            ],

            // 4. DELIVERED on time (Perfect On-Time Delivery KPI Benchmark)
            [
                'number' => 'CT-SEA-EUR-2026-004',
                'customer' => $customer1,
                'carrier' => $carriers['HAG'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $rotterdam,
                'cargo_type' => 'Eco Footwear & Sustainable Apparel',
                'weight' => 12400.00,
                'volume' => 52.00,
                'dep_days' => -24,
                'arr_days' => -2,
                'status' => ShipmentStatus::Delivered,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Recycled Ocean Plastic Sneakers', 'qty' => 1200, 'weight' => 7400.00],
                    ['description' => 'Organic Cotton Polo Shirts', 'qty' => 2500, 'weight' => 5000.00],
                ],
            ],

            // 5. DELIVERED on time (Rotterdam Gateway)
            [
                'number' => 'CT-SEA-EUR-2026-005',
                'customer' => $customer2,
                'carrier' => $carriers['ONE'] ?? Carrier::first(),
                'origin' => $singapore,
                'destination' => $hamburg,
                'cargo_type' => 'Lithium-Ion Energy Storage Racks',
                'weight' => 21300.00,
                'volume' => 48.00,
                'dep_days' => -26,
                'arr_days' => -1,
                'status' => ShipmentStatus::Delivered,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Commercial ESS Battery Enclosures', 'qty' => 35, 'weight' => 21300.00],
                ],
            ],

            // 6. OUT_FOR_DELIVERY (Final mile distribution in Europe)
            [
                'number' => 'CT-SEA-EUR-2026-006',
                'customer' => $customer2,
                'carrier' => $carriers['EMC'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $antwerp,
                'cargo_type' => 'Precision CNC Milling Equipment',
                'weight' => 18900.00,
                'volume' => 42.00,
                'dep_days' => -21,
                'arr_days' => 1,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Industrial 5-Axis CNC Milling Spindles', 'qty' => 12, 'weight' => 18900.00],
                ],
            ],

            // 7. IN_TRANSIT with Resolved CUSTOMS_HOLD (Historic resolution)
            [
                'number' => 'CT-SEA-EUR-2026-007',
                'customer' => $customer1,
                'carrier' => $carriers['MSK'] ?? Carrier::first(),
                'origin' => $singapore,
                'destination' => $felixstowe,
                'cargo_type' => 'Medical Diagnostic Equipment & Consumables',
                'weight' => 11200.00,
                'volume' => 36.00,
                'dep_days' => -12,
                'arr_days' => 7,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 6, // Resolved quickly within 6h, under threshold
                'exception' => [
                    'type' => ExceptionType::CustomsHold,
                    'severity' => ExceptionSeverity::Medium,
                    'status' => ExceptionStatus::Resolved,
                    'description' => 'Import declaration document discrepancy cleared after amended commercial invoice submission.',
                    'detected_hours_ago' => 40,
                    'resolved_hours_ago' => 12,
                ],
                'items' => [
                    ['description' => 'Digital Ultrasonography Probes', 'qty' => 120, 'weight' => 4200.00],
                    ['description' => 'Sterile Reagent Cassettes', 'qty' => 800, 'weight' => 7000.00],
                ],
            ],

            // 8. IN_TRANSIT on-time (Mid-voyage Mediterranean Sea)
            [
                'number' => 'CT-SEA-EUR-2026-008',
                'customer' => $customer2,
                'carrier' => $carriers['MSC'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $valencia,
                'cargo_type' => 'Natural Rubber & Latex Sheet Bales',
                'weight' => 25800.00,
                'volume' => 60.00,
                'dep_days' => -15,
                'arr_days' => 5,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Standard Indonesian Rubber (SIR 20)', 'qty' => 700, 'weight' => 25800.00],
                ],
            ],

            // 9. DISPATCHED (Vessel departed origin port, transiting Malacca Strait)
            [
                'number' => 'CT-SEA-EUR-2026-009',
                'customer' => $customer1,
                'carrier' => $carriers['CMA'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $rotterdam,
                'cargo_type' => 'Frozen Vannamei Shrimp (Reefer -20C)',
                'weight' => 22000.00,
                'volume' => 55.00,
                'dep_days' => -2,
                'arr_days' => 18,
                'status' => ShipmentStatus::PickedUp,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'IQF Peeled Tail-On Shrimp', 'qty' => 1100, 'weight' => 22000.00],
                ],
            ],

            // 10. DISPATCHED (Departing Singapore)
            [
                'number' => 'CT-SEA-EUR-2026-010',
                'customer' => $customer2,
                'carrier' => $carriers['HAG'] ?? Carrier::first(),
                'origin' => $singapore,
                'destination' => $hamburg,
                'cargo_type' => 'Aircraft Maintenance Spares & Avionics',
                'weight' => 8400.00,
                'volume' => 30.00,
                'dep_days' => -1,
                'arr_days' => 19,
                'status' => ShipmentStatus::PickedUp,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Titanium Turbine Fasteners', 'qty' => 500, 'weight' => 3400.00],
                    ['description' => 'Navigation Instrument Modules', 'qty' => 45, 'weight' => 5000.00],
                ],
            ],

            // 11. BOOKED (Vessel allocated, waiting container stuffing)
            [
                'number' => 'CT-SEA-EUR-2026-011',
                'customer' => $customer1,
                'carrier' => $carriers['ONE'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $rotterdam,
                'cargo_type' => 'Automotive Wiring Harnesses',
                'weight' => 15600.00,
                'volume' => 44.00,
                'dep_days' => 2,
                'arr_days' => 22,
                'status' => ShipmentStatus::Booked,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Copper Wiring Harness Assemblies', 'qty' => 850, 'weight' => 15600.00],
                ],
            ],

            // 12. BOOKED (Port Klang -> Antwerp)
            [
                'number' => 'CT-SEA-EUR-2026-012',
                'customer' => $customer2,
                'carrier' => $carriers['EMC'] ?? Carrier::first(),
                'origin' => $klang,
                'destination' => $antwerp,
                'cargo_type' => 'Oleochemical Fatty Acids',
                'weight' => 23400.00,
                'volume' => 54.00,
                'dep_days' => 3,
                'arr_days' => 24,
                'status' => ShipmentStatus::Booked,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Refined Stearic Acid Flakes (Bags)', 'qty' => 936, 'weight' => 23400.00],
                ],
            ],

            // 13. DRAFT (Under preparation by customer/operator)
            [
                'number' => 'CT-SEA-EUR-2026-013',
                'customer' => $customer1,
                'carrier' => $carriers['MSK'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $rotterdam,
                'cargo_type' => 'Rattan Handicrafts & Teak Outdoor Furniture',
                'weight' => 9800.00,
                'volume' => 62.00,
                'dep_days' => 5,
                'arr_days' => 26,
                'status' => ShipmentStatus::Created,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => null,
                'items' => [
                    ['description' => 'Handwoven Synthetic Rattan Loungers', 'qty' => 140, 'weight' => 4800.00],
                    ['description' => 'Sustainably Farmed Teak Dining Tables', 'qty' => 70, 'weight' => 5000.00],
                ],
            ],

            // 14. DRAFT (Air cargo fast-track draft)
            [
                'number' => 'CT-SEA-EUR-2026-014',
                'customer' => $customer2,
                'carrier' => $carriers['MSC'] ?? Carrier::first(),
                'origin' => $singapore,
                'destination' => $hamburg,
                'cargo_type' => 'Precision Medical Lasers',
                'weight' => 3200.00,
                'volume' => 14.00,
                'dep_days' => 6,
                'arr_days' => 27,
                'status' => ShipmentStatus::Created,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => null,
                'items' => [
                    ['description' => 'Ophthalmic Laser Surgical Devices', 'qty' => 8, 'weight' => 3200.00],
                ],
            ],

            // 15. CANCELLED (Customer cancelled booking before gate-in)
            [
                'number' => 'CT-SEA-EUR-2026-015',
                'customer' => $customer1,
                'carrier' => $carriers['CMA'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $rotterdam,
                'cargo_type' => 'Agricultural Fertilizer Components',
                'weight' => 25000.00,
                'volume' => 50.00,
                'dep_days' => -5,
                'arr_days' => 15,
                'status' => ShipmentStatus::Cancelled,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => null,
                'items' => [
                    ['description' => 'Ammonium Nitrate Bags', 'qty' => 500, 'weight' => 25000.00],
                ],
            ],

            // 16. IN_TRANSIT with DAMAGED packaging exception
            [
                'number' => 'CT-SEA-EUR-2026-016',
                'customer' => $customer2,
                'carrier' => $carriers['HAG'] ?? Carrier::first(),
                'origin' => $klang,
                'destination' => $rotterdam,
                'cargo_type' => 'Sheet Glass & Precision Optics',
                'weight' => 17800.00,
                'volume' => 38.00,
                'dep_days' => -8,
                'arr_days' => 12,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::Delayed,
                'delay_hours' => 14,
                'exception' => [
                    'type' => ExceptionType::Damaged,
                    'severity' => ExceptionSeverity::Medium,
                    'status' => ExceptionStatus::Investigating,
                    'description' => 'Minor exterior container dent reported during Colombo crane transshipment hoist.',
                    'detected_hours_ago' => 14,
                ],
                'items' => [
                    ['description' => 'Architectural Coated Glass Crates', 'qty' => 45, 'weight' => 17800.00],
                ],
            ],

            // 17. IN_TRANSIT with DOCUMENTATION_ISSUE (Resolved)
            [
                'number' => 'CT-SEA-EUR-2026-017',
                'customer' => $customer1,
                'carrier' => $carriers['ONE'] ?? Carrier::first(),
                'origin' => $singapore,
                'destination' => $piraeus,
                'cargo_type' => 'Telecom 5G Base Station Racks',
                'weight' => 13500.00,
                'volume' => 40.00,
                'dep_days' => -9,
                'arr_days' => 7,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 4,
                'exception' => [
                    'type' => ExceptionType::DocumentationIssue,
                    'severity' => ExceptionSeverity::Low,
                    'status' => ExceptionStatus::Resolved,
                    'description' => 'Missing Certificate of Origin re-uploaded and approved by carrier portal.',
                    'detected_hours_ago' => 30,
                    'resolved_hours_ago' => 10,
                ],
                'items' => [
                    ['description' => 'MIMO Antenna Array Units', 'qty' => 200, 'weight' => 8500.00],
                    ['description' => 'Baseband Power Modules', 'qty' => 150, 'weight' => 5000.00],
                ],
            ],

            // 18. DELIVERED (Hamburg)
            [
                'number' => 'CT-SEA-EUR-2026-018',
                'customer' => $customer2,
                'carrier' => $carriers['EMC'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $hamburg,
                'cargo_type' => 'Organic Certified Cocoa Butter',
                'weight' => 20500.00,
                'volume' => 46.00,
                'dep_days' => -25,
                'arr_days' => -3,
                'status' => ShipmentStatus::Delivered,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Refined Cocoa Butter Blocks', 'qty' => 820, 'weight' => 20500.00],
                ],
            ],

            // 19. OUT_FOR_DELIVERY (UK Gateway Felixstowe)
            [
                'number' => 'CT-SEA-EUR-2026-019',
                'customer' => $customer1,
                'carrier' => $carriers['MSK'] ?? Carrier::first(),
                'origin' => $singapore,
                'destination' => $felixstowe,
                'cargo_type' => 'Consumer Electronics (Tablets & Wearables)',
                'weight' => 7600.00,
                'volume' => 28.00,
                'dep_days' => -19,
                'arr_days' => 1,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Touchscreen Smart Tablets (Palletized)', 'qty' => 1800, 'weight' => 5200.00],
                    ['description' => 'Smart Fitness Wristbands', 'qty' => 3000, 'weight' => 2400.00],
                ],
            ],

            // 20. IN_TRANSIT with CUSTOMS_HOLD (Second Main Group Exception - Delayed 30h)
            [
                'number' => 'CT-SEA-EUR-2026-020',
                'customer' => $customer2,
                'carrier' => $carriers['MSC'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $rotterdam,
                'cargo_type' => 'Processed Marine Collagen Powder',
                'weight' => 16400.00,
                'volume' => 35.00,
                'dep_days' => -16,
                'arr_days' => 4,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::Delayed,
                'delay_hours' => 30, // Exceeds 24h threshold
                'exception' => [
                    'type' => ExceptionType::CustomsHold,
                    'severity' => ExceptionSeverity::High,
                    'status' => ExceptionStatus::Open,
                    'description' => 'CUSTOMS_HOLD: EU Food & Feed Safety Authority (RASFF) lab sampling notification issued.',
                    'detected_hours_ago' => 22,
                ],
                'items' => [
                    ['description' => 'Hydrolyzed Marine Collagen Peptides', 'qty' => 650, 'weight' => 16400.00],
                ],
            ],

            // 21. IN_TRANSIT with MISROUTED transshipment exception
            [
                'number' => 'CT-SEA-EUR-2026-021',
                'customer' => $customer1,
                'carrier' => $carriers['CMA'] ?? Carrier::first(),
                'origin' => $klang,
                'destination' => $antwerp,
                'cargo_type' => 'Synthetic Latex Examination Gloves',
                'weight' => 18200.00,
                'volume' => 62.00,
                'dep_days' => -13,
                'arr_days' => 9,
                'status' => ShipmentStatus::InTransit,
                'delay_status' => DelayStatus::Delayed,
                'delay_hours' => 28,
                'exception' => [
                    'type' => ExceptionType::Misrouted,
                    'severity' => ExceptionSeverity::High,
                    'status' => ExceptionStatus::Investigating,
                    'description' => 'Container rolled over to secondary feeder vessel at Salalah transshipment terminal due to stack scheduling error.',
                    'detected_hours_ago' => 16,
                ],
                'items' => [
                    ['description' => 'Nitrile Disposable Gloves (Powder-Free)', 'qty' => 3600, 'weight' => 18200.00],
                ],
            ],

            // 22. BOOKED (Jakarta -> Valencia)
            [
                'number' => 'CT-SEA-EUR-2026-022',
                'customer' => $customer1,
                'carrier' => $carriers['HAG'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $valencia,
                'cargo_type' => 'Artisan Ceramic Porcelain Tableware',
                'weight' => 19500.00,
                'volume' => 42.00,
                'dep_days' => 4,
                'arr_days' => 25,
                'status' => ShipmentStatus::Booked,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Stoneware Glazed Plates & Bowls', 'qty' => 950, 'weight' => 19500.00],
                ],
            ],

            // 23. DISPATCHED (En route from Tanjung Priok to Singapore)
            [
                'number' => 'CT-SEA-EUR-2026-023',
                'customer' => $customer2,
                'carrier' => $carriers['ONE'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $rotterdam,
                'cargo_type' => 'Industrial Centrifugal Water Pumps',
                'weight' => 22400.00,
                'volume' => 49.00,
                'dep_days' => -3,
                'arr_days' => 17,
                'status' => ShipmentStatus::PickedUp,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'High-Pressure Heavy Duty Water Pumps', 'qty' => 50, 'weight' => 22400.00],
                ],
            ],

            // 24. DELIVERED (Valencia)
            [
                'number' => 'CT-SEA-EUR-2026-024',
                'customer' => $customer1,
                'carrier' => $carriers['EMC'] ?? Carrier::first(),
                'origin' => $singapore,
                'destination' => $valencia,
                'cargo_type' => 'Specialty Chemical Catalysts',
                'weight' => 14800.00,
                'volume' => 32.00,
                'dep_days' => -23,
                'arr_days' => -2,
                'status' => ShipmentStatus::Delivered,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => 0,
                'items' => [
                    ['description' => 'Zeolite Adsorbent Catalyst Pellets', 'qty' => 590, 'weight' => 14800.00],
                ],
            ],

            // 25. DRAFT (Scheduled for Q2 Dispatch)
            [
                'number' => 'CT-SEA-EUR-2026-025',
                'customer' => $customer2,
                'carrier' => $carriers['MSK'] ?? Carrier::first(),
                'origin' => $priok,
                'destination' => $rotterdam,
                'cargo_type' => 'Specialty Bio-Ethanol Fuel Barrels',
                'weight' => 21000.00,
                'volume' => 45.00,
                'dep_days' => 7,
                'arr_days' => 28,
                'status' => ShipmentStatus::Created,
                'delay_status' => DelayStatus::OnTime,
                'delay_hours' => null,
                'items' => [
                    ['description' => 'UN Certified Steel Drums (Bio-Ethanol)', 'qty' => 105, 'weight' => 21000.00],
                ],
            ],
        ];

        // Seed Each Shipment
        foreach ($shipmentsData as $data) {
            $expectedDeparture = Carbon::now()->addDays($data['dep_days']);
            $expectedArrival = Carbon::now()->addDays($data['arr_days']);

            $estimatedArrival = $expectedArrival;
            if (! empty($data['delay_hours'])) {
                $estimatedArrival = (clone $expectedArrival)->addHours($data['delay_hours']);
            }

            $shipment = Shipment::create([
                'shipment_number' => $data['number'],
                'customer_id' => $data['customer']->id,
                'carrier_id' => $data['carrier']->id,
                'origin_location_id' => $data['origin']->id,
                'destination_location_id' => $data['destination']->id,
                'cargo_type' => $data['cargo_type'],
                'weight_kg' => $data['weight'],
                'volume_cbm' => $data['volume'],
                'expected_departure_at' => $expectedDeparture,
                'expected_arrival_at' => $expectedArrival,
                'estimated_arrival_at' => $estimatedArrival,
                'current_status' => $data['status'],
                'delay_status' => $data['delay_status'] ?? DelayStatus::OnTime,
                'delay_hours' => $data['delay_hours'] ?? null,
                'created_by' => $operator->id,
            ]);

            // 1. Seed Items
            foreach (($data['items'] ?? []) as $item) {
                ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'description' => $item['description'],
                    'quantity' => $item['qty'],
                    'weight_kg' => $item['weight'],
                ]);
            }

            // 2. Seed Tracking Events for active/delivered shipments
            if (in_array($data['status'], [
                ShipmentStatus::PickedUp,
                ShipmentStatus::InTransit,
                ShipmentStatus::InTransit,
                ShipmentStatus::Delivered,
            ])) {
                // Event 1: Origin gate-in / pickup
                TrackingEvent::create([
                    'shipment_id' => $shipment->id,
                    'location_id' => $data['origin']->id,
                    'event_type' => TrackingEventType::PickedUp,
                    'is_exception' => false,
                    'occurred_at' => (clone $expectedDeparture)->subDays(1),
                    'latitude' => $data['origin']->latitude,
                    'longitude' => $data['origin']->longitude,
                    'description' => "Container gated into {$data['origin']->name}. Verified Gross Mass (VGM) verified.",
                    'created_by' => $operator->id,
                ]);

                // Event 2: Vessel departure
                TrackingEvent::create([
                    'shipment_id' => $shipment->id,
                    'location_id' => $data['origin']->id,
                    'event_type' => TrackingEventType::Departed,
                    'is_exception' => false,
                    'occurred_at' => $expectedDeparture,
                    'latitude' => $data['origin']->latitude,
                    'longitude' => $data['origin']->longitude,
                    'description' => "Vessel {$data['carrier']->name} departed {$data['origin']->name} bound for Europe.",
                    'created_by' => $operator->id,
                ]);

                // Event 3: Transshipment hub milestone (Colombo / Suez Canal)
                if (in_array($data['status'], [
                    ShipmentStatus::InTransit,
                    ShipmentStatus::InTransit,
                    ShipmentStatus::Delivered,
                ])) {
                    TrackingEvent::create([
                        'shipment_id' => $shipment->id,
                        'location_id' => $colombo->id,
                        'event_type' => TrackingEventType::TransshipmentArrival,
                        'is_exception' => false,
                        'occurred_at' => (clone $expectedDeparture)->addDays(5),
                        'latitude' => $colombo->latitude,
                        'longitude' => $colombo->longitude,
                        'description' => "Vessel berthed at {$colombo->name} for bunker refueling and transshipment sequence.",
                        'created_by' => $operator->id,
                    ]);
                }

                // Event 4: Delivered or Out for Delivery
                if ($data['status'] === ShipmentStatus::Delivered) {
                    TrackingEvent::create([
                        'shipment_id' => $shipment->id,
                        'location_id' => $data['destination']->id,
                        'event_type' => TrackingEventType::Delivered,
                        'is_exception' => false,
                        'occurred_at' => $expectedArrival,
                        'latitude' => $data['destination']->latitude,
                        'longitude' => $data['destination']->longitude,
                        'description' => "Cargo safely delivered to consignee warehouse in {$data['destination']->city}. Electronic Proof of Delivery (ePOD) signed.",
                        'created_by' => $operator->id,
                    ]);
                }
            }

            // 3. Seed Exception if defined
            if (! empty($data['exception'])) {
                $exData = $data['exception'];
                $detectedAt = Carbon::now()->subHours($exData['detected_hours_ago']);
                $resolvedAt = ! empty($exData['resolved_hours_ago'])
                    ? Carbon::now()->subHours($exData['resolved_hours_ago'])
                    : null;

                $exception = ShipmentException::create([
                    'shipment_id' => $shipment->id,
                    'type' => $exData['type'],
                    'severity' => $exData['severity'],
                    'status' => $exData['status'],
                    'detected_at' => $detectedAt,
                    'resolved_at' => $resolvedAt,
                    'description' => $exData['description'],
                    'created_by' => $operator->id,
                ]);

                // Also log a tracking event representing the exception
                TrackingEvent::create([
                    'shipment_id' => $shipment->id,
                    'location_id' => $data['destination']->id,
                    'event_type' => TrackingEventType::ExceptionAlert,
                    'is_exception' => true,
                    'occurred_at' => $detectedAt,
                    'latitude' => $data['destination']->latitude,
                    'longitude' => $data['destination']->longitude,
                    'description' => "[{$exData['type']->value}] {$exData['description']}",
                    'created_by' => $operator->id,
                ]);

                // Create customer notification
                Notification::create([
                    'user_id' => $data['customer']->id,
                    'shipment_id' => $shipment->id,
                    'type' => 'EXCEPTION_ALERT',
                    'message' => "Shipment {$shipment->shipment_number} has an active exception: {$exData['type']->label()}.",
                    'read_at' => null,
                ]);
            }

            // 4. Create Audit Log for initial creation
            AuditLog::create([
                'user_id' => $operator->id,
                'action' => 'SHIPMENT_CREATED',
                'entity_type' => Shipment::class,
                'entity_id' => $shipment->id,
                'old_values' => null,
                'new_values' => [
                    'shipment_number' => $shipment->shipment_number,
                    'current_status' => $shipment->current_status->value,
                    'carrier' => $data['carrier']->code,
                    'origin' => $data['origin']->city,
                    'destination' => $data['destination']->city,
                ],
                'ip_address' => '127.0.0.1',
            ]);
        }
    }
}
