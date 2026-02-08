@extends('layouts.app')
@section('title', 'Extensions')
@section('content')
<h1>Extensions</h1>
<a href="{{ route('admin.extensions.create') }}">Create Extension</a>
<table>
    <thead><tr><th>Number</th><th>Display Name</th><th>PBX</th><th>User</th><th>Active</th><th>Actions</th></tr></thead>
    <tbody>
    @foreach($extensions as $ext)
        <tr>
            <td>{{ $ext->extension_number }}</td>
            <td>{{ $ext->display_name ?? 'N/A' }}</td>
            <td>{{ $ext->pbxConnection->name ?? 'N/A' }}</td>
            <td>{{ $ext->user->name ?? 'Unassigned' }}</td>
            <td>{{ $ext->is_active ? 'Yes' : 'No' }}</td>
            <td>
                <a href="{{ route('admin.extensions.show', $ext) }}">View</a>
                <a href="{{ route('admin.extensions.edit', $ext) }}">Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $extensions->links() }}
@endsection
