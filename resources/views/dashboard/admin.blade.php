@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<h1>Admin Dashboard</h1>
<div>
    <p>Companies: {{ $stats['total_companies'] }}</p>
    <p>Users: {{ $stats['total_users'] }}</p>
    <p>Extensions: {{ $stats['total_extensions'] }}</p>
    <p>Active Extensions: {{ $stats['active_extensions'] }}</p>
    <p>Calls Today: {{ $stats['total_calls_today'] }}</p>
</div>
<h2>Recent Analytics</h2>
<table>
    <thead><tr><th>Company</th><th>Date</th><th>Total Calls</th><th>Answered</th><th>Missed</th></tr></thead>
    <tbody>
    @foreach($stats['recent_analytics'] as $a)
        <tr>
            <td>{{ $a->company->name ?? 'N/A' }}</td>
            <td>{{ $a->date->toDateString() }}</td>
            <td>{{ $a->total_calls }}</td>
            <td>{{ $a->answered_calls }}</td>
            <td>{{ $a->missed_calls }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
