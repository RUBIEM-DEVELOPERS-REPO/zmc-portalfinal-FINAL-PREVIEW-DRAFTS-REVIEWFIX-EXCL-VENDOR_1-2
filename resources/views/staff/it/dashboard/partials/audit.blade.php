<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold m-0">Master Activity Audit Trail</h6>
            <p class="text-slate-600 small m-0 fw-medium">Immutable records of all administrative and user actions</p>
        </div>
        <button class="btn btn-slate-100 border text-slate-600 btn-sm rounded-pill px-3 fw-bold">
            <i class="ri-file-excel-2-line me-1"></i> Export CSV
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="bg-slate-50 border-top border-bottom">
                    <tr>
                        <th class="ps-4 small text-slate-700 uppercase fw-bold py-3">Timestamp</th>
                        <th class="small text-slate-700 uppercase fw-bold py-3">Actor</th>
                        <th class="small text-slate-700 uppercase fw-bold py-3">Action</th>
                        <th class="small text-slate-700 uppercase fw-bold py-3">Details</th>
                        <th class="small text-slate-700 uppercase fw-bold py-3 text-end pe-4">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4 small text-slate-700 fw-bold">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-xs rounded-circle bg-slate-100 text-slate-400 d-flex align-items-center justify-content-center fw-bold" style="width:24px; height:24px; font-size: var(--font-size-sm);">
                                    {{ substr($log->user_name ?? 'S', 0, 1) }}
                                </div>
                                <span class="small fw-bold text-slate-700">{{ $log->user_name ?: 'System' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-slate-100 text-slate-600 border px-2 py-1 rounded small">{{ strtoupper($log->action) }}</span>
                        </td>
                        <td class="small text-slate-600">
                            <div class="fw-bold text-slate-900">{{ $log->description }}</div>
                            @if($log->entity_reference)
                                <span class="text-primary smaller">#{{ $log->entity_reference }}</span>
                            @endif
                            @if($log->old_values || $log->new_values)
                                <div class="mt-1 smaller opacity-75">
                                    @if($log->old_values)<span class="text-danger">From: {{ json_encode($log->old_values) }}</span>@endif
                                    @if($log->new_values)<span class="text-success ml-2">To: {{ json_encode($log->new_values) }}</span>@endif
                                </div>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <code class="small text-slate-600 fw-bold">{{ $log->ip_address }}</code>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-5 text-center text-slate-700 font-weight-bold">No activity logs recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top">
            {{ $logs->links() }}
        </div>
    </div>
</div>
