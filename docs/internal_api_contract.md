# Internal API Contract

## Characteristics
- Same-origin only (session-based auth via Laravel)
- Used only by dashboard pages (browser fetch)
- All endpoints under `/internal` prefix
- CSRF token required for POST requests

## Authentication

### `GET /internal/me`
Returns authenticated user info.
```json
{ "id": 1, "name": "Agent User", "email": "agent@example.com", "role": "agent" }
```

## Extensions

### `GET /internal/extensions`
List user's assigned extensions with PBX connection details.

### `POST /internal/extensions/{id}/activate`
Activate extension for SIP registration. **Business rule: max 6 active extensions per agent.**
Returns 422 if limit exceeded.

### `POST /internal/extensions/{id}/deactivate`
Deactivate extension.

## Call Analytics (Browser → Backend)

### `POST /internal/calls`
Create call record when call starts.
```json
{
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "extension_id": 1,
    "user_id": 2,
    "direction": "outbound",
    "caller_number": "1001",
    "callee_number": "5551234567",
    "started_at": "2026-02-08T10:00:00Z"
}
```
Returns: call object with `id`

### `POST /internal/calls/{id}/answered`
Mark call as answered.
```json
{ "answered_at": "2026-02-08T10:00:05Z" }
```

### `POST /internal/calls/{id}/ended`
Mark call as ended. Server computes `duration` and `talk_time`.
```json
{ "ended_at": "2026-02-08T10:05:30Z" }
```

### `POST /internal/calls/{id}/wrapup`
Add wrap-up notes and dispositions after call ends.
```json
{
    "notes": "Customer interested in premium plan",
    "disposition_ids": [1, 3]
}
```

## Dispositions

### `GET /internal/dispositions`
List active dispositions for user's company.

## Analytics

### `GET /internal/analytics/summary`
Aggregated analytics data for the authenticated user's scope.

## Call List

### `GET /internal/calls`
Paginated call records with filters:
- `date_from`, `date_to` — date range
- `direction` — inbound/outbound
- `status` — ringing/answered/ended/missed
- `extension_id` — filter by extension

## Business Rules
- Maximum **6 active extensions** per agent (enforced on activate)
- Call duration computed server-side from timestamps
- Status transitions: `ringing → answered → ended` OR `ringing → missed`
- Notes stored in `call_notes` table (multiple per call)
- Dispositions stored in `call_dispositions` pivot table
