@extends('layouts.app')
@section('title', 'PBX Connections')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">PBX Connections</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>PBX Connections</h1>
    <a href="{{ route('admin.pbx-connections.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Connection
    </a>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Host</th>
                    <th>Port</th>
                    <th>WSS URL</th>
                    <th>Extensions</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($connections as $conn)
                <tr>
                    <td><a href="{{ route('admin.pbx-connections.show', $conn) }}" class="fw-semibold text-decoration-none">{{ $conn->name }}</a></td>
                    <td>{{ $conn->company->name ?? 'N/A' }}</td>
                    <td><code>{{ $conn->host }}</code></td>
                    <td>{{ $conn->port }}</td>
                    <td><code class="small">{{ Str::limit($conn->wss_url, 30) }}</code></td>
                    <td>{{ $conn->extensions_count }}</td>
                    <td>
                        @if($conn->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.pbx-connections.edit', $conn) }}" class="btn btn-sm btn-outline-secondary me-1"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('admin.pbx-connections.destroy', $conn) }}" class="d-inline" onsubmit="return confirm('Delete this PBX connection?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No PBX connections found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($connections->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $connections->links() }}
    </div>
    @endif
</div>
@endsection
