@extends('layouts.app')
@section('title', 'Reports')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span>Reports</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Call Reports</h1>
            <p class="page-description">Detailed call reporting data</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Reports Data</h3>
    </div>
    <div class="table-container">
        <table class="table">
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
                    <td><strong>{{ $a->total_calls }}</strong></td>
                    <td>{{ $a->answered_calls }}</td>
                    <td>{{ $a->missed_calls }}</td>
                    <td>{{ floor($a->total_duration / 60) }}m {{ $a->total_duration % 60 }}s</td>
                    <td>{{ floor($a->total_talk_time / 60) }}m {{ $a->total_talk_time % 60 }}s</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                            </div>
                            <h3 class="empty-state-title">No report data found</h3>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($analytics->hasPages())
    <div class="card-footer">
        <div class="pagination">{{ $analytics->links() }}</div>
    </div>
    @endif
</div>

<div class="card" style="margin-top: 1rem;">
    <div class="card-body" style="display:flex;align-items:center;gap:0.5rem;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:var(--primary);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
        <span style="color:var(--muted-foreground);font-size:0.875rem;">CSV export is available via CLI command: <code>php artisan calls:export</code></span>
    </div>
</div>
@endsection
