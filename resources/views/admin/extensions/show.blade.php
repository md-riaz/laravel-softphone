@extends('layouts.app')
@section('title', 'Extension: ' . $extension->extension_number)
@section('breadcrumb')
<a href="{{ route('dashboard') }}">Dashboard</a>
<span class="breadcrumb-separator">/</span>
<a href="{{ route('admin.extensions.index') }}">Extensions</a>
<span class="breadcrumb-separator">/</span>
<span>{{ $extension->extension_number }}</span>
@endsection
@section('content')
<div class="page-header">
    <div class="page-header-row">
        <h1 class="page-title">Extension {{ $extension->extension_number }}</h1>
        <div class="action-buttons">
            <a href="{{ route('admin.extensions.edit', $extension) }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                Edit
            </a>
            <form method="POST" action="{{ route('admin.extensions.destroy', $extension) }}" id="delete-ext-{{ $extension->id }}" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
            <button type="button" class="btn btn-danger" onclick="confirmDelete('delete-ext-{{ $extension->id }}', 'Are you sure you want to delete this extension?')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;margin-right:4px;vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                Delete
            </button>
            <a href="{{ route('admin.extensions.index') }}" class="btn btn-ghost">Back</a>
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
                <label class="form-label">Extension Number</label>
                <p>{{ $extension->extension_number }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Display Name</label>
                <p>{{ $extension->display_name ?? '-' }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">PBX Connection</label>
                <p>{{ $extension->pbxConnection->name ?? '-' }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Assigned User</label>
                <p>{{ $extension->user->name ?? 'Unassigned' }}</p>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <p>
                    @if($extension->is_active)
                        <span class="badge badge-success">Active</span>
                    @else
                        <span class="badge badge-secondary">Inactive</span>
                    @endif
                </p>
            </div>
            <div class="form-group">
                <label class="form-label">Registration</label>
                <p>
                    @if($extension->is_registered)
                        <span class="badge badge-primary">Registered</span>
                    @else
                        <span class="badge badge-secondary">Unregistered</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Calls ({{ $extension->calls->count() }})</h3>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Direction</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($extension->calls->take(10) as $call)
                    <tr>
                        <td>
                            @if($call->direction === 'inbound')
                                <span class="badge badge-info">Inbound</span>
                            @else
                                <span class="badge badge-primary">Outbound</span>
                            @endif
                        </td>
                        <td>{{ $call->caller_number }}</td>
                        <td>{{ $call->callee_number }}</td>
                        <td>
                            @if($call->status === 'answered')
                                <span class="badge badge-success">Answered</span>
                            @else
                                <span class="badge badge-secondary">{{ ucfirst($call->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $call->duration ? floor($call->duration / 60) . 'm ' . ($call->duration % 60) . 's' : '-' }}</td>
                        <td>{{ $call->started_at?->format('M d, Y H:i') ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <h3 class="empty-state-title">No calls found</h3>
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
