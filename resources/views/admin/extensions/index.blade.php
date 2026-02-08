@extends('layouts.app')
@section('title', 'Extensions')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Extensions</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Extensions</h1>
    <a href="{{ route('admin.extensions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Create Extension
    </a>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Extension #</th>
                    <th>Display Name</th>
                    <th>PBX Connection</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Registration</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($extensions as $ext)
                <tr>
                    <td class="fw-semibold">{{ $ext->extension_number }}</td>
                    <td>{{ $ext->display_name ?? '-' }}</td>
                    <td>{{ $ext->pbxConnection->name ?? '-' }}</td>
                    <td>{{ $ext->user->name ?? 'Unassigned' }}</td>
                    <td>
                        @if($ext->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($ext->is_registered)
                            <span class="badge badge-active">Registered</span>
                        @else
                            <span class="badge badge-inactive">Unregistered</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.extensions.show', $ext) }}" class="btn btn-sm btn-outline-info me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.extensions.edit', $ext) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.extensions.destroy', $ext) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No extensions found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($extensions->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $extensions->links() }}
    </div>
    @endif
</div>
@endsection
