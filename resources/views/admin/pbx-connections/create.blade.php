@extends('layouts.app')
@section('title', 'Create PBX Connection')
@section('content')
<h1>Create PBX Connection</h1>
<form method="POST" action="{{ route('admin.pbx-connections.store') }}">
    @csrf
    <div><label>Company ID</label><input type="number" name="company_id" required></div>
    <div><label>Name</label><input type="text" name="name" required></div>
    <div><label>Host</label><input type="text" name="host" required></div>
    <div><label>Port</label><input type="number" name="port" value="5060" required></div>
    <div><label>WSS URL</label><input type="text" name="wss_url" required></div>
    <button type="submit">Create</button>
</form>
@endsection
