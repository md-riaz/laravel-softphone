@extends('layouts.app')
@section('title', 'Edit PBX Connection')
@section('breadcrumb')
<ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.pbx-connections.index') }}">PBX Connections</a></li>
    <li class="breadcrumb-item active">Edit</li>
</ol>
@endsection
@section('content')
<div class="page-header">
    <h1>Edit: {{ $pbxConnection->name }}</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card form-card">
            <div class="card-header"><i class="bi bi-hdd-network-fill me-2"></i>Connection Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.pbx-connections.update', $pbxConnection) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="company_id" class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                        <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $pbxConnection->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Connection Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $pbxConnection->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="host" class="form-label fw-semibold">Host <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('host') is-invalid @enderror" id="host" name="host" value="{{ old('host', $pbxConnection->host) }}" required>
                            @error('host')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="port" class="form-label fw-semibold">Port <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('port') is-invalid @enderror" id="port" name="port" value="{{ old('port', $pbxConnection->port) }}" required>
                            @error('port')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="wss_url" class="form-label fw-semibold">WebSocket URL</label>
                        <input type="text" class="form-control @error('wss_url') is-invalid @enderror" id="wss_url" name="wss_url" value="{{ old('wss_url', $pbxConnection->wss_url) }}">
                        @error('wss_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <hr>
                    <h6 class="fw-semibold mb-3">ICE Server Configuration</h6>
                    <div class="mb-3">
                        <label for="stun_server" class="form-label fw-semibold">STUN Server</label>
                        <input type="text" class="form-control @error('stun_server') is-invalid @enderror" id="stun_server" name="stun_server" value="{{ old('stun_server', $pbxConnection->stun_server) }}">
                        @error('stun_server')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="turn_server" class="form-label fw-semibold">TURN Server</label>
                        <input type="text" class="form-control @error('turn_server') is-invalid @enderror" id="turn_server" name="turn_server" value="{{ old('turn_server', $pbxConnection->turn_server) }}">
                        @error('turn_server')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="turn_username" class="form-label fw-semibold">TURN Username</label>
                            <input type="text" class="form-control @error('turn_username') is-invalid @enderror" id="turn_username" name="turn_username" value="{{ old('turn_username', $pbxConnection->turn_username) }}">
                            @error('turn_username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="turn_password" class="form-label fw-semibold">TURN Password</label>
                            <input type="password" class="form-control @error('turn_password') is-invalid @enderror" id="turn_password" name="turn_password" placeholder="Leave blank to keep current">
                            @error('turn_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3 form-check form-switch">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $pbxConnection->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Active</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Update Connection</button>
                        <a href="{{ route('admin.pbx-connections.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
