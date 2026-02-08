@extends('layouts.app')
@section('title', 'PBX: ' . $pbxConnection->name)
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<a href="{{ route('admin.pbx-connections.index') }}">PBX Connections</a>
<span class="breadcrumb-separator">/</span>
<span>{{ $pbxConnection->name }}</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title">{{ $pbxConnection->name }}</h1>
        <div class="action-buttons">
            <a href="{{ route('admin.pbx-connections.edit', $pbxConnection) }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                Edit
            </a>
            <form method="POST" action="{{ route('admin.pbx-connections.destroy', $pbxConnection) }}" id="delete-pbx-{{ $pbxConnection->id }}" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
            <button type="button" class="btn btn-danger" onclick="confirmDelete('delete-pbx-{{ $pbxConnection->id }}', 'Are you sure you want to delete this PBX connection?')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                Delete
            </button>
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Details</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Company</label>
                <p>{{ $pbxConnection->company->name ?? 'N/A' }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Host</label>
                <p>{{ $pbxConnection->host }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Port</label>
                <p>{{ $pbxConnection->port }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">WebSocket URL</label>
                <p>{{ $pbxConnection->wss_url ?? '-' }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">STUN Server</label>
                <p>{{ $pbxConnection->stun_server ?? '-' }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">TURN Server</label>
                <p>{{ $pbxConnection->turn_server ?? '-' }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <p>
                    @if($pbxConnection->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Inactive</span>
                    @endif
                </p>
            </div>
            <div class="form-group">
                <label class="form-label">Created</label>
                <p>{{ $pbxConnection->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Extensions ({{ $pbxConnection->extensions->count() }})</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Extension</th>
                        <th>Display Name</th>
                        <th>Assigned To</th>
                        <th>Active</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pbxConnection->extensions as $ext)
                    <tr>
                        <td><a href="{{ route('admin.extensions.show', $ext) }}">{{ $ext->extension_number }}</a></td>
                        <td>{{ $ext->display_name ?? '-' }}</td>
                        <td>{{ $ext->user->name ?? '-' }}</td>
                        <td>
                            @if($ext->is_active)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            @if($ext->is_registered)
                                <span class="badge badge-primary">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <h3 class="empty-state-title">No extensions</h3>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
