<div class="row g-4">
    <div class="col-xl-9">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 p-4">
                <h6 class="fw-bold m-0">Recent Transactions & Reconciliation</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle m-0">
                        <thead class="bg-slate-50 border-top border-bottom border-slate-100">
                            <tr>
                                <th class="ps-4 small text-slate-700 uppercase fw-bold py-3">Ref</th>
                                <th class="small text-slate-700 uppercase fw-bold py-3">Method</th>
                                <th class="small text-slate-700 uppercase fw-bold py-3">Amount</th>
                                <th class="small text-slate-700 uppercase fw-bold py-3">Status</th>
                                <th class="small text-slate-700 uppercase fw-bold py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Mocking for UI demonstration since table structure varies -->
                            <tr>
                                <td class="ps-4 fw-bold">TX-90210</td>
                                <td class="small">Paynow (EcoCash)</td>
                                <td class="fw-bold text-slate-900">$20.00</td>
                                <td><span class="badge bg-success-subtle text-success rounded-pill px-3">Success</span></td>
                                <td class="text-end pe-4">
                                     <button class="btn btn-sm btn-slate-100 border rounded-pill px-3">Audit</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white border-start border-4 border-warning">
            <h6 class="fw-bold mb-1">Manual Reconciliation</h6>
            <h3 class="fw-bold mb-1 text-slate-900">{{ $reconciliation['pending_proofs'] }}</h3>
            <p class="text-slate-700 small m-0 fw-bold">Bank transfers awaiting IT validation</p>
            <hr>
            <form action="{{ route('staff.it.payments.process_queue') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-warning w-100 rounded-pill fw-bold btn-sm text-dark">Process Queue</button>
            </form>
        </div>
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-slate-900 text-white">
            <h6 class="fw-bold mb-3">Revenue Oversight</h6>
            <div class="mb-3">
                <div class="text-white-50 small mb-1">Total Digital Revenue</div>
                <div class="fs-4 fw-bold text-success">${{ number_format($reconciliation['total_revenue'], 2) }}</div>
            </div>
            <div class="progress bg-white-10" style="height: 4px;">
                <div class="progress-bar bg-success" style="width: 75%"></div>
            </div>
        </div>
    </div>
</div>
