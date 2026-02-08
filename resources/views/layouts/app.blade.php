<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Softphone') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @include('partials.shadcn-theme')
    @include('partials.styles')
    @stack('styles')
</head>
<body>
    @auth
    <div class="dashboard-layout">
        @include('partials.sidebar')
        <div class="main-content">
            @include('partials.topbar')
            <main class="page-content">
                @include('partials.flash-messages')
                @yield('content')
            </main>
        </div>
    </div>
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
    @include('partials.modal')
    @else
    <div>
        @yield('content')
    </div>
    @endauth
    @include('partials.scripts')
    @stack('scripts')
</body>
</html>
