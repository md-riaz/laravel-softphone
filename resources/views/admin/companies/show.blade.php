@extends('layouts.app')
@section('title', 'Company: ' . $company->name)
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.companies.index') }}">Companies</a></li>
    <li class="breadcrumb-item active">{{ $company->name }}</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>{{ $company->name }}</h1>
    <div>
        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-outline-primary me-1"><i class="bi bi-pencil me-1"></i> Edit</a>
        <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" class="d-inline" onsubmit="return confirm('Delete this company?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete</button>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4">
        <div class="card form-card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Details</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Name</dt>
                    <dd>{{ $company->name }}</dd>
                    <dt class="text-muted small">Domain</dt>
                    <dd>{{ $company->domain ?? '-' }}</dd>
                    <dt class="text-muted small">Status</dt>
                    <dd>
                        @if($company->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </dd>
                    <dt class="text-muted small">Created</dt>
                    <dd class="mb-0">{{ $company->created_at->format('M d, Y H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card table-card mb-3">
            <div class="card-header"><i class="bi bi-hdd-network-fill me-2"></i>PBX Connections ({{ $company->pbxConnections->count() }})</div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Host</th>
                            <th>Port</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($company->pbxConnections as $pbx)
                        <tr>
                            <td><a href="{{ route('admin.pbx-connections.show', $pbx) }}">{{ $pbx->name }}</a></td>
                            <td>{{ $pbx->host }}</td>
                            <td>{{ $pbx->port }}</td>
                            <td>
                                @if($pbx->is_active)
                                    <span class="badge badge-active">Active</span>
                                @else
                                    <span class="badge badge-inactive">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No PBX connections</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card table-card">
            <div class="card-header"><i class="bi bi-people-fill me-2"></i>Users ({{ $company->users->count() }})</div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($company->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-{{ $user->isAdmin() ? 'primary' : 'success' }}">{{ ucfirst($user->role) }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">No users</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
