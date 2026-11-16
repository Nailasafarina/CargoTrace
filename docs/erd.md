# Entity-Relationship Diagram (ERD) - CargoTrace Platform

This document describes the relational database architecture for **CargoTrace: Global Shipment Visibility & Tracking Platform**.

---

## Mermaid ER Diagram

```mermaid
erDiagram
    USERS ||--o{ SHIPMENTS : "places / owns (customer_id)"
    USERS ||--o{ SHIPMENTS : "creates (created_by)"
    CARRIERS ||--o{ SHIPMENTS : "transports (carrier_id)"
    LOCATIONS ||--o{ SHIPMENTS : "originates (origin_location_id)"
    LOCATIONS ||--o{ SHIPMENTS : "destined_for (destination_location_id)"
    
    SHIPMENTS ||--|{ SHIPMENT_ITEMS : "contains (shipment_id)"
    SHIPMENTS ||--o{ TRACKING_EVENTS : "chronicles (shipment_id)"
    LOCATIONS ||--o{ TRACKING_EVENTS : "milestone_at (location_id)"
    USERS ||--o{ TRACKING_EVENTS : "logged_by (created_by)"
    
    SHIPMENTS ||--o{ SHIPMENT_EXCEPTIONS : "encounters (shipment_id)"
    USERS ||--o{ SHIPMENT_EXCEPTIONS : "reported_by (created_by)"
    
    USERS ||--o{ NOTIFICATIONS : "receives (user_id)"
    SHIPMENTS ||--o{ NOTIFICATIONS : "relates_to (shipment_id)"
    
    USERS ||--o{ AUDIT_LOGS : "triggers (user_id)"

    USERS {
        bigint id PK
        string name
        string email UK
        string password
        enum role "admin, operator, customer"
        datetime email_verified_at
        datetime created_at
        datetime updated_at
    }

    CARRIERS {
        bigint id PK
        string name
        string code UK
        string contact_email
        string contact_phone
        boolean is_active
        datetime created_at
        datetime updated_at
    }

    LOCATIONS {
        bigint id PK
        string name
        string type "port, airport, warehouse, city"
        string country
        string city
        decimal latitude "10,7"
        decimal longitude "10,7"
        datetime created_at
        datetime updated_at
    }

    SHIPMENTS {
        bigint id PK
        string shipment_number UK
        bigint customer_id FK
        bigint carrier_id FK
        bigint origin_location_id FK
        bigint destination_location_id FK
        string cargo_type
        decimal weight_kg "12,2"
        decimal volume_cbm "10,2"
        datetime expected_departure_at
        datetime expected_arrival_at
        datetime estimated_arrival_at
        string current_status "DRAFT, BOOKED, DISPATCHED, IN_TRANSIT, OUT_FOR_DELIVERY, DELIVERED, CANCELLED"
        string delay_status "ON_TIME, DELAYED, CRITICAL_DELAY"
        int delay_hours
        bigint created_by FK
        datetime created_at
        datetime updated_at
    }

    SHIPMENT_ITEMS {
        bigint id PK
        bigint shipment_id FK
        string description
        int quantity
        decimal weight_kg "10,2"
        datetime created_at
        datetime updated_at
    }

    TRACKING_EVENTS {
        bigint id PK
        bigint shipment_id FK
        bigint location_id FK
        string event_type
        boolean is_exception
        datetime occurred_at
        decimal latitude "10,7"
        decimal longitude "10,7"
        text description
        bigint created_by FK
        datetime created_at
        datetime updated_at
    }

    SHIPMENT_EXCEPTIONS {
        bigint id PK
        bigint shipment_id FK
        string type "CUSTOMS_HOLD, WEATHER_DELAY, PORT_CONGESTION, DAMAGED, MISROUTED, DOCUMENTATION_ISSUE"
        string severity "LOW, MEDIUM, HIGH, CRITICAL"
        string status "OPEN, INVESTIGATING, RESOLVED"
        datetime detected_at
        datetime resolved_at
        text description
        bigint created_by FK
        datetime created_at
        datetime updated_at
    }

    NOTIFICATIONS {
        bigint id PK
        bigint user_id FK
        bigint shipment_id FK
        string type
        text message
        datetime read_at
        datetime created_at
        datetime updated_at
    }

    AUDIT_LOGS {
        bigint id PK
        bigint user_id FK
        string action
        string entity_type
        bigint entity_id
        json old_values
        json new_values
        string ip_address
        datetime created_at
        datetime updated_at
    }
```

---

## Cardinality & Relationship Breakdown

1. **Users &rarr; Shipments (Customer)**: `1 : N` (One Customer owns Zero or Many Shipments). Enforces customer data isolation.
2. **Users &rarr; Shipments (Creator)**: `1 : N` (One Operator/Admin user creates Zero or Many Shipments).
3. **Carriers &rarr; Shipments**: `1 : N` (One Freight Carrier transports Zero or Many Shipments).
4. **Locations &rarr; Shipments (Origin & Destination)**: `1 : N` (One Port/Hub serves as the departure/arrival terminal for Many Shipments).
5. **Shipments &rarr; Shipment Items**: `1 : N` (One Shipment contains One or Many individual packaged cargo items/manifest lines).
6. **Shipments &rarr; Tracking Events**: `1 : N` (One Shipment has Zero or Many chronological tracking milestones).
7. **Shipments &rarr; Shipment Exceptions**: `1 : N` (One Shipment can experience Zero or Many operational issues, e.g., `CUSTOMS_HOLD`).
8. **Users &rarr; Notifications**: `1 : N` (One User receives Zero or Many alerts).
9. **Users &rarr; Audit Logs**: `1 : N` (One User generates Zero or Many immutable audit records).
