<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold m-0">Draft Management</h6>
            <p class="text-slate-600 small m-0 fw-medium">View user draft applications</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0">
                <thead class="bg-slate-50 border-top border-bottom border-slate-100">
                    <tr>
                        <th class="ps-4 small text-slate-700 uppercase fw-bold py-3">Reference</th>
                        <th class="small text-slate-700 uppercase fw-bold py-3">User</th>
                        <th class="small text-slate-700 uppercase fw-bold py-3">Last Activity</th>
                        <th class="small text-slate-700 uppercase fw-bold py-3">Completion</th>
                        <th class="small text-slate-700 uppercase fw-bold py-3 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="border-0">
                    @forelse($drafts as $draft)
                    <tr>
                        <td class="ps-4">
                             <div class="fw-bold text-slate-900 small">{{ $draft->reference }}</div>
                        </td>
                        <td>
                            <div class="small fw-medium text-slate-700">{{ $draft->applicant?->name }}</div>
                            <div class="text-slate-600 fw-medium" style="font-size: 11px;">{{ $draft->applicant?->email }}</div>
                        </td>
                        <td>
                             <div class="small text-slate-600">{{ $draft->updated_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2" style="max-width: 100px;">
                                <div class="progress flex-grow-1" style="height: 4px;">
                                    <div class="progress-bar bg-warning" style="width: 45%"></div>
                                </div>
                                <span class="small fw-bold text-slate-700">45%</span>
                            </div>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('staff.it.application.overview', $draft) }}" class="btn btn-sm btn-slate-100 border text-slate-600 py-1 px-3 rounded-pill fw-bold">Review</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center text-slate-700 font-weight-bold">No active drafts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-top border-slate-100">
            {{ $drafts->links() }}
        </div>
    </div>
</div>
