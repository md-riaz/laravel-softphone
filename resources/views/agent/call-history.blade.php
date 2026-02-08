@extends('layouts.app')
@section('title', 'Call History')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span>Call History</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Call History</h1>
            <p class="page-description">View your past calls and details</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Direction</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Extension</th>
                    <th>Status</th>
                    <th>Duration</th>
                    <th>Date</th>
                    <th>Dispositions</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($calls as $call)
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
                    <td>{{ $call->extension->extension_number ?? '-' }}</td>
                    <td>
                        @if($call->status === 'answered')
                            <span class="badge badge-success">Answered</span>
                        @elseif($call->status === 'missed')
                            <span class="badge badge-danger">Missed</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($call->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $call->duration ? floor($call->duration / 60) . 'm ' . ($call->duration % 60) . 's' : '-' }}</td>
                    <td>{{ $call->started_at?->format('M d, Y H:i') ?? '-' }}</td>
                    <td>
                        @forelse($call->dispositions as $disposition)
                            <span class="badge" style="background-color: {{ preg_match('/^#[0-9a-fA-F]{3,8}$/', $disposition->color) ? $disposition->color : '#6c757d' }}; color: #fff;">{{ $disposition->name }}</span>
                        @empty
                            <span style="color:var(--muted-foreground);">-</span>
                        @endforelse
                    </td>
                    <td>
                        @if($call->notes->isNotEmpty())
                            <span title="{{ $call->notes->first()->content }}">{{ Str::limit($call->notes->first()->content, 30) }}</span>
                        @else
                            <span style="color:var(--muted-foreground);">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="empty-state-title">No calls found</h3>
                            <p class="empty-state-description">Your call history will appear here.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($calls->hasPages())
    <div class="card-footer">
        <div class="pagination">{{ $calls->links() }}</div>
    </div>
    @endif
</div>
@endsection
