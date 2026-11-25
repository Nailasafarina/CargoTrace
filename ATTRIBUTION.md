# Open Source & Third-Party Attribution

**CargoTrace: Global Shipment Visibility & Tracking Platform** is built upon open-source software, standards-based web protocols, and public telemetry APIs. We gratefully acknowledge the creators and maintainers of the following technologies:

---

## 1. Core Framework & Backend Libraries
- **Laravel Framework (v12.x / v13.x)**
  - License: [MIT License](https://opensource.org/licenses/MIT)
  - Author: Taylor Otwell and the Laravel Core Team
  - Usage: Core application architecture, Eloquent ORM, Routing, Dependency Injection, Validation, and Testing Harness.
- **Laravel Sanctum**
  - License: [MIT License](https://opensource.org/licenses/MIT)
  - Author: Laravel LLC
  - Usage: Lightweight API token authentication and SPA session protection.

---

## 2. Frontend & Styling Libraries
- **Tailwind CSS (v4.x)**
  - License: [MIT License](https://opensource.org/licenses/MIT)
  - Author: Tailwind Labs, Inc.
  - Usage: Responsive utility-first light industrial logistics UI styling.
- **Heroicons**
  - License: [MIT License](https://opensource.org/licenses/MIT)
  - Author: Tailwind Labs, Inc.
  - Usage: SVG iconography for logistics markers, alerts, status pills, and navigation.
- **Plus Jakarta Sans, Instrument Sans & JetBrains Mono Typography**
  - License: [SIL Open Font License 1.1](https://scripts.sil.org/OFL)
  - Author: Tokotype, Instrument, Rodrigo Fuenzalida & JetBrains (served via Bunny Fonts / Vite)
  - Usage: Primary UI and monospace telemetry typography across the CargoTrace application.

---

## 3. Mapping, GIS & External Telemetry Providers
- **Leaflet.js (v1.9.4)**
  - License: [BSD 2-Clause License](https://opensource.org/licenses/BSD-2-Clause)
  - Author: Volodymyr Agafonkin and Leaflet contributors
  - Usage: Interactive shipment corridor visualization, markers, smooth fractional zoom, and polyline navigation.
- **OpenStreetMap**
  - License: [Open Database License (ODbL)](https://www.openstreetmap.org/copyright)
  - Copyright: &copy; OpenStreetMap contributors
  - Usage: Cartographic basemap tiles.
- **Nominatim Geocoding Service**
  - Data License: Open Database License (ODbL)
  - Usage: Automated forward-geocoding of logistics hubs and port coordinates via Laravel HTTP Client with custom User-Agent compliance.
- **Open-Meteo Meteorological API**
  - License: [Attribution 4.0 International (CC BY 4.0)](https://creativecommons.org/licenses/by/4.0/)
  - Usage: Real-time ambient weather telemetry (temperature, wind speed, WMO code) for situational awareness along transit corridors.

---

## 4. Visualizations & Analytical Charts
- **Chart.js (v4.x)**
  - License: [MIT License](https://opensource.org/licenses/MIT)
  - Usage: Interactive operational charts (Lifecycle Status Doughnut, Carrier Allocation Bar, Delay Severity Bar, Exception Doughnut, and Operational Health Telemetry).

---

## 5. Development & Testing Infrastructure
- **PHPUnit (v12.x)**: [BSD 3-Clause License](https://opensource.org/licenses/BSD-3-Clause) by Sebastian Bergmann.
- **Laravel Pint**: [MIT License](https://opensource.org/licenses/MIT) by Nuno Maduro and Laravel LLC.
- **Vite**: [MIT License](https://opensource.org/licenses/MIT) by Evan You and the Vite Core Team.
