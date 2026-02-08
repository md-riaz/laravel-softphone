@extends('layouts.app')
@section('title', 'Call History')
@section('content')
<h1>Call History</h1>
<table>
    <thead><tr><th>Direction</th><th>Caller</th><th>Callee</th><th>Status</th><th>Duration</th><th>Started</th></tr></thead>
    <tbody>
    @foreach($calls as $call)
        <tr>
            <td>{{ $call->direction }}</td>
            <td>{{ $call->caller_number }}</td>
            <td>{{ $call->callee_number }}</td>
            <td>{{ $call->status }}</td>
            <td>{{ $call->duration ?? '-' }}s</td>
            <td>{{ $call->started_at?->toDateTimeString() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $calls->links() }}
@endsection
