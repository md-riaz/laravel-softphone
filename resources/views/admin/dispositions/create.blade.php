@extends('layouts.app')
@section('title', 'Create Disposition')
@section('content')
<h1>Create Disposition</h1>
<form method="POST" action="{{ route('admin.dispositions.store') }}">
    @csrf
    <div><label>Company ID</label><input type="number" name="company_id" required></div>
    <div><label>Name</label><input type="text" name="name" required></div>
    <div><label>Color</label><input type="color" name="color" value="#6c757d"></div>
    <button type="submit">Create</button>
</form>
@endsection
