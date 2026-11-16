# CargoTrace Security Checklist & Vulnerability Audit Report

This document records the comprehensive security review, defense-in-depth mitigations, and verification results for the **CargoTrace: Global Shipment Visibility & Tracking Platform**.

---

## 1. Security Checklist Matrix

| # | Security Category | Item / Threat Scenario | Status | Mitigating Implementation & Files |
|---|---|---|---|---|
| 1 | **Authentication** | Password Storage & Hashing | **PASS** | Bcrypt with work factor 12 (`BCRYPT_ROUNDS=12`). Handled in [RegisteredUserController.php](file:///E:/coding/UAS%20pemograman%20web/app/Http/Controllers/Auth/RegisteredUserController.php) and [User.php](file:///E:/coding/UAS%20pemograman%20web/app/Models/User.php#L39). |
| 2 | **Authentication** | API Token Security (Sanctum) | **PASS** | SHA-256 hashed bearer tokens stored in `personal_access_tokens`. Revocation on logout in [AuthController.php](file:///E:/coding/UAS%20pemograman%20web/app/Http/Controllers/Api/AuthController.php#L68). |
| 3 | **Authentication** | Privilege Escalation in Registration | **MITIGATED** | Public web & API registration strictly forces `role = 'customer'`. Tampered requests attempting to inject `role: admin` or `operator` are rejected with HTTP 422 in [RegisterApiRequest.php](file:///E:/coding/UAS%20pemograman%20web/app/Http/Requests/Api/RegisterApiRequest.php#L27). |
| 4 | **Authentication** | Brute-Force Rate Limiting | **PASS** | Login endpoint throttled to 6 requests/minute via `throttle:6,1` middleware in [routes/api.php](file:///E:/coding/UAS%20pemograman%20web/routes/api.php#L17). |
| 5 | **Authorization** | Multi-Role Access Control | **PASS** | Server-side role middleware `role:admin`, `role:operator`, `role:customer` in [EnsureUserHasRole.php](file:///E:/coding/UAS%20pemograman%20web/app/Http/Middleware/EnsureUserHasRole.php). Non-privileged users receive HTTP 403 Forbidden. |
| 6 | **Authorization** | Customer Data Isolation | **PASS** | Customers can only view shipments where `customer_id = auth()->id()`. Verified in [ShipmentPolicy.php](file:///E:/coding/UAS%20pemograman%20web/app/Policies/ShipmentPolicy.php#L25) and [ShipmentController.php](file:///E:/coding/UAS%20pemograman%20web/app/Http/Controllers/ShipmentController.php#L42). |
| 7 | **Authorization** | Operator Mutation Restrictions | **PASS** | Operators can create and update fleet shipments and log events, but are prohibited from deleting shipments, viewing audit logs, or modifying system delay thresholds. |
| 8 | **Validation** | Mass-Assignment Protection | **PASS** | Every Eloquent model defines strict `$fillable` arrays. No `$guarded = []`. Input validated before persistence via dedicated Form Request classes. |
| 9 | **XSS Defense** | Template Escaping in Blade | **PASS** | Zero occurrences of unescaped `{!! !!}` in any application Blade view. All dynamic outputs sanitized through `{{ }}` (`htmlspecialchars`). |
| 10 | **XSS Defense** | Leaflet.js DOM XSS Sanitization | **MITIGATED** | Custom `escapeHtml()` function created in [show.blade.php](file:///E:/coding/UAS%20pemograman%20web/resources/views/shipments/show.blade.php#L756-L762) to escape location names, milestones, and status before inserting into map popup innerHTML. |
| 11 | **SQL Injection** | Parameterized Queries & Whitelist | **PASS** | All database queries utilize Eloquent ORM parameter bindings. Sorting columns protected by strict in-array whitelist in [ShipmentController.php](file:///E:/coding/UAS%20pemograman%20web/app/Http/Controllers/ShipmentController.php#L85). Raw queries contain no user variables. |
| 12 | **CSRF Defense** | Form Tokens & SameSite Cookies | **PASS** | `@csrf` blade directive included on all state-changing HTML forms. Sanctum CSRF cookie middleware enabled for client sessions. |
| 13 | **Audit Trails** | Sensitive Operation Traceability | **PASS** | [AuditLog.php](file:///E:/coding/UAS%20pemograman%20web/app/Models/AuditLog.php) automatically logs user ID, action, entity, IP address, and JSON diffs of previous/new states. Access restricted to Administrators. |
| 14 | **Secrets & Git** | Environment Variable Hygiene | **PASS** | `.env` ignored in [.gitignore](file:///E:/coding/UAS%20pemograman%20web/.gitignore#L3). Clean [.env.example](file:///E:/coding/UAS%20pemograman%20web/.env.example) contains zero secrets, real API keys, or production passwords. |
| 15 | **Information Leak** | Production Error Handling | **PASS** | API exception handler formats errors into clean `{ message, errors }` responses without leaking PHP stack traces or system filepaths when `APP_DEBUG=false`. |

---

## 2. Identified Vulnerabilities & Mitigations (Audit Log)

### A. Potential Privilege Escalation via API Registration
- **Finding**: During the security audit, `RegisterApiRequest` allowed callers to pass `'role' => Rule::in(['admin', 'operator', 'customer'])`, which could allow malicious users to register an administrative account.
- **Mitigation Applied**: Restricted public registration role validation to `Rule::in(['customer'])` and hardcoded `'role' => 'customer'` in [AuthController.php](file:///E:/coding/UAS%20pemograman%20web/app/Http/Controllers/Api/AuthController.php#L28). Administrative role promotion is now restricted exclusively to existing platform administrators.
- **Verification**: Verified by automated test `SecurityAuditTest::test_public_api_registration_cannot_escalate_role_to_admin` (HTTP 422 rejected).

### B. Client-Side DOM XSS in Leaflet.js Map Popups
- **Finding**: Leaflet `marker.bindPopup(...)` evaluates popup contents as raw HTML. If an operator logged a tracking event description or location name containing malicious script tags (e.g. `<img src=x onerror=alert(1)>`), it could execute when users clicked the map pins.
- **Mitigation Applied**: Implemented a client-side `escapeHtml()` utility function in [show.blade.php](file:///E:/coding/UAS%20pemograman%20web/resources/views/shipments/show.blade.php#L756-L762) that creates an in-memory DOM text node to escape all dynamic strings before template literal interpolation.
- **Verification**: Verified by automated test `SecurityAuditTest::test_xss_payloads_are_safely_escaped_in_shipment_view`.

### C. SQL Injection Defense in Dynamic Table Sorting
- **Finding**: User-provided `sort_by` query parameters in server-side filtering could introduce SQL injection if passed directly to `orderBy()`.
- **Mitigation Applied**: Enforced strict whitelist validation against an allowed array (`['expected_departure_at', 'expected_arrival_at', 'shipment_number', 'created_at', 'current_status', 'weight_kg']`), defaulting safely to `'expected_departure_at'` if an unknown column or SQL fragment is detected.
- **Verification**: Verified by automated test `SecurityAuditTest::test_sql_injection_payloads_in_search_and_sorting_are_neutralized`.

---

## 3. Production Readiness Instructions

Before deploying CargoTrace to production environments:
1. Ensure `.env` has `APP_ENV=production` and `APP_DEBUG=false`.
2. Generate an application encryption key: `php artisan key:generate`.
3. Set secure, strong credentials for database and Redis.
4. Run deployment optimization commands:
   ```sh
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   npm run build
   ```
5. Ensure HTTPS / SSL termination is active so all cookies are transmitted with the `Secure` flag.
