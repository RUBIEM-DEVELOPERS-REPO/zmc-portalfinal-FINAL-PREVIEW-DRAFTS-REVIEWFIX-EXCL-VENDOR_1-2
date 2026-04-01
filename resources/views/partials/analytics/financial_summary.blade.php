<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="fw-bold m-0 text-slate-700">Financial Health Overview</h6>
                <div class="badge bg-success-subtle text-success rounded-pill px-3">Live</div>
            </div>
            <div class="d-flex gap-3 flex-wrap mb-4">
                <div class="flex-grow-1 p-3 rounded-4 bg-slate-50 border border-slate-100 text-center">
                    <div class="text-slate-600 small fw-bold mb-1 uppercase letter-spacing-1">Confirmed Revenue</div>
                    <div class="fw-bold fs-3 text-slate-900">${{ number_format($paymentSummary['Paid'] ?? 0, 2) }}</div>
                </div>
                <div class="flex-grow-1 p-3 rounded-4 bg-danger-subtle border border-danger-subtle text-center">
                    <div class="text-danger small fw-bold mb-1 uppercase letter-spacing-1">Failed Payments</div>
                    <div class="fw-bold fs-3 text-danger">${{ number_format($paymentSummary['Failed'] ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="progress bg-slate-100" style="height: 6px; border-radius: 10px;">
                @php
                    $total = ($paymentSummary['Paid'] ?? 0) + ($paymentSummary['Failed'] ?? 0);
                    $percent = $total > 0 ? (($paymentSummary['Paid'] ?? 0) / $total) * 100 : 0;
                @endphp
                <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
            </div>
            <div class="text-muted smaller mt-2 fw-bold text-center">Payment Collection Success Rate: {{ round($percent, 1) }}%</div>
        </div>
    </div>
    
    <div class="col-md-6">
         <div class="card border-0 shadow-sm rounded-4 h-100 p-4 bg-slate-900 text-white">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="ri-shield-flash-line text-info"></i> Compliance & Risk Oversight
            </h6>
            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center p-3 rounded-4 bg-white-10 border border-white-10">
                    <div>
                        <div class="text-white-50 small fw-bold">Manual Reconciliations</div>
                        <div class="fs-4 fw-bold">{{ $paymentReconciliation['pending_proofs'] ?? 0 }}</div>
                    </div>
                    <div class="icon-box bg-warning-subtle text-warning p-2 rounded-3"><i class="ri-time-line fs-4"></i></div>
                </div>
                <div class="d-flex justify-content-between align-items-center p-3 rounded-4 bg-white-10 border border-white-10">
                    <div>
                        <div class="text-white-50 small fw-bold">Waiver Requests</div>
                        <div class="fs-4 fw-bold">{{ $kpis['pending_waivers'] ?? 0 }}</div>
                    </div>
                    <div class="icon-box bg-info-subtle text-info p-2 rounded-3"><i class="ri-hand-coin-line fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>
