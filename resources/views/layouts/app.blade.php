<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Softphone Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-active: #3b82f6;
            --topbar-height: 60px;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f1f5f9;
            overflow-x: hidden;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: #cbd5e1;
            z-index: 1040;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }
        .sidebar-brand {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid #334155;
        }
        .sidebar-brand i {
            margin-right: 0.75rem;
            font-size: 1.5rem;
            color: var(--sidebar-active);
        }
        .sidebar-nav {
            padding: 1rem 0;
        }
        .sidebar-heading {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.75rem 1.25rem 0.5rem;
            color: #64748b;
            font-weight: 600;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 0.6rem 1.25rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar-link:hover {
            color: #e2e8f0;
            background: var(--sidebar-hover);
        }
        .sidebar-link.active {
            color: #fff;
            background: var(--sidebar-hover);
            border-left-color: var(--sidebar-active);
        }
        .sidebar-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .topbar .btn-toggle-sidebar {
            display: none;
            border: none;
            background: none;
            font-size: 1.25rem;
            cursor: pointer;
        }
        .page-content {
            padding: 1.5rem;
        }
        .page-header {
            margin-bottom: 1.5rem;
        }
        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        .page-header .breadcrumb {
            margin-bottom: 0;
            font-size: 0.8rem;
        }
        .stat-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .stat-card .card-body {
            padding: 1.25rem;
        }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }
        .stat-card .stat-label {
            font-size: 0.8rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .table-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .table-card .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        .table-card .table {
            margin: 0;
        }
        .table-card .table th {
            background: #f8fafc;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
        }
        .table-card .table td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            font-size: 0.875rem;
        }
        .badge-active { background-color: #22c55e; }
        .badge-inactive { background-color: #ef4444; }
        .badge-inbound { background-color: #3b82f6; }
        .badge-outbound { background-color: #8b5cf6; }
        .badge-ringing { background-color: #f59e0b; }
        .badge-answered { background-color: #22c55e; }
        .badge-ended { background-color: #64748b; }
        .badge-missed { background-color: #ef4444; }
        .form-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .form-card .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }
        .form-card .card-body {
            padding: 1.5rem;
        }
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .topbar .btn-toggle-sidebar {
                display: block;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0; left: 0;
                width: 100%; height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1035;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
        .phone-dialer {
            max-width: 300px;
        }
        .phone-dialer .dial-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 1.25rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s;
        }
        .phone-dialer .dial-btn:hover {
            background: #f1f5f9;
        }
        .phone-dialer .call-btn {
            background: #22c55e;
            color: #fff;
            border-color: #22c55e;
        }
        .phone-dialer .call-btn:hover {
            background: #16a34a;
        }
        .phone-dialer .hangup-btn {
            background: #ef4444;
            color: #fff;
            border-color: #ef4444;
        }
        .phone-dialer .hangup-btn:hover {
            background: #dc2626;
        }
        .extension-card {
            border-radius: 0.5rem;
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
        }
        .extension-card.active {
            border-color: #22c55e;
            background: #f0fdf4;
        }
        .extension-card.registered {
            border-color: #3b82f6;
            background: #eff6ff;
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-telephone-fill"></i> Softphone
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-heading">Main</div>
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>

            @if(auth()->user()->isAdmin())
            <div class="sidebar-heading">Administration</div>
            <a href="{{ route('admin.companies.index') }}" class="sidebar-link {{ request()->routeIs('admin.companies.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Companies
            </a>
            <a href="{{ route('admin.pbx-connections.index') }}" class="sidebar-link {{ request()->routeIs('admin.pbx-connections.*') ? 'active' : '' }}">
                <i class="bi bi-hdd-network-fill"></i> PBX Connections
            </a>
            <a href="{{ route('admin.extensions.index') }}" class="sidebar-link {{ request()->routeIs('admin.extensions.*') ? 'active' : '' }}">
                <i class="bi bi-phone-fill"></i> Extensions
            </a>
            <a href="{{ route('admin.dispositions.index') }}" class="sidebar-link {{ request()->routeIs('admin.dispositions.*') ? 'active' : '' }}">
                <i class="bi bi-tags-fill"></i> Dispositions
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="sidebar-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-fill"></i> Analytics
            </a>
            <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i> Reports
            </a>
            @endif

            <div class="sidebar-heading">Agent</div>
            <a href="{{ route('agent.console') }}" class="sidebar-link {{ request()->routeIs('agent.console') ? 'active' : '' }}">
                <i class="bi bi-headset"></i> Console
            </a>
            <a href="{{ route('agent.call-history') }}" class="sidebar-link {{ request()->routeIs('agent.call-history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Call History
            </a>
        </nav>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div class="d-flex align-items-center">
                <button class="btn-toggle-sidebar me-3" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <nav aria-label="breadcrumb">
                    @yield('breadcrumb')
                </nav>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-{{ auth()->user()->isAdmin() ? 'primary' : 'success' }}">
                    {{ ucfirst(auth()->user()->role) }}
                </span>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <div class="page-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
    @else
    <div class="container">
        @yield('content')
    </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
        document.getElementById('sidebarOverlay')?.addEventListener('click', toggleSidebar);
    </script>
    @stack('scripts')
</body>
</html>
