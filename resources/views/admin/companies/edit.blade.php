@extends('layouts.app')
@section('title', 'Edit Company')
@section('content')
<h1>Edit {{ $company->name }}</h1>
<form method="POST" action="{{ route('admin.companies.update', $company) }}">
    @csrf @method('PUT')
    <div><label>Name</label><input type="text" name="name" value="{{ $company->name }}" required></div>
    <div><label>Domain</label><input type="text" name="domain" value="{{ $company->domain }}"></div>
    <button type="submit">Update</button>
</form>
@endsection
