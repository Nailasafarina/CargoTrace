# Data Dictionary - CargoTrace Platform

This data dictionary outlines the database schemas, column specifications, constraints, and design justifications for the **CargoTrace** global visibility application.

---

## 1. Architectural Design Decision: Omission of Soft Deletes

In this enterprise logistics architecture, soft deletes (`deleted_at`) are intentionally omitted across operational tracking tables in favor of an **Explicit Lifecycle State Machine** combined with an **Append-Only Audit Trail**. The technical and business rationales are as follows:

1. **Maritime Regulatory & Compliance Immutability**:
   Under maritime shipping guidelines (such as the Safety of Life at Sea / SOLAS conventions, customs import declarations, and Bill of Lading statutory records), shipping manifests and event logs must never be retroactively concealed or soft-deleted. When an order is aborted, its status transitions explicitly to `CANCELLED` with an accompanying audit entry.
2. **Unique Key Constraint Integrity**:
   Standard MySQL unique constraints (e.g. `shipment_number` or tracking references) enforce uniqueness across all rows regardless of nullability in other columns. Soft deletes typically lead to collisions if a reference is reused or recreated, necessitating non-standard composite indexes.
3. **Query Performance & Index Optimizations**:
   Omitting `whereNull('deleted_at')` simplifies query execution plans and eliminates composite index overhead across multi-table joins involving shipments, events, and exceptions.
4. **Comprehensive Audit Logging**:
   State transitions and administrative actions are faithfully captured in the `audit_logs` table with snapshot payloads (`old_values`, `new_values`), user IDs, and client IP addresses.

---

## 2. Table Specifications

### 2.1 Table: `users`
Stores system accounts categorized by distinct operational roles.

| Column | Data Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key | Unique user identifier |
| `name` | `VARCHAR(255)` | No | None | None | User's full name or organization entity |
| `email` | `VARCHAR(255)` | No | None | Unique Index | User email address used for authentication |
| `email_verified_at` | `TIMESTAMP` | Yes | `NULL` | None | Timestamp of email verification |
| `password` | `VARCHAR(255)` | No | None | None | Bcrypt hashed account password |
| `role` | `ENUM('admin','operator','customer')` | No | `'customer'` | Index | Role-based authorization discriminator |
| `remember_token` | `VARCHAR(100)` | Yes | `NULL` | None | Session token for persistent login |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Last update timestamp |

---

### 2.2 Table: `carriers`
Stores multi-modal freight transport operators (shipping lines, freight forwarders).

| Column | Data Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key | Unique carrier identifier |
| `name` | `VARCHAR(255)` | No | None | None | Commercial name of carrier (e.g. Maersk, MSC) |
| `code` | `VARCHAR(10)` | No | None | Unique Index | Standardized carrier code (e.g. MSK, ONE) |
| `contact_email` | `VARCHAR(255)` | Yes | `NULL` | None | Operational inquiries contact email |
| `contact_phone` | `VARCHAR(255)` | Yes | `NULL` | None | Operational dispatch telephone contact |
| `is_active` | `BOOLEAN` | No | `TRUE` | Index | Status flag indicating active dispatch capability |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Last update timestamp |

---

### 2.3 Table: `locations`
Geographic hubs, ports, airports, and terminals along primary route corridors.

| Column | Data Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key | Unique location identifier |
| `name` | `VARCHAR(255)` | No | None | None | Formal hub or port name |
| `type` | `VARCHAR(20)` | No | None | Index | Facility classification (`port`, `airport`, `warehouse`, `city`) |
| `country` | `VARCHAR(255)` | No | None | Index | Sovereign country where facility is situated |
| `city` | `VARCHAR(255)` | No | None | Index | Municipal territory or port city |
| `latitude` | `DECIMAL(10,7)` | No | None | None | Exact WGS84 GPS latitude coordinate |
| `longitude` | `DECIMAL(10,7)` | No | None | None | Exact WGS84 GPS longitude coordinate |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Last update timestamp |

---

### 2.4 Table: `shipments`
Primary entity tracking global freight movements, schedules, delays, and statuses.

| Column | Data Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key | Unique internal shipment record identifier |
| `shipment_number` | `VARCHAR(50)` | No | None | Unique Index | Public tracking or Bill of Lading reference |
| `customer_id` | `BIGINT UNSIGNED` | No | None | FK &rarr; `users.id` (RESTRICT) | Account of customer owning cargo |
| `carrier_id` | `BIGINT UNSIGNED` | No | None | FK &rarr; `carriers.id` (RESTRICT) | Freight carrier assigned to transport |
| `origin_location_id` | `BIGINT UNSIGNED` | No | None | FK &rarr; `locations.id` (RESTRICT) | Origin port / departure hub |
| `destination_location_id` | `BIGINT UNSIGNED` | No | None | FK &rarr; `locations.id` (RESTRICT) | Destination port / arrival terminal |
| `cargo_type` | `VARCHAR(255)` | No | None | None | Description of goods (e.g. Reefer, FCL Dry) |
| `weight_kg` | `DECIMAL(12,2)` | No | None | None | Total gross cargo weight in kilograms |
| `volume_cbm` | `DECIMAL(10,2)` | No | None | None | Total volumetric space in cubic meters |
| `expected_departure_at` | `DATETIME` | No | None | None | Scheduled departure date and time |
| `expected_arrival_at` | `DATETIME` | No | None | None | Contractual estimated arrival date and time |
| `estimated_arrival_at` | `DATETIME` | Yes | `NULL` | None | Dynamically updated ETA factoring delays |
| `current_status` | `VARCHAR(30)` | No | `'DRAFT'` | Index | Current lifecycle phase (`ShipmentStatus` enum) |
| `delay_status` | `VARCHAR(30)` | No | `'ON_TIME'` | Index | Operational delay classification (`DelayStatus`) |
| `delay_hours` | `INT` | Yes | `NULL` | None | Calculated cumulative delay in hours |
| `created_by` | `BIGINT UNSIGNED` | No | None | FK &rarr; `users.id` (RESTRICT) | Operator/Admin account creating record |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Last update timestamp |

**Composite Indexes:**
- `['customer_id', 'current_status']` : Optimizes customer portal lookups.
- `['current_status', 'delay_status']` : Optimizes exception and KPI calculation queries.
- `['carrier_id', 'current_status']` : Optimizes carrier operational overview.

---

### 2.5 Table: `shipment_items`
Itemized manifest breakdown of cargo packed within a shipment.

| Column | Data Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key | Unique item line identifier |
| `shipment_id` | `BIGINT UNSIGNED` | No | None | FK &rarr; `shipments.id` (CASCADE) | Parent shipment reference |
| `description` | `VARCHAR(255)` | No | None | None | Cargo line item description |
| `quantity` | `INT` | No | None | None | Unit count / package quantity |
| `weight_kg` | `DECIMAL(10,2)` | No | None | None | Line item weight in kilograms |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Last update timestamp |

---

### 2.6 Table: `tracking_events`
Chronological milestone telemetry recorded throughout transit.

| Column | Data Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key | Unique milestone event identifier |
| `shipment_id` | `BIGINT UNSIGNED` | No | None | FK &rarr; `shipments.id` (CASCADE) | Associated shipment reference |
| `location_id` | `BIGINT UNSIGNED` | Yes | `NULL` | FK &rarr; `locations.id` (NULL ON DELETE) | Waypoint hub where event transpired |
| `event_type` | `VARCHAR(50)` | No | None | Index | Telemetry event code (`TrackingEventType`) |
| `is_exception` | `BOOLEAN` | No | `FALSE` | Index | Flag indicating an operational anomaly |
| `occurred_at` | `DATETIME` | No | None | Index | Real-world occurrence timestamp |
| `latitude` | `DECIMAL(10,7)` | Yes | `NULL` | None | Geographic latitude at milestone |
| `longitude` | `DECIMAL(10,7)` | Yes | `NULL` | None | Geographic longitude at milestone |
| `description` | `TEXT` | No | None | None | Detailed milestone log message |
| `created_by` | `BIGINT UNSIGNED` | No | None | FK &rarr; `users.id` (RESTRICT) | User recording the event |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Last update timestamp |

**Composite Index:**
- `['shipment_id', 'occurred_at']` : Enforces instant chronological timeline ordering.

---

### 2.7 Table: `shipment_exceptions`
Disruptive logistical conditions impacting schedule or cargo integrity.

| Column | Data Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key | Unique exception record identifier |
| `shipment_id` | `BIGINT UNSIGNED` | No | None | FK &rarr; `shipments.id` (CASCADE) | Affected shipment reference |
| `type` | `VARCHAR(40)` | No | None | Index | Exception type (`ExceptionType` enum) |
| `severity` | `VARCHAR(20)` | No | `'MEDIUM'` | Index | Severity level (`LOW`, `MEDIUM`, `HIGH`, `CRITICAL`) |
| `status` | `VARCHAR(20)` | No | `'OPEN'` | Index | Lifecycle status (`OPEN`, `INVESTIGATING`, `RESOLVED`) |
| `detected_at` | `DATETIME` | No | None | None | Timestamp when anomaly was flagged |
| `resolved_at` | `DATETIME` | Yes | `NULL` | None | Timestamp when anomaly was resolved |
| `description` | `TEXT` | No | None | None | Root cause and operational remediation notes |
| `created_by` | `BIGINT UNSIGNED` | No | None | FK &rarr; `users.id` (RESTRICT) | User logging the exception |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Last update timestamp |

---

### 2.8 Table: `notifications`
Alerts pushed to users regarding milestones, status changes, or exceptions.

| Column | Data Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key | Unique notification identifier |
| `user_id` | `BIGINT UNSIGNED` | No | None | FK &rarr; `users.id` (CASCADE) | Recipient user account |
| `shipment_id` | `BIGINT UNSIGNED` | Yes | `NULL` | FK &rarr; `shipments.id` (NULL ON DELETE) | Related shipment (if applicable) |
| `type` | `VARCHAR(50)` | No | None | Index | Notification category code |
| `message` | `TEXT` | No | None | None | Notification body message |
| `read_at` | `TIMESTAMP` | Yes | `NULL` | Index | Read acknowledgement timestamp |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | None | Creation timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Last update timestamp |

---

### 2.9 Table: `audit_logs`
Immutable forensic audit trail recording critical mutations and user actions.

| Column | Data Type | Nullable | Default | Keys / Indexes | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | No | Auto Increment | Primary Key | Unique audit log entry identifier |
| `user_id` | `BIGINT UNSIGNED` | Yes | `NULL` | FK &rarr; `users.id` (NULL ON DELETE) | Actor user executing the action |
| `action` | `VARCHAR(50)` | No | None | Index | Standard action verb (e.g. `SHIPMENT_CREATED`) |
| `entity_type` | `VARCHAR(100)` | No | None | Index | Eloquent model class reference |
| `entity_id` | `BIGINT UNSIGNED` | No | None | Index | Target record primary key ID |
| `old_values` | `JSON` | Yes | `NULL` | None | Pre-mutation JSON attribute snapshot |
| `new_values` | `JSON` | Yes | `NULL` | None | Post-mutation JSON attribute snapshot |
| `ip_address` | `VARCHAR(45)` | Yes | `NULL` | None | IPv4 or IPv6 client origin address |
| `created_at` | `TIMESTAMP` | Yes | `NULL` | Index | Event execution timestamp |
| `updated_at` | `TIMESTAMP` | Yes | `NULL` | None | Record metadata update timestamp |
