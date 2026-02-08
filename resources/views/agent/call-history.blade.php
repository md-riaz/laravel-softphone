@extends('layouts.app')
@section('title', 'Call History')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Call History</li>
</ol>
@endsection
@section('content')
<div class="page-header">
    <h1>Call History</h1>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover">
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
                            <span class="badge badge-inbound">Inbound</span>
                        @else
                            <span class="badge badge-outbound">Outbound</span>
                        @endif
                    </td>
                    <td>{{ $call->caller_number }}</td>
                    <td>{{ $call->callee_number }}</td>
                    <td>{{ $call->extension->extension_number ?? '-' }}</td>
                    <td>
                        @if($call->status === 'answered')
                            <span class="badge badge-active">Answered</span>
                        @elseif($call->status === 'missed')
                            <span class="badge badge-inactive">Missed</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($call->status) }}</span>
                        @endif
                    </td>
                    <td>{{ $call->duration ? floor($call->duration / 60) . 'm ' . ($call->duration % 60) . 's' : '-' }}</td>
                    <td>{{ $call->started_at?->format('M d, Y H:i') ?? '-' }}</td>
                    <td>
                        @forelse($call->dispositions as $disposition)
                            <span class="badge rounded-pill" style="background-color: {{ preg_match('/^#[0-9a-fA-F]{3,8}$/', $disposition->color) ? $disposition->color : '#6c757d' }}; color: #fff;">{{ $disposition->name }}</span>
                        @empty
                            <span class="text-muted">-</span>
                        @endforelse
                    </td>
                    <td>
                        @if($call->notes->isNotEmpty())
                            <span title="{{ $call->notes->first()->content }}">{{ Str::limit($call->notes->first()->content, 30) }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No calls found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($calls->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $calls->links() }}
    </div>
    @endif
</div>
@endsection
