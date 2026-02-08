@extends('layouts.app')
@section('title', 'PBX Connections')
@section('content')
<h1>PBX Connections</h1>
<a href="{{ route('admin.pbx-connections.create') }}">Create Connection</a>
<table>
    <thead><tr><th>Name</th><th>Host</th><th>Port</th><th>Company</th><th>Extensions</th><th>Actions</th></tr></thead>
    <tbody>
    @foreach($connections as $conn)
        <tr>
            <td>{{ $conn->name }}</td>
            <td>{{ $conn->host }}</td>
            <td>{{ $conn->port }}</td>
            <td>{{ $conn->company->name ?? 'N/A' }}</td>
            <td>{{ $conn->extensions_count }}</td>
            <td>
                <a href="{{ route('admin.pbx-connections.show', $conn) }}">View</a>
                <a href="{{ route('admin.pbx-connections.edit', $conn) }}">Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $connections->links() }}
@endsection
