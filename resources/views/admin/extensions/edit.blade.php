@extends('layouts.app')
@section('title', 'Edit Extension')
@section('content')
<h1>Edit Extension {{ $extension->extension_number }}</h1>
<form method="POST" action="{{ route('admin.extensions.update', $extension) }}">
    @csrf @method('PUT')
    <div><label>PBX Connection ID</label><input type="number" name="pbx_connection_id" value="{{ $extension->pbx_connection_id }}" required></div>
    <div><label>Extension Number</label><input type="text" name="extension_number" value="{{ $extension->extension_number }}" required></div>
    <div><label>Password</label><input type="text" name="password" value="{{ $extension->password }}" required></div>
    <div><label>Display Name</label><input type="text" name="display_name" value="{{ $extension->display_name }}"></div>
    <div><label>User ID</label><input type="number" name="user_id" value="{{ $extension->user_id }}"></div>
    <button type="submit">Update</button>
</form>
@endsection
