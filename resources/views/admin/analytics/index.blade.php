@extends('layouts.app')
@section('title', 'Analytics')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span>Analytics</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Call Analytics</h1>
            <p class="page-description">Overview of call metrics and trends</p>
        </div>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Total Calls</span>
            <span class="stat-value">{{ $analytics->sum('total_calls') }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-info">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" /></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Inbound</span>
            <span class="stat-value">{{ $analytics->sum('inbound_calls') }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Outbound</span>
            <span class="stat-value">{{ $analytics->sum('outbound_calls') }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Answered</span>
            <span class="stat-value">{{ $analytics->sum('answered_calls') }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-danger">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div class="stat-content">
            <span class="stat-label">Missed</span>
            <span class="stat-value">{{ $analytics->sum('missed_calls') }}</span>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 1.5rem;">
    <div class="card-header">
        <h3 class="card-title">Analytics Data</h3>
    </div>
    <div class="table-container">
        <table class="table">
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
                @forelse($analytics as $a)
                <tr>
                    <td>{{ $a->date->format('M d, Y') }}</td>
                    <td>{{ $a->company->name ?? '-' }}</td>
                    <td><strong>{{ $a->total_calls }}</strong></td>
                    <td>{{ $a->inbound_calls }}</td>
                    <td>{{ $a->outbound_calls }}</td>
                    <td>{{ $a->answered_calls }}</td>
                    <td>{{ $a->missed_calls }}</td>
                    <td>{{ floor($a->avg_duration / 60) }}m {{ $a->avg_duration % 60 }}s</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                            </div>
                            <h3 class="empty-state-title">No analytics data found</h3>
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
@endsection
