@extends('layouts.app')
@section('title', 'Create Company')
@section('content')
<h1>Create Company</h1>
<form method="POST" action="{{ route('admin.companies.store') }}">
    @csrf
    <div><label>Name</label><input type="text" name="name" required></div>
    <div><label>Domain</label><input type="text" name="domain"></div>
    <button type="submit">Create</button>
</form>
@endsection
