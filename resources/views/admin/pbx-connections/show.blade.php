@extends('layouts.app')
@section('title', 'PBX: ' . $pbxConnection->name)
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.pbx-connections.index') }}">PBX Connections</a></li>
    <li class="breadcrumb-item active">{{ $pbxConnection->name }}</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>{{ $pbxConnection->name }}</h1>
    <div>
        <a href="{{ route('admin.pbx-connections.edit', $pbxConnection) }}" class="btn btn-outline-primary me-1"><i class="bi bi-pencil me-1"></i> Edit</a>
        <form method="POST" action="{{ route('admin.pbx-connections.destroy', $pbxConnection) }}" class="d-inline" onsubmit="return confirm('Delete this PBX connection?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete</button>
        </form>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card form-card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Details</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Company</dt>
                    <dd>{{ $pbxConnection->company->name ?? 'N/A' }}</dd>
                    <dt class="text-muted small">Host</dt>
                    <dd><code>{{ $pbxConnection->host }}</code></dd>
                    <dt class="text-muted small">Port</dt>
                    <dd>{{ $pbxConnection->port }}</dd>
                    <dt class="text-muted small">WebSocket URL</dt>
                    <dd><code>{{ $pbxConnection->wss_url ?? '-' }}</code></dd>
                    <dt class="text-muted small">STUN Server</dt>
                    <dd>{{ $pbxConnection->stun_server ?? '-' }}</dd>
                    <dt class="text-muted small">TURN Server</dt>
                    <dd>{{ $pbxConnection->turn_server ?? '-' }}</dd>
                    <dt class="text-muted small">Status</dt>
                    <dd>
                        @if($pbxConnection->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </dd>
                    <dt class="text-muted small">Created</dt>
                    <dd class="mb-0">{{ $pbxConnection->created_at->format('M d, Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card table-card">
            <div class="card-header"><i class="bi bi-phone-fill me-2"></i>Extensions ({{ $pbxConnection->extensions->count() }})</div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Extension</th>
                            <th>Display Name</th>
                            <th>Assigned To</th>
                            <th>Active</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pbxConnection->extensions as $ext)
                        <tr>
                            <td><a href="{{ route('admin.extensions.show', $ext) }}">{{ $ext->extension_number }}</a></td>
                            <td>{{ $ext->display_name ?? '-' }}</td>
                            <td>{{ $ext->user->name ?? '-' }}</td>
                            <td>
                                @if($ext->is_active)
                                    <span class="badge badge-active">Yes</span>
                                @else
                                    <span class="badge badge-inactive">No</span>
                                @endif
                            </td>
                            <td>
                                @if($ext->is_registered)
                                    <span class="badge bg-primary">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No extensions</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
