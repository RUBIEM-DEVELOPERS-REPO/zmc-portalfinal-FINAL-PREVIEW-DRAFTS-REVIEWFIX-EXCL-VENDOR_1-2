@extends('layouts.portal')
@section('title', 'System Audit Trail')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="mb-4">
        <h4 class="fw-bold m-0">System Audit Trail</h4>
        <div class="text-muted small">Chronological chain of custody and action logs.</div>
    </div>

    <form action="{{ url()->current() }}" method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Search by action, user, or meta data..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </div>
        </div>
    </form>

    <div class="zmc-card p-0 shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="bg-light">
                    <tr class="text-muted text-uppercase">
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Action</th>
                        <th>Entity</th>
                        <th>Details</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-muted">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                            <td class="fw-bold">{{ $log->user?->name ?? 'System' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $log->user_role }}</span></td>
                            <td><span class="fw-bold text-primary">{{ strtoupper(str_replace('_',' ', $log->action)) }}</span></td>
                            <td>
                                @if($log->entity)
                                    <span class="text-muted">{{ class_basename($log->entity_type) }} #{{ $log->entity_id }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($log->meta)
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ json_encode($log->meta) }}">
                                        {{ json_encode($log->meta) }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-muted">{{ $log->ip }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">No audit logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        {{ $logs->links() }}
    </div>
</div>
@endsection
