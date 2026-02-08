# Tyro Dashboard Analysis

## Overview
Tyro Dashboard (https://github.com/hasinhayder/tyro-dashboard) is a Laravel admin panel package providing RBAC, user management, and dynamic CRUD generation with a shadcn-inspired custom CSS theme.

## UI Architecture
- **CSS Framework**: Custom shadcn-inspired CSS variables (NOT Tailwind utility classes, NOT Bootstrap)
- **Theme**: Light/dark mode via CSS variables in `:root` and `.dark` class
- **Font**: Inter (from fonts.bunny.net)
- **Icons**: Inline SVG (Heroicons-style outlines)
- **Layout**: Fixed sidebar (280px) + sticky topbar (60px) + scrollable content area

## Key CSS Classes Used in This Project
| Category | Classes |
|----------|---------|
| Layout | `.dashboard-layout`, `.sidebar`, `.main-content`, `.topbar`, `.page-content` |
| Cards | `.card`, `.card-header`, `.card-title`, `.card-body`, `.card-footer` |
| Stats | `.stats-grid`, `.stat-card`, `.stat-icon`, `.stat-content`, `.stat-label`, `.stat-value` |
| Tables | `.table-container`, `.table` |
| Forms | `.form-group`, `.form-label`, `.form-input`, `.form-select`, `.form-textarea`, `.form-error`, `.form-row` |
| Buttons | `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-destructive`, `.btn-ghost`, `.btn-danger`, `.btn-sm` |
| Badges | `.badge`, `.badge-primary`, `.badge-success`, `.badge-warning`, `.badge-danger`, `.badge-info` |
| Actions | `.action-buttons`, `.action-btn`, `.action-btn-danger` |
| Sidebar | `.sidebar-link`, `.sidebar-section`, `.sidebar-section-title`, `.sidebar-logo` |

## Extension Points
- Sidebar sections via `.sidebar-section` divs
- Page content via `@yield('content')` in layouts
- Breadcrumbs via `@section('breadcrumb')`
- Scripts/styles via `@push('scripts')` / `@push('styles')`
- Flash messages auto-rendered from session

## How New Pages Are Added
1. Create a Blade view extending `layouts.app`
2. Define `@section('title')`, `@section('breadcrumb')`, `@section('content')`
3. Add route to `routes/web.php`
4. Add sidebar link in `partials/sidebar.blade.php`
5. Use Tyro CSS classes for consistent styling

## Auth Handling
- Session-based authentication (Laravel default)
- Role-based access via `role` column on users table
- Middleware checks `user->role` against allowed roles
- Sidebar dynamically shows/hides admin sections based on `auth()->user()->isAdmin()`

## API Consumption Pattern
- Internal API uses `fetch()` with CSRF token from `<meta>` tag
- All API responses are JSON
- No external API dependencies
