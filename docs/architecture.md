# CargoTrace System Architecture Documentation

This document describes the software architecture, architectural layers, request lifecycle, and data flow of **CargoTrace: Global Shipment Visibility & Tracking Platform**.

---

## 1. High-Level Architectural Diagram

```mermaid
flowchart TD
    subgraph ClientLayer["Client Layer (Presentation)"]
        Browser["Desktop & Mobile Web Browser"]
        APIClient["API Consumers / Mobile App / Postman"]
    end

    subgraph IngressRouting["Ingress, Routing & Security Layer"]
        Nginx["Nginx Web Server"]
        WebRoutes["Web Routes (routes/web.php)"]
        APIRoutes["REST API Routes (routes/api.php)"]
        AuthMiddleware["Authentication Middleware\n('auth' / 'auth:sanctum')"]
        RoleMiddleware["Role Guard Middleware\n('role:admin', 'role:operator', 'role:customer')"]
        RateLimiter["Rate Limiting Throttle\n('throttle:6,1')"]
    end

    subgraph ControllerLayer["Thin Controller Layer"]
        WebControllers["Web Controllers\n(Shipment, Tracking, Exception, Dashboard, Location, Carrier, AuditLog)"]
        APIControllers["API Controllers\n(Api/Shipment, Api/Tracking, Api/Exception, Api/Carrier, Api/Location)"]
        FormRequests["Form Request Validation & Authorization\n(StoreShipmentRequest, TrackingRequest, etc.)"]
        APIResources["API Resource Transformers\n(ShipmentResource, TrackingResource, ExceptionResource)"]
    end

    subgraph DomainServiceLayer["Domain Service Layer (Business Logic)"]
        ShipmentService["ShipmentService\n(DB Transactions, Multi-Item Handling)"]
        TrackingService["TrackingService\n(FSM Lifecycle Transitions, Append-Only Events)"]
        EtaService["EtaService\n(Deterministic Delay Rules, Threshold Evaluation)"]
        ExceptionService["ExceptionService\n(Disruption Management, FSM Status)"]
        DashboardService["DashboardService\n(Aggregations, KPIs, Chart.js Formatter)"]
        GeocodingService["GeocodingService\n(OSM Nominatim Client, Caching, Fallbacks)"]
        WeatherService["WeatherService\n(Open-Meteo Client, WMO Mapper, Disclaimers)"]
    end

    subgraph PolicyLayer["Authorization & Policy Layer"]
        ShipmentPolicy["ShipmentPolicy"]
        CarrierPolicy["CarrierPolicy"]
        LocationPolicy["LocationPolicy"]
        AuditLogPolicy["AuditLog Verification"]
    end

    subgraph DataLayer["Persistence & External Telemetry"]
        Eloquent["Eloquent ORM Models\n(Shipment, Event, Exception, Location, Carrier, Notification, AuditLog)"]
        Observers["Model Observers\n(ShipmentObserver -> AuditLog)"]
        MySQL[("MySQL 8.0 Relational Database")]
        CacheStore[("Cache Store / Redis\n(Coordinates & Weather Telemetry)")]
        ExternalAPIs["External Upstream APIs\n(OSM Nominatim & Open-Meteo)"]
    end

    %% Connections
    Browser -->|HTTP Request| Nginx
    APIClient -->|JSON Request| Nginx

    Nginx --> WebRoutes
    Nginx --> APIRoutes

    WebRoutes --> AuthMiddleware --> RoleMiddleware --> WebControllers
    APIRoutes --> RateLimiter --> AuthMiddleware --> APIControllers

    WebControllers --> FormRequests
    APIControllers --> FormRequests

    WebControllers -.-> PolicyLayer
    APIControllers -.-> PolicyLayer

    WebControllers --> DomainServiceLayer
    APIControllers --> DomainServiceLayer

    DomainServiceLayer --> Eloquent
    DomainServiceLayer --> ExternalAPIs

    Eloquent --> Observers
    Eloquent --> MySQL
    DomainServiceLayer --> CacheStore

    APIControllers --> APIResources
    APIResources --> APIClient
    WebControllers -->|Blade + Tailwind View| Browser
```

---

## 2. Layer Responsibilities & Architectural Patterns

### A. Presentation Layer (Blade + Tailwind CSS v4 + Vanilla JS)
- **Zero Heavy Frontend Frameworks**: Implemented using clean server-rendered Blade templates, Tailwind CSS v4 utility classes, and Vanilla JavaScript (Fetch API).
- **Interactive UI Components**:
  - **Leaflet.js & OpenStreetMap**: Interactive route corridor maps with colored pins, popup telemetry, polyline milestones, and WGS84 coordinates.
  - **Chart.js**: Dark-mode operational analytics (status doughnut, carrier allocation bar, delay severity, and exception type charts).
  - **Vanilla Fetch API**: Asynchronous auto-geocoding in Location forms without page reloads.

### B. Controller Layer (Thin Controllers)
- Controllers are strictly responsible for:
  1. Authorizing incoming actions via Policies or Gates.
  2. Receiving validated inputs via Form Request classes.
  3. Delegating domain business logic to Domain Services.
  4. Returning HTTP responses (Views or JSON Resources).
- Controllers **never** execute raw database queries, multivariable calculations, or complex state transitions directly.

### C. Domain Service Layer
- **ShipmentService**: Orchestrates shipment creation, atomic database transactions for shipment + items, and auditing.
- **TrackingService**: Enforces the Finite State Machine (FSM) rules for status transitions (`CREATED` &rarr; `BOOKED` &rarr; `PICKED_UP` &rarr; `IN_TRANSIT` &rarr; `DELIVERED`). `DELIVERED` is strictly terminal. Appends tracking history without modifying past records.
- **EtaService**: Evaluates deterministic delay rules (`estimated_arrival_at > expected_arrival_at`). Classifies severity into `LOW` (&le;6h), `MEDIUM` (6–24h), and `HIGH` (&gt;24h) based on configurable thresholds.
- **ExceptionService**: Handles disruption logging (`CUSTOMS_HOLD`, `WEATHER_DELAY`, `PORT_CONGESTION`) and FSM status updates (`OPEN` &rarr; `INVESTIGATING` &rarr; `RESOLVED`).
- **DashboardService**: Optimizes aggregate database queries (`count`, `group by`, `avg`) to avoid N+1 queries and formats metric payloads for Chart.js.
- **GeocodingService & WeatherService**: Resilient HTTP Client integrations with timeouts, retries, caching, and fallback handling.

### D. Security & Data Integrity Layer
- **Multi-Tenant / Role Isolation**: Customer users are strictly isolated to their own shipments and notifications at both Controller and Policy levels.
- **Audit Trails**: [AuditLog.php](file:///E:/coding/UAS%20pemograman%20web/app/Models/AuditLog.php) records actor, action, timestamp, IP address, and JSON diffs of state changes across administrative and operational events.
