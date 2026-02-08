@extends('layouts.app')
@section('title', 'Extension: ' . $extension->extension_number)
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.extensions.index') }}">Extensions</a></li>
    <li class="breadcrumb-item active">{{ $extension->extension_number }}</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Extension {{ $extension->extension_number }}</h1>
    <div>
        <a href="{{ route('admin.extensions.edit', $extension) }}" class="btn btn-outline-primary me-1"><i class="bi bi-pencil me-1"></i> Edit</a>
        <form method="POST" action="{{ route('admin.extensions.destroy', $extension) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete</button>
        </form>
        <a href="{{ route('admin.extensions.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card form-card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Details</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Extension Number</dt>
                    <dd>{{ $extension->extension_number }}</dd>
                    <dt class="text-muted small">Display Name</dt>
                    <dd>{{ $extension->display_name ?? '-' }}</dd>
                    <dt class="text-muted small">PBX Connection</dt>
                    <dd>{{ $extension->pbxConnection->name ?? '-' }}</dd>
                    <dt class="text-muted small">Assigned User</dt>
                    <dd>{{ $extension->user->name ?? 'Unassigned' }}</dd>
                    <dt class="text-muted small">Status</dt>
                    <dd>
                        @if($extension->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </dd>
                    <dt class="text-muted small">Registration</dt>
                    <dd class="mb-0">
                        @if($extension->is_registered)
                            <span class="badge badge-active">Registered</span>
                        @else
                            <span class="badge badge-inactive">Unregistered</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card table-card">
            <div class="card-header"><i class="bi bi-telephone-fill me-2"></i>Recent Calls ({{ $extension->calls->count() }})</div>
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
                        @forelse($extension->calls->take(10) as $call)
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
                            <td>
                                @if($call->status === 'answered')
                                    <span class="badge badge-active">Answered</span>
                                @else
                                    <span class="badge badge-inactive">{{ ucfirst($call->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $call->duration ? floor($call->duration / 60) . 'm ' . ($call->duration % 60) . 's' : '-' }}</td>
                            <td>{{ $call->started_at?->format('M d, Y H:i') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No calls found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
