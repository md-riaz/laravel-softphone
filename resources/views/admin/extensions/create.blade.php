@extends('layouts.app')
@section('title', 'Create Extension')
@section('content')
<h1>Create Extension</h1>
<form method="POST" action="{{ route('admin.extensions.store') }}">
    @csrf
    <div><label>PBX Connection ID</label><input type="number" name="pbx_connection_id" required></div>
    <div><label>Extension Number</label><input type="text" name="extension_number" required></div>
    <div><label>Password</label><input type="text" name="password" required></div>
    <div><label>Display Name</label><input type="text" name="display_name"></div>
    <div><label>User ID</label><input type="number" name="user_id"></div>
    <button type="submit">Create</button>
</form>
@endsection
