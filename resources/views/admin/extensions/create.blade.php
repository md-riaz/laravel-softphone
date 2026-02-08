@extends('layouts.app')
@section('title', 'Create Extension')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<a href="{{ route('admin.extensions.index') }}">Extensions</a>
<span class="breadcrumb-separator">/</span>
<span>Create</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title">Create Extension</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Extension Details</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.extensions.store') }}">
            @csrf
            <div class="form-group">
                <label for="pbx_connection_id" class="form-label">PBX Connection <span style="color:var(--destructive);">*</span></label>
                <select class="form-select @error('pbx_connection_id') is-invalid @enderror" id="pbx_connection_id" name="pbx_connection_id" required>
                    <option value="">Select PBX Connection</option>
                    @foreach($pbxConnections as $pbx)
                        <option value="{{ $pbx->id }}" {{ old('pbx_connection_id') == $pbx->id ? 'selected' : '' }}>{{ $pbx->name }} ({{ $pbx->company->name }})</option>
                    @endforeach
                </select>
                @error('pbx_connection_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="extension_number" class="form-label">Extension Number <span style="color:var(--destructive);">*</span></label>
                <input type="text" class="form-input @error('extension_number') is-invalid @enderror" id="extension_number" name="extension_number" value="{{ old('extension_number') }}" required>
                @error('extension_number')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="password" class="form-label">Password <span style="color:var(--destructive);">*</span></label>
                <input type="password" class="form-input @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="display_name" class="form-label">Display Name</label>
                <input type="text" class="form-input @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name') }}">
                @error('display_name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="user_id" class="form-label">Assigned User</label>
                <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                    <option value="">Unassigned</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                    @endforeach
                </select>
                @error('user_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <div class="checkbox-item">
                    <input type="checkbox" class="checkbox-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="form-label" style="margin-bottom:0;">Active</label>
                </div>
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">Create Extension</button>
                <a href="{{ route('admin.extensions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
