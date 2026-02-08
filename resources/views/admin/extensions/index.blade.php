@extends('layouts.app')
@section('title', 'Extensions')
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<span>Extensions</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <div>
            <h1 class="page-title">Extensions</h1>
            <p class="page-description">Manage SIP extensions</p>
        </div>
        <a href="{{ route('admin.extensions.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Create Extension
        </a>
    </div>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Extension #</th>
                    <th>Display Name</th>
                    <th>PBX Connection</th>
                    <th>Assigned User</th>
                    <th>Active</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($extensions as $ext)
                <tr>
                    <td><strong>{{ $ext->extension_number }}</strong></td>
                    <td>{{ $ext->display_name ?? '-' }}</td>
                    <td>{{ $ext->pbxConnection->name ?? '-' }}</td>
                    <td>{{ $ext->user->name ?? 'Unassigned' }}</td>
                    <td>
                        @if($ext->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-secondary">Inactive</span>
                        @endif
                    </td>
                    <td>
                        @if($ext->is_registered)
                            <span class="badge badge-primary">Registered</span>
                        @else
                            <span class="badge badge-secondary">Unregistered</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.extensions.show', $ext) }}" class="action-btn" title="View">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </a>
                            <a href="{{ route('admin.extensions.edit', $ext) }}" class="action-btn" title="Edit">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                            </a>
                            <form method="POST" action="{{ route('admin.extensions.destroy', $ext) }}" id="delete-ext-{{ $ext->id }}" style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                            <button type="button" class="action-btn action-btn-danger" title="Delete" onclick="confirmDelete('delete-ext-{{ $ext->id }}', 'Are you sure you want to delete this extension?')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                            </div>
                            <h3 class="empty-state-title">No extensions found</h3>
                            <p class="empty-state-description">Create your first extension to get started.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($extensions->hasPages())
    <div class="card-footer">
        <div class="pagination">{{ $extensions->links() }}</div>
    </div>
    @endif
</div>
@endsection
