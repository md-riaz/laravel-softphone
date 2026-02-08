@extends('layouts.app')
@section('title', 'Edit Extension')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.extensions.index') }}">Extensions</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>
@endsection
@section('content')
<div class="page-header">
    <h1>Edit Extension: {{ $extension->extension_number }}</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card form-card">
            <div class="card-header"><i class="bi bi-telephone me-2"></i>Extension Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.extensions.update', $extension) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="pbx_connection_id" class="form-label fw-semibold">PBX Connection <span class="text-danger">*</span></label>
                        <select class="form-select @error('pbx_connection_id') is-invalid @enderror" id="pbx_connection_id" name="pbx_connection_id" required>
                            <option value="">Select PBX Connection</option>
                            @foreach($pbxConnections as $pbx)
                                <option value="{{ $pbx->id }}" {{ old('pbx_connection_id', $extension->pbx_connection_id) == $pbx->id ? 'selected' : '' }}>{{ $pbx->name }} ({{ $pbx->company->name }})</option>
                            @endforeach
                        </select>
                        @error('pbx_connection_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="extension_number" class="form-label fw-semibold">Extension Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('extension_number') is-invalid @enderror" id="extension_number" name="extension_number" value="{{ old('extension_number', $extension->extension_number) }}" required>
                        @error('extension_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="display_name" class="form-label fw-semibold">Display Name</label>
                        <input type="text" class="form-control @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name', $extension->display_name) }}">
                        @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="user_id" class="form-label fw-semibold">Assigned User</label>
                        <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id', $extension->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $extension->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Active</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update Extension</button>
                        <a href="{{ route('admin.extensions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
