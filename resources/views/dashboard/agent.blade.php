@extends('layouts.app')
@section('title', 'Agent Dashboard')
@section('breadcrumb')
<span>Dashboard</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Agent Dashboard</h1>
            <p class="page-description">{{ now()->format('l, F j, Y') }}</p>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Calls Today</span>
            <span class="stat-value">{{ $stats['total_calls_today'] }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Active Extensions</span>
            <span class="stat-value">{{ $stats['active_extensions'] }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">My Extensions</span>
            <span class="stat-value">{{ $stats['my_extensions']->count() }}</span>
        </div>
    </div>
</div>

<div class="grid-2" style="margin-top: 1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">My Extensions</h3>
        </div>
        <div class="card-body">
            @forelse($stats['my_extensions'] as $ext)
            <div class="extension-card {{ $ext->is_active ? 'ext-active' : '' }} {{ $ext->is_registered ? 'ext-registered' : '' }}">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>{{ $ext->display_name ?? $ext->extension_number }}</strong>
                        <div style="font-size: 0.75rem; color: var(--muted-foreground);">{{ $ext->pbxConnection->name ?? '' }} &middot; Ext {{ $ext->extension_number }}</div>
                    </div>
                    <div>
                        @if($ext->is_registered)
                            <span class="badge badge-primary">Registered</span>
                        @elseif($ext->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-state-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                </div>
                <h3 class="empty-state-title">No extensions assigned</h3>
            </div>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Calls</h3>
            <a href="{{ route('agent.call-history') }}" class="btn btn-sm btn-secondary">View All</a>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Direction</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stats['recent_calls'] as $call)
                    <tr>
                        <td>
                            @if($call->direction === 'inbound')
                                <span class="badge badge-info">Inbound</span>
                            @else
                                <span class="badge badge-primary">Outbound</span>
                            @endif
                        </td>
                        <td>{{ $call->caller_number }}</td>
                        <td>{{ $call->callee_number }}</td>
                        <td>
                            @if($call->status === 'answered')
                                <span class="badge badge-success">Answered</span>
                            @elseif($call->status === 'missed')
                                <span class="badge badge-danger">Missed</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($call->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $call->duration ? gmdate('H:i:s', $call->duration) : '-' }}</td>
                        <td>{{ $call->started_at ? $call->started_at->format('M d, H:i') : '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <h3 class="empty-state-title">No calls yet</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
