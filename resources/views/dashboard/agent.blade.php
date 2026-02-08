@extends('layouts.app')
@section('title', 'Agent Dashboard')
@section('content')
<h1>Agent Dashboard</h1>
<div>
    <p>Active Extensions: {{ $stats['active_extensions'] }}</p>
    <p>Calls Today: {{ $stats['total_calls_today'] }}</p>
</div>
<h2>My Extensions</h2>
<ul>
@foreach($stats['my_extensions'] as $ext)
    <li>{{ $ext->extension_number }} - {{ $ext->display_name ?? 'N/A' }} ({{ $ext->is_active ? 'Active' : 'Inactive' }})</li>
@endforeach
</ul>
<h2>Recent Calls</h2>
<table>
    <thead><tr><th>Direction</th><th>Caller</th><th>Callee</th><th>Status</th><th>Started</th></tr></thead>
    <tbody>
    @foreach($stats['recent_calls'] as $call)
        <tr>
            <td>{{ $call->direction }}</td>
            <td>{{ $call->caller_number }}</td>
            <td>{{ $call->callee_number }}</td>
            <td>{{ $call->status }}</td>
            <td>{{ $call->started_at?->toDateTimeString() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
