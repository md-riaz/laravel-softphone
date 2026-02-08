@extends('layouts.app')
@section('title', 'Analytics')
@section('content')
<h1>Call Analytics</h1>
<table>
    <thead><tr><th>Company</th><th>Date</th><th>Total</th><th>Inbound</th><th>Outbound</th><th>Answered</th><th>Missed</th><th>Avg Duration</th></tr></thead>
    <tbody>
    @foreach($analytics as $a)
        <tr>
            <td>{{ $a->company->name ?? 'N/A' }}</td>
            <td>{{ $a->date->toDateString() }}</td>
            <td>{{ $a->total_calls }}</td>
            <td>{{ $a->inbound_calls }}</td>
            <td>{{ $a->outbound_calls }}</td>
            <td>{{ $a->answered_calls }}</td>
            <td>{{ $a->missed_calls }}</td>
            <td>{{ $a->avg_duration }}s</td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $analytics->links() }}
@endsection
