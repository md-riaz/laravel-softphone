@extends('layouts.app')
@section('title', 'Reports')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Reports</li>
</ol>
@endsection
@section('content')
<div class="page-header">
    <h1>Call Reports</h1>
</div>

<div class="card table-card">
    <div class="card-header"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reports Data</div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Company</th>
                    <th>Total Calls</th>
                    <th>Answered</th>
                    <th>Missed</th>
                    <th>Total Duration</th>
                    <th>Total Talk Time</th>
                </tr>
            </thead>
            <tbody>
                @forelse($analytics as $a)
                <tr>
                    <td>{{ $a->date->format('M d, Y') }}</td>
                    <td>{{ $a->company->name ?? '-' }}</td>
                    <td class="fw-semibold">{{ $a->total_calls }}</td>
                    <td>{{ $a->answered_calls }}</td>
                    <td>{{ $a->missed_calls }}</td>
                    <td>{{ floor($a->total_duration / 60) }}m {{ $a->total_duration % 60 }}s</td>
                    <td>{{ floor($a->total_talk_time / 60) }}m {{ $a->total_talk_time % 60 }}s</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No report data found</td></tr>
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

<div class="card form-card mt-3">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <i class="bi bi-info-circle text-primary me-2"></i>
            <span class="text-muted">CSV export is available via CLI command: <code>php artisan calls:export</code></span>
        </div>
    </div>
</div>
@endsection
