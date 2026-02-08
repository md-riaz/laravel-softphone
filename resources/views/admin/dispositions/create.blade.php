@extends('layouts.app')
@section('title', 'Create Disposition')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<a href="{{ route('admin.dispositions.index') }}">Dispositions</a>
<span class="breadcrumb-separator">/</span>
<span>Create</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title">Create Disposition</h1>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Disposition Details</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.dispositions.store') }}">
            @csrf
            <div class="form-group">
                <label for="company_id" class="form-label">Company <span style="color:var(--destructive);">*</span></label>
                <select class="form-select @error('company_id') is-invalid @enderror" id="company_id" name="company_id" required>
                    <option value="">Select Company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('company_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="name" class="form-label">Name <span style="color:var(--destructive);">*</span></label>
                <input type="text" class="form-input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <label for="color" class="form-label">Color</label>
                <input type="color" class="form-input @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', '#6c757d') }}" style="height:40px;padding:4px;">
                @error('color')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="form-group">
                <div class="checkbox-item">
                    <input type="checkbox" class="checkbox-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="form-label" style="margin-bottom:0;">Active</label>
                </div>
            </div>
            <div class="action-buttons">
                <button type="submit" class="btn btn-primary">Create Disposition</button>
                <a href="{{ route('admin.dispositions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
