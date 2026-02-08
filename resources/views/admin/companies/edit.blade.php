@extends('layouts.app')
@section('title', 'Edit Company')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<a href="{{ route('admin.companies.index') }}">Companies</a>
<span class="breadcrumb-separator">/</span>
<span>Edit</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title">Edit Company: {{ $company->name }}</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Company Details</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.companies.update', $company) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name" class="form-label">Company Name <span style="color:var(--destructive);">*</span></label>
                <input type="text" class="form-input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $company->name) }}" required>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="domain" class="form-label">Domain</label>
                <input type="text" class="form-input @error('domain') is-invalid @enderror" id="domain" name="domain" value="{{ old('domain', $company->domain) }}" placeholder="example.com">
                @error('domain')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <div class="checkbox-item">
                    <input type="checkbox" class="checkbox-input" id="is_active" name="is_active" value="1" {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="form-label" style="margin-bottom:0;">Active</label>
                </div>
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">Update Company</button>
                <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
