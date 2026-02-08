<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Softphone')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @auth
    <nav>
        <strong>Softphone</strong>
        <a href="{{ route('dashboard') }}">Dashboard</a>
        @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.companies.index') }}">Companies</a>
            <a href="{{ route('admin.pbx-connections.index') }}">PBX</a>
            <a href="{{ route('admin.extensions.index') }}">Extensions</a>
            <a href="{{ route('admin.dispositions.index') }}">Dispositions</a>
            <a href="{{ route('admin.analytics.index') }}">Analytics</a>
        @endif
        <a href="{{ route('agent.console') }}">Console</a>
        <a href="{{ route('agent.call-history') }}">Call History</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </nav>
    @endauth
    <main>
        @if(session('success'))
            <div>{{ session('success') }}</div>
        @endif
        @yield('content')
    </main>
</body>
</html>
