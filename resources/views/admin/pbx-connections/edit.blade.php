@extends('layouts.app')
@section('title', 'Edit PBX Connection')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<a href="{{ route('admin.pbx-connections.index') }}">PBX Connections</a>
<span class="breadcrumb-separator">/</span>
<span>Edit</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title">Edit: {{ $pbxConnection->name }}</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Connection Details</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pbx-connections.update', $pbxConnection) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="company_id" class="form-label">Company <span style="color:var(--destructive);">*</span></label>
                <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id', $pbxConnection->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('company_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="name" class="form-label">Connection Name <span style="color:var(--destructive);">*</span></label>
                <input type="text" class="form-input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $pbxConnection->name) }}" required>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-row">
                <div class="form-group" style="flex:2;">
                    <label for="host" class="form-label">Host <span style="color:var(--destructive);">*</span></label>
                    <input type="text" class="form-input @error('host') is-invalid @enderror" id="host" name="host" value="{{ old('host', $pbxConnection->host) }}" required>
                    @error('host')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group" style="flex:1;">
                    <label for="port" class="form-label">Port <span style="color:var(--destructive);">*</span></label>
                    <input type="number" class="form-input @error('port') is-invalid @enderror" id="port" name="port" value="{{ old('port', $pbxConnection->port) }}" required>
                    @error('port')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="form-group">
                <label for="wss_url" class="form-label">WebSocket URL</label>
                <input type="text" class="form-input @error('wss_url') is-invalid @enderror" id="wss_url" name="wss_url" value="{{ old('wss_url', $pbxConnection->wss_url) }}">
                @error('wss_url')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="border-top: 1px solid var(--border); margin: 1.5rem 0; padding-top: 1.5rem;">
                <h3 class="card-title" style="margin-bottom: 1rem;">ICE Server Configuration</h3>
            </div>

            <div class="form-group">
                <label for="stun_server" class="form-label">STUN Server</label>
                <input type="text" class="form-input @error('stun_server') is-invalid @enderror" id="stun_server" name="stun_server" value="{{ old('stun_server', $pbxConnection->stun_server) }}">
                @error('stun_server')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="turn_server" class="form-label">TURN Server</label>
                <input type="text" class="form-input @error('turn_server') is-invalid @enderror" id="turn_server" name="turn_server" value="{{ old('turn_server', $pbxConnection->turn_server) }}">
                @error('turn_server')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="turn_username" class="form-label">TURN Username</label>
                    <input type="text" class="form-input @error('turn_username') is-invalid @enderror" id="turn_username" name="turn_username" value="{{ old('turn_username', $pbxConnection->turn_username) }}">
                    @error('turn_username')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label for="turn_password" class="form-label">TURN Password</label>
                    <input type="password" class="form-input @error('turn_password') is-invalid @enderror" id="turn_password" name="turn_password" placeholder="Leave blank to keep current">
                    @error('turn_password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="form-group">
                <div class="checkbox-item">
                    <input type="checkbox" class="checkbox-input" id="is_active" name="is_active" value="1" {{ old('is_active', $pbxConnection->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="form-label" style="margin-bottom:0;">Active</label>
                </div>
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">Update Connection</button>
                <a href="{{ route('admin.pbx-connections.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
