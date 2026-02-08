<style>
    /* ===== Reset ===== */
    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    /* ===== Body ===== */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: var(--muted);
        color: var(--foreground);
        line-height: 1.6;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    a {
        color: var(--primary);
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }

    /* ===== Dashboard Layout ===== */
    .dashboard-layout {
        display: flex;
        min-height: 100vh;
    }

    /* ===== Sidebar ===== */
    .sidebar {
        width: 280px;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        background: var(--sidebar);
        border-right: 1px solid var(--sidebar-border);
        z-index: 100;
        overflow-y: auto;
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        height: 60px;
        display: flex;
        align-items: center;
        padding: 0 1.25rem;
        border-bottom: 1px solid var(--sidebar-border);
        flex-shrink: 0;
    }

    .sidebar-logo {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: var(--sidebar-foreground);
    }
    .sidebar-logo:hover {
        text-decoration: none;
        color: var(--sidebar-foreground);
    }

    .sidebar-logo-icon {
        width: 36px;
        height: 36px;
        background: var(--sidebar-primary);
        color: var(--sidebar-primary-foreground);
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sidebar-logo-icon svg {
        width: 20px;
        height: 20px;
    }

    .sidebar-logo-text {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--sidebar-foreground);
    }

    .sidebar-nav {
        padding: 1rem 0;
        flex: 1;
        overflow-y: auto;
    }

    .sidebar-section {
        margin-bottom: 0.5rem;
    }

    .sidebar-section-title {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0.75rem 1.25rem 0.5rem;
        color: var(--muted-foreground);
        font-weight: 600;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.55rem 1.25rem;
        color: var(--sidebar-foreground);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 0;
        transition: all 0.15s;
        border-left: 3px solid transparent;
    }
    .sidebar-link:hover {
        background: var(--sidebar-accent);
        color: var(--sidebar-accent-foreground);
        text-decoration: none;
    }
    .sidebar-link.active {
        background: var(--sidebar-accent);
        color: var(--sidebar-primary);
        border-left-color: var(--sidebar-primary);
        font-weight: 600;
    }
    .sidebar-link svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .sidebar-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 99;
    }

    /* ===== Main Content ===== */
    .main-content {
        margin-left: 280px;
        flex: 1;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ===== Topbar ===== */
    .topbar {
        height: 60px;
        background: var(--card);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
        position: sticky;
        top: 0;
        z-index: 50;
        flex-shrink: 0;
    }

    .topbar-left {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .topbar-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .topbar-btn {
        background: transparent;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0.5rem;
        cursor: pointer;
        color: var(--foreground);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
    }
    .topbar-btn:hover {
        background: var(--accent);
    }
    .topbar-btn svg {
        width: 18px;
        height: 18px;
    }

    .mobile-menu-btn {
        display: none;
        background: transparent;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0.5rem;
        cursor: pointer;
        color: var(--foreground);
    }
    .mobile-menu-btn svg {
        width: 20px;
        height: 20px;
    }

    /* ===== Breadcrumb ===== */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: var(--muted-foreground);
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .breadcrumb a {
        color: var(--muted-foreground);
        text-decoration: none;
        transition: color 0.15s;
    }
    .breadcrumb a:hover {
        color: var(--foreground);
        text-decoration: none;
    }
    .breadcrumb-separator {
        color: var(--muted-foreground);
        font-size: 0.75rem;
    }
    .breadcrumb-current {
        color: var(--foreground);
        font-weight: 500;
    }

    /* ===== User Dropdown ===== */
    .user-dropdown {
        position: relative;
    }

    .user-dropdown-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.375rem 0.5rem;
        background: transparent;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.15s;
        color: var(--foreground);
    }
    .user-dropdown-btn:hover {
        background: var(--accent);
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--primary);
        color: var(--primary-foreground);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .user-info {
        text-align: left;
    }

    .user-name {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--foreground);
        line-height: 1.2;
    }

    .user-role {
        font-size: 0.7rem;
        color: var(--muted-foreground);
        line-height: 1.2;
    }

    .user-dropdown-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        width: 220px;
        background: var(--popover);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--card-shadow-hover);
        display: none;
        z-index: 200;
        overflow: hidden;
    }
    .user-dropdown.active .user-dropdown-menu {
        display: block;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1rem;
        font-size: 0.8125rem;
        color: var(--foreground);
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s;
        border: none;
        background: none;
        width: 100%;
    }
    .dropdown-item:hover {
        background: var(--accent);
        text-decoration: none;
    }
    .dropdown-item svg {
        width: 16px;
        height: 16px;
    }

    .dropdown-item-danger {
        color: var(--destructive);
    }
    .dropdown-item-danger:hover {
        background: color-mix(in oklch, var(--destructive) 10%, transparent);
    }

    .dropdown-divider {
        height: 1px;
        background: var(--border);
        margin: 0.25rem 0;
    }

    .dropdown-header {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        color: var(--muted-foreground);
    }

    /* ===== Page Content ===== */
    .page-content {
        padding: 1.5rem;
        flex: 1;
    }

    /* ===== Page Header ===== */
    .page-header {
        margin-bottom: 1.5rem;
    }

    .page-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--foreground);
        margin: 0;
        line-height: 1.3;
    }

    .page-description {
        font-size: 0.875rem;
        color: var(--muted-foreground);
        margin-top: 0.25rem;
    }

    /* ===== Cards ===== */
    .card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--card-shadow);
        color: var(--card-foreground);
    }

    .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    .card-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--card-foreground);
        margin: 0;
    }

    .card-body {
        padding: 1.25rem;
    }

    .card-footer {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border);
        background: var(--muted);
        border-radius: 0 0 var(--radius) var(--radius);
    }

    /* ===== Stats Grid ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--card-shadow);
        transition: box-shadow 0.2s;
    }
    .stat-card:hover {
        box-shadow: var(--card-shadow-hover);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .stat-icon svg {
        width: 24px;
        height: 24px;
    }

    .stat-icon-primary {
        background: color-mix(in oklch, var(--primary) 15%, transparent);
        color: var(--primary);
    }
    .stat-icon-success {
        background: color-mix(in oklch, var(--success) 15%, transparent);
        color: var(--success);
    }
    .stat-icon-warning {
        background: color-mix(in oklch, var(--warning) 15%, transparent);
        color: var(--warning);
    }
    .stat-icon-danger {
        background: color-mix(in oklch, var(--danger) 15%, transparent);
        color: var(--danger);
    }
    .stat-icon-info {
        background: color-mix(in oklch, var(--info) 15%, transparent);
        color: var(--info);
    }

    .stat-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--muted-foreground);
        font-weight: 500;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--foreground);
        line-height: 1.2;
    }

    /* ===== Grid Layouts ===== */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }

    /* ===== Tables ===== */
    .table-container {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        padding: 0.75rem 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--muted-foreground);
        background: var(--muted);
        border-bottom: 1px solid var(--border);
    }

    .table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border);
        font-size: 0.875rem;
        color: var(--foreground);
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background: var(--accent);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ===== Buttons ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: var(--radius);
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
        line-height: 1.5;
        font-family: inherit;
    }
    .btn:hover {
        text-decoration: none;
    }
    .btn svg {
        width: 16px;
        height: 16px;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
    }
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .btn-primary {
        background: var(--primary);
        color: var(--primary-foreground);
        border-color: var(--primary);
    }
    .btn-primary:hover {
        opacity: 0.9;
    }

    .btn-secondary {
        background: var(--secondary);
        color: var(--secondary-foreground);
        border-color: var(--border);
    }
    .btn-secondary:hover {
        background: var(--accent);
    }

    .btn-destructive {
        background: var(--destructive);
        color: var(--destructive-foreground);
        border-color: var(--destructive);
    }
    .btn-destructive:hover {
        opacity: 0.9;
    }

    .btn-ghost {
        background: transparent;
        color: var(--foreground);
        border-color: transparent;
    }
    .btn-ghost:hover {
        background: var(--accent);
    }

    .btn-danger {
        background: var(--danger);
        color: white;
        border-color: var(--danger);
    }
    .btn-danger:hover {
        opacity: 0.9;
    }

    .btn-success {
        background: var(--success);
        color: var(--success-foreground);
        border-color: var(--success);
    }
    .btn-success:hover {
        opacity: 0.9;
    }

    .btn-warning {
        background: var(--warning);
        color: var(--warning-foreground);
        border-color: var(--warning);
    }
    .btn-warning:hover {
        opacity: 0.9;
    }

    .btn-info {
        background: var(--info);
        color: var(--info-foreground);
        border-color: var(--info);
    }
    .btn-info:hover {
        opacity: 0.9;
    }

    .btn-outline {
        background: transparent;
        color: var(--foreground);
        border-color: var(--border);
    }
    .btn-outline:hover {
        background: var(--accent);
    }

    /* ===== Form Elements ===== */
    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.375rem;
        color: var(--foreground);
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        border: 1px solid var(--input);
        border-radius: var(--radius);
        background: var(--background);
        color: var(--foreground);
        transition: border-color 0.15s, box-shadow 0.15s;
        font-family: inherit;
        line-height: 1.5;
    }

    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--ring);
        box-shadow: 0 0 0 2px var(--ring);
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: var(--muted-foreground);
    }

    .form-input.is-invalid {
        border-color: var(--destructive);
    }
    .form-input.is-invalid:focus {
        box-shadow: 0 0 0 2px var(--destructive);
    }

    .form-error {
        font-size: 0.8rem;
        color: var(--destructive);
        margin-top: 0.25rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .form-hint {
        font-size: 0.8rem;
        color: var(--muted-foreground);
        margin-top: 0.25rem;
    }

    .form-textarea {
        min-height: 80px;
        resize: vertical;
    }

    /* ===== Badges ===== */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.125rem 0.625rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 9999px;
        line-height: 1.5;
    }

    .badge-primary {
        background: color-mix(in oklch, var(--primary) 15%, transparent);
        color: var(--primary);
    }
    .badge-success {
        background: color-mix(in oklch, var(--success) 15%, transparent);
        color: var(--success);
    }
    .badge-warning {
        background: color-mix(in oklch, var(--warning) 15%, transparent);
        color: var(--warning);
    }
    .badge-danger {
        background: color-mix(in oklch, var(--danger) 15%, transparent);
        color: var(--danger);
    }
    .badge-secondary {
        background: var(--secondary);
        color: var(--secondary-foreground);
    }
    .badge-info {
        background: color-mix(in oklch, var(--info) 15%, transparent);
        color: var(--info);
    }

    /* ===== Alerts ===== */
    .alert {
        padding: 1rem;
        border-radius: var(--radius);
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }
    .alert svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }

    .alert-success {
        background: color-mix(in oklch, var(--success) 10%, var(--background));
        border: 1px solid var(--success);
        color: var(--success);
    }

    .alert-error {
        background: color-mix(in oklch, var(--danger) 10%, var(--background));
        border: 1px solid var(--danger);
        color: var(--danger);
    }

    .alert-warning {
        background: color-mix(in oklch, var(--warning) 10%, var(--background));
        border: 1px solid var(--warning);
        color: var(--warning);
    }

    .alert-info {
        background: color-mix(in oklch, var(--info) 10%, var(--background));
        border: 1px solid var(--info);
        color: var(--info);
    }

    .alert-content {
        flex: 1;
    }

    .alert-message {
        font-size: 0.875rem;
    }

    .alert-title {
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .alert-dismiss {
        background: none;
        border: none;
        cursor: pointer;
        color: inherit;
        opacity: 0.7;
        padding: 0;
        display: flex;
        align-items: center;
    }
    .alert-dismiss:hover {
        opacity: 1;
    }
    .alert-dismiss svg {
        width: 16px;
        height: 16px;
    }

    .alert-errors-list {
        list-style: disc;
        margin: 0.5rem 0 0 1rem;
        padding: 0;
    }
    .alert-errors-list li {
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
    }

    /* ===== Action Buttons ===== */
    .action-buttons {
        display: flex;
        gap: 0.25rem;
        align-items: center;
    }

    .action-btn {
        padding: 0.375rem;
        border: none;
        background: transparent;
        color: var(--muted-foreground);
        cursor: pointer;
        border-radius: var(--radius);
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .action-btn:hover {
        background: var(--accent);
        color: var(--foreground);
    }
    .action-btn svg {
        width: 16px;
        height: 16px;
    }

    .action-btn-danger:hover {
        background: color-mix(in oklch, var(--destructive) 15%, transparent);
        color: var(--destructive);
    }

    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }

    .empty-state-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto 1rem;
        color: var(--muted-foreground);
    }
    .empty-state-icon svg {
        width: 48px;
        height: 48px;
    }

    .empty-state-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: var(--foreground);
    }

    .empty-state-description {
        font-size: 0.875rem;
        color: var(--muted-foreground);
    }

    /* ===== Pagination ===== */
    .pagination {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.25rem;
        list-style: none;
        margin: 0;
    }

    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.5rem;
        font-size: 0.8125rem;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        color: var(--foreground);
        text-decoration: none;
        transition: all 0.15s;
    }
    .pagination a:hover {
        background: var(--accent);
        text-decoration: none;
    }
    .pagination .active span {
        background: var(--primary);
        color: var(--primary-foreground);
        border-color: var(--primary);
    }
    .pagination .disabled span {
        color: var(--muted-foreground);
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ===== User Cell ===== */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-cell-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--primary);
        color: var(--primary-foreground);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .user-cell-info {
        display: flex;
        flex-direction: column;
    }

    .user-cell-name {
        font-weight: 500;
        color: var(--foreground);
        font-size: 0.875rem;
    }

    .user-cell-email {
        font-size: 0.75rem;
        color: var(--muted-foreground);
    }

    /* ===== Checkbox ===== */
    .checkbox-input {
        accent-color: var(--primary);
    }

    .checkbox-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        cursor: pointer;
        transition: all 0.15s;
    }
    .checkbox-item:hover {
        background: var(--accent);
    }

    .checkbox-label {
        font-size: 0.875rem;
        color: var(--foreground);
    }

    /* ===== Modal ===== */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 200;
        display: none;
        place-items: center;
    }
    .modal-overlay.active {
        display: grid;
    }

    .modal {
        background: var(--card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        width: 100%;
        max-width: 480px;
        box-shadow: var(--card-shadow-hover);
        margin: 1rem;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
    }

    .modal-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--foreground);
        margin: 0;
    }

    .modal-close {
        background: transparent;
        border: none;
        color: var(--muted-foreground);
        cursor: pointer;
        padding: 0.25rem;
        border-radius: var(--radius);
        display: flex;
        align-items: center;
        transition: all 0.15s;
    }
    .modal-close:hover {
        background: var(--accent);
        color: var(--foreground);
    }
    .modal-close svg {
        width: 18px;
        height: 18px;
    }

    .modal-body {
        padding: 1.25rem;
    }

    .modal-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border);
    }

    /* ===== Filters Bar ===== */
    .filters-bar {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 200px;
    }
    .search-box svg {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--muted-foreground);
        pointer-events: none;
    }
    .search-box input {
        padding-left: 2.25rem;
        width: 100%;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        padding-right: 0.75rem;
        font-size: 0.875rem;
        border: 1px solid var(--input);
        border-radius: var(--radius);
        background: var(--background);
        color: var(--foreground);
        transition: border-color 0.15s, box-shadow 0.15s;
        font-family: inherit;
    }
    .search-box input:focus {
        outline: none;
        border-color: var(--ring);
        box-shadow: 0 0 0 2px var(--ring);
    }
    .search-box input::placeholder {
        color: var(--muted-foreground);
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-label {
        font-size: 0.8rem;
        color: var(--muted-foreground);
        font-weight: 500;
        white-space: nowrap;
    }

    /* ===== Softphone-Specific ===== */
    .phone-dialer {
        max-width: 300px;
        margin: 0 auto;
    }

    .dial-pad {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        padding: 1rem;
    }

    .dial-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        font-size: 1.25rem;
        font-weight: 600;
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--foreground);
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-family: inherit;
    }
    .dial-btn:hover {
        background: var(--accent);
    }

    .dial-btn-call {
        background: var(--success);
        color: var(--success-foreground);
        border-color: var(--success);
    }
    .dial-btn-call:hover {
        opacity: 0.9;
    }

    .dial-btn-hangup {
        background: var(--destructive);
        color: var(--destructive-foreground);
        border-color: var(--destructive);
    }
    .dial-btn-hangup:hover {
        opacity: 0.9;
    }

    .extension-card {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        background: var(--card);
        transition: all 0.2s;
    }

    .extension-card.ext-active {
        border-color: var(--success);
        background: color-mix(in oklch, var(--success) 5%, var(--card));
    }

    .extension-card.ext-registered {
        border-color: var(--info);
        background: color-mix(in oklch, var(--info) 5%, var(--card));
    }

    .call-timer {
        font-family: monospace;
        font-size: 1rem;
        font-weight: 600;
    }

    /* ===== Utility ===== */
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-muted { color: var(--muted-foreground); }
    .text-success { color: var(--success); }
    .text-danger { color: var(--danger); }
    .text-warning { color: var(--warning); }
    .text-info { color: var(--info); }
    .text-sm { font-size: 0.875rem; }
    .text-xs { font-size: 0.75rem; }
    .font-medium { font-weight: 500; }
    .font-semibold { font-weight: 600; }
    .font-bold { font-weight: 700; }
    .mb-0 { margin-bottom: 0; }
    .mb-1 { margin-bottom: 0.5rem; }
    .mb-2 { margin-bottom: 1rem; }
    .mb-3 { margin-bottom: 1.5rem; }
    .mt-1 { margin-top: 0.5rem; }
    .mt-2 { margin-top: 1rem; }
    .gap-1 { gap: 0.5rem; }
    .gap-2 { gap: 1rem; }
    .w-full { width: 100%; }
    .flex { display: flex; }
    .flex-col { flex-direction: column; }
    .items-center { align-items: center; }
    .justify-between { justify-content: space-between; }
    .justify-end { justify-content: flex-end; }
    .flex-wrap { flex-wrap: wrap; }
    .hidden { display: none; }

    /* ===== Auth Layout ===== */
    .auth-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--muted);
        padding: 1rem;
    }

    .auth-card {
        width: 100%;
        max-width: 400px;
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--card-shadow-hover);
        padding: 2rem;
    }

    .auth-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .auth-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1rem;
        background: color-mix(in oklch, var(--primary) 15%, transparent);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
    }
    .auth-icon svg {
        width: 28px;
        height: 28px;
    }

    .auth-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--foreground);
        margin-bottom: 0.25rem;
    }

    .auth-subtitle {
        font-size: 0.875rem;
        color: var(--muted-foreground);
    }

    /* ===== Responsive ===== */
    @media (max-width: 1024px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
        .grid-3 {
            grid-template-columns: 1fr;
        }
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .sidebar-overlay.active {
            display: block;
        }
        .main-content {
            margin-left: 0;
        }
        .mobile-menu-btn {
            display: flex;
        }
        .page-content {
            padding: 1rem;
        }
        .topbar {
            padding: 0 1rem;
        }
        .page-title {
            font-size: 1.25rem;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .user-info {
            display: none;
        }
        .filters-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .search-box {
            min-width: auto;
        }
    }
</style>
