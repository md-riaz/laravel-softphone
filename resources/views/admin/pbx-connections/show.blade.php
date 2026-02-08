@extends('layouts.app')
@section('title', 'PBX Connection Details')
@section('content')
<h1>{{ $pbxConnection->name }}</h1>
<p>Host: {{ $pbxConnection->host }}:{{ $pbxConnection->port }}</p>
<p>WSS: {{ $pbxConnection->wss_url }}</p>
<p>Company: {{ $pbxConnection->company->name ?? 'N/A' }}</p>
@endsection
