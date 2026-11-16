# CargoTrace REST API Documentation

CargoTrace provides a comprehensive, token-authenticated RESTful API powered by **Laravel Sanctum**.

---

## 1. General API Specifications

- **Base URL**: `http://localhost:8000/api` (or `https://your-domain/api`)
- **Format**: JSON (`Content-Type: application/json`, `Accept: application/json`)
- **Authentication**: Bearer Token in HTTP Authorization Header:
  ```http
  Authorization: Bearer <SANCTUM_TOKEN>
  ```
- **Postman Collection**: Located at [docs/postman/CargoTrace.postman_collection.json](file:///E:/coding/UAS%20pemograman%20web/docs/postman/CargoTrace.postman_collection.json) with pre-configured environment variables `base_url` and `token`.

---

## 2. Standard HTTP Status Codes

| Code | Meaning | Typical Usage |
|---|---|---|
| **200 OK** | Request succeeded | `GET`, `PUT`, `PATCH`, `DELETE` operations |
| **201 Created** | Resource created successfully | Successful `POST` operations |
| **401 Unauthorized** | Missing or invalid Sanctum token | Accessing protected routes without Bearer token |
| **403 Forbidden** | Role authorization failed | Customers accessing other users' shipments |
| **404 Not Found** | Resource identifier does not exist | Invalid shipment, carrier, or location ID |
| **422 Unprocessable Content** | Validation error or invalid FSM transition | Form validation failures or terminal status modification |
| **429 Too Many Requests** | Rate limit threshold exceeded | Exceeding 6 login attempts per minute |

---

## 3. Endpoints Summary

### Authentication
| Method | Endpoint | Description | Auth Required |
|---|---|---|---|
| `POST` | `/api/register` | Register new customer account & obtain token | Public |
| `POST` | `/api/login` | Authenticate user & obtain token (rate-limited) | Public |
| `POST` | `/api/logout` | Revoke current Bearer token | Sanctum |
| `GET` | `/api/user` | Fetch authenticated user profile | Sanctum |

### Shipments
| Method | Endpoint | Description | Auth Required |
|---|---|---|---|
| `GET` | `/api/shipments` | List shipments (server-side pagination, filter, sort) | Sanctum |
| `POST` | `/api/shipments` | Create new shipment with items (Admin/Operator) | Sanctum |
| `GET` | `/api/shipments/{id}` | Retrieve shipment detail & item specifications | Sanctum |
| `PUT/PATCH` | `/api/shipments/{id}` | Update shipment parameters | Sanctum |
| `DELETE` | `/api/shipments/{id}` | Delete shipment record (Admin only) | Sanctum |

### Milestone Tracking
| Method | Endpoint | Description | Auth Required |
|---|---|---|---|
| `GET` | `/api/shipments/{id}/tracking` | List chronological tracking events | Sanctum |
| `POST` | `/api/shipments/{id}/tracking` | Log milestone & trigger FSM status transition | Sanctum |

### Master Data: Carriers & Locations
| Method | Endpoint | Description | Auth Required |
|---|---|---|---|
| `GET` | `/api/carriers` | List all freight carriers | Sanctum |
| `POST` | `/api/carriers` | Register new carrier (Admin only) | Sanctum |
| `GET` | `/api/locations` | List all global logistics hubs | Sanctum |
| `POST` | `/api/locations` | Register new location with GPS coords (Admin only) | Sanctum |

### Disruption & Exception Management
| Method | Endpoint | Description | Auth Required |
|---|---|---|---|
| `GET` | `/api/shipments/{id}/exceptions` | List exceptions flagged on shipment | Sanctum |
| `POST` | `/api/shipments/{id}/exceptions` | Flag new operational exception (CUSTOMS_HOLD, etc.) | Sanctum |
| `PATCH` | `/api/exceptions/{id}` | Update exception status (`OPEN` &rarr; `RESOLVED`) | Sanctum |

---

## 4. Sample Request & Response Payloads

### POST /api/login
**Request Body:**
```json
{
  "email": "admin@cargotrace.test",
  "password": "Password123!"
}
```
**Response (200 OK):**
```json
{
  "message": "Authentication successful.",
  "access_token": "1|qX8g...vP3",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "System Administrator",
    "email": "admin@cargotrace.test",
    "role": "admin"
  }
}
```

### POST /api/shipments/{id}/tracking
**Request Body:**
```json
{
  "status": "IN_TRANSIT",
  "location_id": 4,
  "occurred_at": "2026-11-17 08:30:00",
  "latitude": 12.7855,
  "longitude": 45.0187,
  "description": "Vessel transiting the Bab-el-Mandeb Strait towards Suez Canal."
}
```
**Response (201 Created):**
```json
{
  "message": "Tracking milestone event logged successfully.",
  "event": {
    "id": 42,
    "shipment_id": 1,
    "event_type": "IN_TRANSIT",
    "occurred_at": "2026-11-17 08:30:00",
    "description": "Vessel transiting the Bab-el-Mandeb Strait towards Suez Canal."
  },
  "shipment_status": "IN_TRANSIT"
}
```

### Error Response Format (Consistent RFC 7807 Style)
```json
{
  "message": "Invalid status transition from DELIVERED to IN_TRANSIT. Terminal status cannot be mutated.",
  "errors": {
    "status": [
      "Cannot transition from terminal DELIVERED status."
    ]
  }
}
```
