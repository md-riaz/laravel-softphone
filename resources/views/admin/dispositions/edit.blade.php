@extends('layouts.app')
@section('title', 'Edit Disposition')
@section('content')
<h1>Edit {{ $disposition->name }}</h1>
<form method="POST" action="{{ route('admin.dispositions.update', $disposition) }}">
    @csrf @method('PUT')
    <div><label>Company ID</label><input type="number" name="company_id" value="{{ $disposition->company_id }}" required></div>
    <div><label>Name</label><input type="text" name="name" value="{{ $disposition->name }}" required></div>
    <div><label>Color</label><input type="color" name="color" value="{{ $disposition->color }}"></div>
    <button type="submit">Update</button>
</form>
@endsection
