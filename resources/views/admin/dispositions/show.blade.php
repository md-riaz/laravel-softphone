@extends('layouts.app')
@section('title', 'Disposition: ' . $disposition->name)
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.dispositions.index') }}">Dispositions</a></li>
    <li class="breadcrumb-item active">{{ $disposition->name }}</li>
</ol>
@endsection
@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <h1>{{ $disposition->name }}</h1>
    <div>
        <a href="{{ route('admin.dispositions.edit', $disposition) }}" class="btn btn-outline-primary me-1"><i class="bi bi-pencil me-1"></i> Edit</a>
        <form method="POST" action="{{ route('admin.dispositions.destroy', $disposition) }}" class="d-inline" onsubmit="return confirm('Are you sure?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete</button>
        </form>
        <a href="{{ route('admin.dispositions.index') }}" class="btn btn-outline-secondary ms-1"><i class="bi bi-arrow-left me-1"></i> Back</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card form-card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Details</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Name</dt>
                    <dd>{{ $disposition->name }}</dd>
                    <dt class="text-muted small">Company</dt>
                    <dd>{{ $disposition->company->name ?? '-' }}</dd>
                    <dt class="text-muted small">Color</dt>
                    <dd>
                        <span class="d-inline-block rounded-circle me-2" style="width: 16px; height: 16px; background-color: {{ preg_match('/^#[0-9a-fA-F]{3,8}$/', $disposition->color) ? $disposition->color : '#6c757d' }}; vertical-align: middle;"></span>
                        {{ $disposition->color }}
                    </dd>
                    <dt class="text-muted small">Status</dt>
                    <dd class="mb-0">
                        @if($disposition->is_active)
                            <span class="badge badge-active">Active</span>
                        @else
                            <span class="badge badge-inactive">Inactive</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
