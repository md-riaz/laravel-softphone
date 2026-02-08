@extends('layouts.app')
@section('title', 'Dispositions')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Dispositions</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>Dispositions</h1>
    <a href="{{ route('admin.dispositions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Create Disposition
    </a>
</div>

<div class="card table-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dispositions as $d)
                <tr>
                    <td>
                        <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: {{ preg_match('/^#[0-9a-fA-F]{3,8}$/', $d->color) ? $d->color : '#6c757d' }}; vertical-align: middle;"></span>
                        <a href="{{ route('admin.dispositions.show', $d) }}" class="fw-semibold text-decoration-none">{{ $d->name }}</a>
                    </td>
                    <td>{{ $d->company->name ?? '-' }}</td>
                    <td>
                        @if($d->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.dispositions.show', $d) }}" class="btn btn-sm btn-outline-info me-1">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('admin.dispositions.edit', $d) }}" class="btn btn-sm btn-outline-secondary me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.dispositions.destroy', $d) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No dispositions found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($dispositions->hasPages())
    <div class="card-footer bg-white border-top">
        {{ $dispositions->links() }}
    </div>
    @endif
</div>
@endsection
