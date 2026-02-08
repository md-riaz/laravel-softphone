@extends('layouts.app')
@section('title', 'Agent Dashboard')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Agent Dashboard</h1>
    <span class="text-muted small">{{ now()->format('l, F j, Y') }}</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_calls_today'] }}</div>
                    <div class="stat-label">Calls Today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-phone-fill"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['active_extensions'] }}</div>
                    <div class="stat-label">Active Extensions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-headset"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['my_extensions']->count() }}</div>
                    <div class="stat-label">My Extensions</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <a href="{{ route('agent.call-history') }}" class="text-decoration-none">
                        <div class="stat-label">View History</div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card table-card">
            <div class="card-header"><i class="bi bi-phone-fill me-2"></i>My Extensions</div>
            <div class="card-body p-3">
                @forelse($stats['my_extensions'] as $ext)
                <div class="extension-card {{ $ext->is_active ? 'active' : '' }} {{ $ext->is_registered ? 'registered' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $ext->display_name ?? $ext->extension_number }}</strong>
                            <div class="text-muted small">{{ $ext->pbxConnection->name ?? '' }} &middot; Ext {{ $ext->extension_number }}</div>
                        </div>
                        <div>
                            @if($ext->is_registered)
                                <span class="badge bg-primary">Registered</span>
                            @elseif($ext->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center py-3 mb-0">No extensions assigned</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Recent Calls</span>
                <a href="{{ route('agent.call-history') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
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
                            <td><span class="badge badge-{{ $call->direction }}">{{ ucfirst($call->direction) }}</span></td>
                            <td>{{ $call->caller_number }}</td>
                            <td>{{ $call->callee_number }}</td>
                            <td><span class="badge badge-{{ $call->status }}">{{ ucfirst($call->status) }}</span></td>
                            <td>{{ $call->duration ? gmdate('H:i:s', $call->duration) : '-' }}</td>
                            <td>{{ $call->started_at ? $call->started_at->format('M d, H:i') : '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">No calls yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
