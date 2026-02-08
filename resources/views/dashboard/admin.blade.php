@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Dashboard</h1>
    <span class="text-muted small">{{ now()->format('l, F j, Y') }}</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-building"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_companies'] }}</div>
                    <div class="stat-label">Companies</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Users</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-phone-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['active_extensions'] }} / {{ $stats['total_extensions'] }}</div>
                    <div class="stat-label">Extensions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_calls_today'] }}</div>
                    <div class="stat-label">Calls Today</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-bar-chart-fill me-2"></i>Recent Analytics</span>
        <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Company</th>
                    <th>Total Calls</th>
                    <th>Inbound</th>
                    <th>Outbound</th>
                    <th>Answered</th>
                    <th>Missed</th>
                    <th>Avg Duration</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['recent_analytics'] as $analytic)
                <tr>
                    <td>{{ $analytic->date->format('M d, Y') }}</td>
                    <td>{{ $analytic->company->name ?? 'N/A' }}</td>
                    <td><strong>{{ $analytic->total_calls }}</strong></td>
                    <td><span class="badge badge-inbound">{{ $analytic->inbound_calls }}</span></td>
                    <td><span class="badge badge-outbound">{{ $analytic->outbound_calls }}</span></td>
                    <td><span class="badge badge-answered">{{ $analytic->answered_calls }}</span></td>
                    <td><span class="badge badge-missed">{{ $analytic->missed_calls }}</span></td>
                    <td>{{ $analytic->avg_duration ? gmdate('i:s', (int)$analytic->avg_duration) : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No analytics data yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
