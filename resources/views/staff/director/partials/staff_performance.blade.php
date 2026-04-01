<div class="zmc-card bg-white shadow-sm border-0 rounded-4 p-4">
    <h5 class="fw-bold mb-4">Operational Throughput (MonthlyProcessed)</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="ps-3 py-3 text-muted small fw-bold text-uppercase">Staff Member</th>
                    <th class="py-3 text-muted small fw-bold text-uppercase">Role / Designation</th>
                    <th class="py-3 text-center text-muted small fw-bold text-uppercase">Applications Processed</th>
                    <th class="pe-3 py-3 text-end text-muted small fw-bold text-uppercase">Operational Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staffPerformance as $staff)
                    <tr>
                        <td class="ps-3 py-3 d-flex align-items-center gap-3">
                            <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:32px; height:32px; font-size: var(--font-size-sm);">
                                {{ substr($staff->name, 0, 2) }}
                            </div>
                            <span class="fw-bold">{{ $staff->name }}</span>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-light text-dark text-capitalize">{{ str_replace('_', ' ', $staff->getRoleNames()->first() ?? 'Staff') }}</span>
                        </td>
                        <td class="py-3 text-center">
                            <div class="h5 fw-black mb-0">{{ number_format($staff->processed_applications_count) }}</div>
                        </td>
                        <td class="pe-3 py-3 text-end">
                            @if($staff->processed_applications_count > 50)
                                <span class="text-success small fw-bold"><i class="ri-checkbox-circle-fill me-1"></i> High Performance</span>
                            @elseif($staff->processed_applications_count > 10)
                                <span class="text-primary small fw-bold"><i class="ri-checkbox-circle-line me-1"></i> Active</span>
                            @else
                                <span class="text-muted small fw-bold">Low Volume</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
