@extends('layouts.app')
@section('title', 'Extension Details')
@section('content')
<h1>Extension {{ $extension->extension_number }}</h1>
<p>Display Name: {{ $extension->display_name ?? 'N/A' }}</p>
<p>PBX: {{ $extension->pbxConnection->name ?? 'N/A' }}</p>
<p>User: {{ $extension->user->name ?? 'Unassigned' }}</p>
<p>Active: {{ $extension->is_active ? 'Yes' : 'No' }}</p>
@endsection
