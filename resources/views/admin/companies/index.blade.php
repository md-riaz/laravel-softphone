@extends('layouts.app')
@section('title', 'Companies')
@section('content')
<h1>Companies</h1>
<a href="{{ route('admin.companies.create') }}">Create Company</a>
<table>
    <thead><tr><th>Name</th><th>Domain</th><th>Users</th><th>PBX</th><th>Active</th><th>Actions</th></tr></thead>
    <tbody>
    @foreach($companies as $company)
        <tr>
            <td>{{ $company->name }}</td>
            <td>{{ $company->domain }}</td>
            <td>{{ $company->users_count }}</td>
            <td>{{ $company->pbx_connections_count }}</td>
            <td>{{ $company->is_active ? 'Yes' : 'No' }}</td>
            <td>
                <a href="{{ route('admin.companies.show', $company) }}">View</a>
                <a href="{{ route('admin.companies.edit', $company) }}">Edit</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $companies->links() }}
@endsection
