@extends('layouts.app')
@section('title', 'Dispositions')
@section('content')
<h1>Dispositions</h1>
<a href="{{ route('admin.dispositions.create') }}">Create Disposition</a>
<table>
    <thead><tr><th>Name</th><th>Color</th><th>Company</th><th>Active</th><th>Actions</th></tr></thead>
    <tbody>
    @foreach($dispositions as $d)
        <tr>
            <td>{{ $d->name }}</td>
            <td><span style="color:{{ $d->color }}">{{ $d->color }}</span></td>
            <td>{{ $d->company->name ?? 'N/A' }}</td>
            <td>{{ $d->is_active ? 'Yes' : 'No' }}</td>
            <td>
                <a href="{{ route('admin.dispositions.show', $d) }}">View</a>
                <a href="{{ route('admin.dispositions.edit', $d) }}">Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $dispositions->links() }}
@endsection
