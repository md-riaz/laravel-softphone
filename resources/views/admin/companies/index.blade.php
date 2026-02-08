@extends('layouts.app')
@section('title', 'Companies')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Companies</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Companies</h1>
    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Company
    </a>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Domain</th>
                    <th>Users</th>
                    <th>PBX Connections</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                <tr>
                    <td><a href="{{ route('admin.companies.show', $company) }}" class="fw-semibold text-decoration-none">{{ $company->name }}</a></td>
                    <td>{{ $company->domain ?? '-' }}</td>
                    <td>{{ $company->users_count }}</td>
                    <td>{{ $company->pbx_connections_count }}</td>
                    <td>
                        @if($company->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>{{ $company->created_at->format('M d, Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.companies.destroy', $company) }}" class="d-inline" onsubmit="return confirm('Delete this company?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No companies found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $companies->links() }}
    </div>
    @endif
</div>
@endsection
