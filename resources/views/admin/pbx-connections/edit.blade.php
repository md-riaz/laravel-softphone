@extends('layouts.app')
@section('title', 'Edit PBX Connection')
@section('content')
<h1>Edit {{ $pbxConnection->name }}</h1>
<form method="POST" action="{{ route('admin.pbx-connections.update', $pbxConnection) }}">
    @csrf @method('PUT')
    <div><label>Company ID</label><input type="number" name="company_id" value="{{ $pbxConnection->company_id }}" required></div>
    <div><label>Name</label><input type="text" name="name" value="{{ $pbxConnection->name }}" required></div>
    <div><label>Host</label><input type="text" name="host" value="{{ $pbxConnection->host }}" required></div>
    <div><label>Port</label><input type="number" name="port" value="{{ $pbxConnection->port }}" required></div>
    <div><label>WSS URL</label><input type="text" name="wss_url" value="{{ $pbxConnection->wss_url }}" required></div>
    <button type="submit">Update</button>
</form>
@endsection
