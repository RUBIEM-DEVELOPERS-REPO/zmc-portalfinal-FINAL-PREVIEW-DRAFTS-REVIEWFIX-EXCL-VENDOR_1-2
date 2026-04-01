@extends('layouts.staff')

@section('title', 'Pending Batch Payments')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h4 class="fw-bold"><i class="ri-stack-line me-2 text-primary"></i>Pending Batch Payments</h4>
        <p class="text-muted">Review and verify bulk payments from Media Houses.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th>Batch Reference</th>
                    <th>Media House</th>
                    <th>Amount</th>
                    <th>Staff Count</th>
                    <th>Submitted</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($batches as $batch)
                <tr>
                    <td>
                        <span class="fw-bold">{{ $batch->reference }}</span>
                        @if($batch->proof_path)
                        <div class="mt-1">
                            <a href="{{ Storage::url($batch->proof_path) }}" target="_blank" class="badge bg-soft-info text-info text-decoration-none">
                                <i class="ri-file-search-line me-1"></i> View Proof
                            </a>
                        </div>
                        @endif
                    </td>
                    <td>{{ $batch->mediaHouse->name }}</td>
                    <td class="fw-bold">{{ number_format($batch->amount, 2) }} USD</td>
                    <td>{{ count($batch->metadata['journalist_ids'] ?? []) }}</td>
                    <td>{{ $batch->updated_at->format('d M Y, H:i') }}</td>
                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-2">
                            <form action="{{ route('staff.accounts.batches.approve', $batch) }}" method="POST" onsubmit="return confirm('Approve this batch payment? This will notify all journalists.')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success px-3">
                                    <i class="ri-check-line me-1"></i> Approve
                                </button>
                            </form>
                            
                            <button class="btn btn-sm btn-outline-danger px-3" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $batch->id }}">
                                <i class="ri-close-line me-1"></i> Reject
                            </button>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $batch->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <form action="{{ route('staff.accounts.batches.reject', $batch) }}" method="POST">
                                    @csrf
                                    <div class="modal-content text-start">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Batch {{ $batch->reference }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Reason for Rejection</label>
                                                <textarea name="reason" class="form-control" rows="3" required placeholder="Describe why this proof was rejected..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Submit Rejection</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">No pending batches found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($batches->hasPages())
    <div class="card-footer bg-white">
        {{ $batches->links() }}
    </div>
    @endif
</div>
@endsection
