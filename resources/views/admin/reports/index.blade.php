@extends('layouts.app')
@section('title', 'Reports')
@section('content')
<h1>Call Reports</h1>
<table>
    <thead><tr><th>Company</th><th>Date</th><th>Total</th><th>Answered</th><th>Missed</th><th>Total Duration</th><th>Total Talk Time</th></tr></thead>
    <tbody>
    @foreach($analytics as $a)
        <tr>
            <td>{{ $a->company->name ?? 'N/A' }}</td>
            <td>{{ $a->date->toDateString() }}</td>
            <td>{{ $a->total_calls }}</td>
            <td>{{ $a->answered_calls }}</td>
            <td>{{ $a->missed_calls }}</td>
            <td>{{ $a->total_duration }}s</td>
            <td>{{ $a->total_talk_time }}s</td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $analytics->links() }}
@endsection
