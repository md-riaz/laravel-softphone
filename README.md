# FusionPBX Softphone + Analytics

**Laravel API + Tyro Dashboard UI + SIP.js WebRTC Softphone**

A browser-based softphone system with admin dashboard, agent console, and call analytics. Built with Laravel for the backend API, Tyro Dashboard-style UI (shadcn CSS theme), and SIP.js for WebRTC calling.

---

## Architecture

| Layer | Responsibility |
|-------|---------------|
| **Browser (SIP.js)** | SIP registration, calls, media, call state |
| **Laravel API** | Persist call data, admin CRUD, analytics |
| **Tyro Dashboard UI** | Render admin & agent UI (shadcn CSS variables, dark mode, SVG icons) |
| **FusionPBX** | SIP registrar + RTP (external, not included) |

> **Key principle**: The browser is the only real-time actor. The API only stores data — it never controls calls.

---

## Quick Start

### Requirements
- PHP 8.2+
- Composer
- SQLite (or MySQL/PostgreSQL)

### Installation

```bash
# Clone the repository
git clone https://github.com/md-riaz/laravel-softphone.git
cd laravel-softphone

# Install dependencies
composer install

# Configure environment
cp .env.example .env
php artisan key:generate

# For SQLite (simplest)
touch database/database.sqlite

# Run migrations and seed demo data
php artisan migrate --seed

# Start the server
php artisan serve
```

### Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@example.com` | `password` |
| Agent | `agent@example.com` | `password` |

---

## Project Preview

### Login Page
![Login](https://github.com/user-attachments/assets/10f2a2d4-2d31-427b-aecc-1a1f2bec2694)

### Admin Dashboard
![Admin Dashboard](https://github.com/user-attachments/assets/20f3130d-106e-4dc0-85c7-3e52a1bf6c6a)

### Companies Management
![Companies](https://github.com/user-attachments/assets/0f4d460e-6b86-4d2b-bdea-a85647033980)

### PBX Connections
![PBX Connections](https://github.com/user-attachments/assets/38d38fcf-fc1a-4881-8f13-fce8f605fd50)

### Extensions Management
![Extensions](https://github.com/user-attachments/assets/aeafcc48-9ecd-46a0-8470-153e0fea4bfd)

### Dispositions
![Dispositions](https://github.com/user-attachments/assets/97167d78-0cc3-491d-97bb-2f4505f6755d)

### Call Analytics
![Analytics](https://github.com/user-attachments/assets/605337a5-85ac-4c37-bc86-163310f0bc12)

### Agent Console (with Dialer + Extensions)
![Agent Console](https://github.com/user-attachments/assets/3ac52af3-cdd4-41ce-a252-1947f4710752)

### Agent Dashboard
![Agent Dashboard](https://github.com/user-attachments/assets/384afc50-8930-4c91-9b52-8183bf9d16bf)

---

## Features

### Admin Panel
- **Companies** — Create and manage companies
- **PBX Connections** — Configure FusionPBX server connections (host, port, WSS URL, STUN/TURN)
- **Extensions** — Manage SIP extensions, assign to agents
- **Dispositions** — Define call disposition codes per company (with color coding)
- **Analytics** — View aggregated call metrics (total, inbound, outbound, answered, missed, avg duration)
- **Reports** — Analytics data with CSV export capability

### Agent Console
- **Extension Management** — Activate/deactivate up to 6 SIP extensions simultaneously
- **Phone Dialer** — Numpad dialer with extension selector
- **Active Calls** — Real-time call status display (answer, hold, mute, hangup)
- **Wrap-up Panel** — Add notes and disposition codes after each call
- **Call History** — Paginated history with direction, status, duration, notes, and dispositions

### SIP.js Integration
- **Local SIP.js bundle** — Pre-built from SIP.js v0.21.2, no CDN dependency
- **SIPAccountManager** — Multi-account manager supporting up to 6 simultaneous registrations
- **Call flows** — Inbound/outbound calls with answer, end, hold, mute
- **STUN/TURN** — Configurable ICE servers per PBX connection
- **Graceful degradation** — UI remains functional even without SIP connectivity

### Call Notes & Dispositions
- **Wrap-up panel** in agent console for adding notes after calls
- **Multiple disposition codes** can be assigned per call
- **Notes displayed** in call history with tooltip for full content
- **Dispositions shown** as colored badges in call history

---

## Database Schema

| Table | Description |
|-------|-------------|
| `users` | Admin and agent users with role field |
| `companies` | Company entities |
| `pbx_connections` | FusionPBX server configurations |
| `extensions` | SIP extensions assigned to users |
| `calls` | Call records (uuid, direction, timestamps, duration) |
| `call_notes` | Text notes linked to calls |
| `dispositions` | Disposition code definitions per company |
| `call_dispositions` | Pivot: calls ↔ dispositions |
| `call_analytics` | Aggregated daily analytics per company |

---

## Internal API Endpoints

All endpoints require session authentication and are under the `/internal` prefix.

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/internal/me` | Current user info |
| `GET` | `/internal/extensions` | User's assigned extensions |
| `POST` | `/internal/extensions/{id}/activate` | Activate extension (max 6) |
| `POST` | `/internal/extensions/{id}/deactivate` | Deactivate extension |
| `POST` | `/internal/calls` | Create call record |
| `POST` | `/internal/calls/{id}/answered` | Mark call answered |
| `POST` | `/internal/calls/{id}/ended` | Mark call ended |
| `POST` | `/internal/calls/{id}/wrapup` | Add notes & dispositions |
| `GET` | `/internal/calls` | List calls (with filters) |
| `GET` | `/internal/dispositions` | Active dispositions |

See [docs/internal_api_contract.md](docs/internal_api_contract.md) for full details.

---

## Business Rules

- **Max 6 active extensions** per agent (enforced on activation)
- **Call duration** computed server-side from `started_at` → `ended_at`
- **Talk time** computed from `answered_at` → `ended_at`
- **Status transitions**: `ringing → answered → ended` or `ringing → missed`
- **Admin role** required for company/PBX/extension/disposition management
- **Agent role** required for console and call history access

---

## Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=AuthTest
php artisan test --filter=CompanyTest
php artisan test --filter=CallTest
```

**33 tests with 85 assertions** covering:
- Authentication (login, logout, invalid credentials)
- Dashboard access (admin vs agent views)
- Admin CRUD (companies, extensions)
- Internal API (calls, extensions, wrapup)
- Agent console and call history access
- Business rule enforcement (max 6 extensions)

---

## CLI Commands

```bash
# Aggregate analytics (daily)
php artisan analytics:aggregate {--date=}

# Export analytics to CSV
php artisan analytics:export {--from=} {--to=} {--company=}
```

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/        # 9 controllers
│   ├── Middleware/          # RoleMiddleware
│   └── Requests/           # 10 form request validators
├── Jobs/                   # AggregateCallAnalytics
├── Models/                 # 8 Eloquent models
database/
├── factories/              # 8 model factories
├── migrations/             # 9 migrations
├── seeders/                # DatabaseSeeder with demo data
docs/
├── tyro_dashboard_analysis.md
├── sipjs_browser_model.md
└── internal_api_contract.md
public/js/
├── sip.js                  # SIP.js v0.21.2 (bundled IIFE)
└── sip-account-manager.js  # Multi-account SIP manager
resources/views/
├── admin/                  # 17 admin CRUD views
├── agent/                  # Console + call history
├── auth/                   # Login page
├── dashboard/              # Admin + agent dashboards
├── layouts/                # Main app layout
└── partials/               # Sidebar, topbar, styles, scripts, theme
tests/Feature/              # 7 test files, 33 tests
```

---

## UI Framework

This project replicates the [Tyro Dashboard](https://github.com/hasinhayder/tyro-dashboard) CSS system:
- **shadcn-inspired CSS variables** for theming (light/dark mode)
- **Custom component classes** (`.card`, `.btn`, `.stat-card`, `.table`, `.badge`, etc.)
- **Inline SVG icons** (Heroicons-style)
- **Inter font** from Bunny Fonts
- **No Bootstrap, no Tailwind utility classes**

See [docs/tyro_dashboard_analysis.md](docs/tyro_dashboard_analysis.md) for details.

---

## Hard Exclusions (by design)

- ❌ WebSockets (no Laravel Echo/Reverb)
- ❌ Redis
- ❌ Server-side SIP control
- ❌ External API integrations
- ❌ Real-time call monitoring from backend
- ❌ PBX control from Laravel

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
