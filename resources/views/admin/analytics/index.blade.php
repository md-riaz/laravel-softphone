@extends('layouts.app')
@section('title', 'Analytics')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Analytics</li>
</ol>
@endsection
@section('content')
<div class="page-header">
    <h1>Call Analytics</h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="text-muted small fw-semibold">Total Calls</div>
                <div class="fs-3 fw-bold">{{ $analytics->sum('total_calls') }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="text-muted small fw-semibold">Inbound</div>
                <div class="fs-3 fw-bold text-primary">{{ $analytics->sum('inbound_calls') }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="text-muted small fw-semibold">Outbound</div>
                <div class="fs-3 fw-bold text-info">{{ $analytics->sum('outbound_calls') }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="text-muted small fw-semibold">Answered</div>
                <div class="fs-3 fw-bold text-success">{{ $analytics->sum('answered_calls') }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg">
        <div class="card stat-card">
            <div class="card-body text-center">
                <div class="text-muted small fw-semibold">Missed</div>
                <div class="fs-3 fw-bold text-danger">{{ $analytics->sum('missed_calls') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card table-card">
    <div class="card-header"><i class="bi bi-bar-chart-fill me-2"></i>Analytics Data</div>
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
                    <th>Avg Talk Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($analytics as $a)
                <tr>
                    <td>{{ $a->date->format('M d, Y') }}</td>
                    <td>{{ $a->company->name ?? '-' }}</td>
                    <td class="fw-semibold">{{ $a->total_calls }}</td>
                    <td>{{ $a->inbound_calls }}</td>
                    <td>{{ $a->outbound_calls }}</td>
                    <td>{{ $a->answered_calls }}</td>
                    <td>{{ $a->missed_calls }}</td>
                    <td>{{ floor($a->avg_duration / 60) }}m {{ $a->avg_duration % 60 }}s</td>
                    <td>{{ floor(($a->avg_talk_time ?? 0) / 60) }}m {{ ($a->avg_talk_time ?? 0) % 60 }}s</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No analytics data found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($analytics->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $analytics->links() }}
    </div>
    @endif
</div>
@endsection
