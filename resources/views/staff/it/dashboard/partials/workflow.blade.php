<div class="row g-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 p-4">
                <h6 class="fw-bold m-0">Workflow Logic Settings</h6>
                <p class="text-slate-600 small m-0 fw-medium">Control the state machine and progression rules</p>
            </div>
            <div class="card-body p-4 pt-0">
                <form action="{{ route('staff.it.config.save') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-slate-700">Draft Feature</label>
                                <div class="form-check form-switch p-3 bg-slate-50 rounded-3 border border-slate-100">
                                    <input class="form-check-input" type="checkbox" name="enable_drafts" checked>
                                    <label class="form-check-label small ms-2">Allow users to save incomplete drafts</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-slate-700">Final Review Mode</label>
                                <div class="form-check form-switch p-3 bg-slate-50 rounded-3 border border-slate-100">
                                    <input class="form-check-input" type="checkbox" name="review_before_submit" checked>
                                    <label class="form-check-label small ms-2">Force "Review & Submit" summary page</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-slate-700">Registration Expiry (Days)</label>
                        <input type="number" class="form-control" name="expiry_days" value="365">
                        <div class="form-text small text-slate-600 fw-bold">Standard accreditation validity period from issuance.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-slate-700">Renewal Window Open (Days before expiry)</label>
                        <input type="number" class="form-control" name="renewal_window" value="30">
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Update Workflow Config</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card bg-primary text-white border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="ri-git-branch-line"></i> Pipeline Summary
            </h6>
            <div class="d-flex flex-column gap-2 small">
                <div class="d-flex justify-content-between p-2 rounded-2 bg-white-10">
                    <span>Officer Review</span>
                    <span class="fw-bold">Level 1</span>
                </div>
                <div class="d-flex justify-content-between p-2 rounded-2 bg-white-10">
                    <span>Financial Validation</span>
                    <span class="fw-bold">Level 2</span>
                </div>
                <div class="d-flex justify-content-between p-2 rounded-2 bg-white-10">
                    <span>Registrar Reviews</span>
                    <span class="fw-bold">Level 3</span>
                </div>
                <div class="d-flex justify-content-between p-2 rounded-2 bg-white-10">
                    <span>Final Production</span>
                    <span class="fw-bold">Level 4</span>
                </div>
            </div>
            <hr class="border-white opacity-20 my-4">
            <p class="small text-white-50 m-0 leading-relaxed italic border-start border-3 border-white-20 ps-3">
                Current workflow follows a sequential state machine. Parallel processing is disabled by default.
            </p>
        </div>
    </div>
</div>

<style>
    .bg-white-10 { background: rgba(255,255,255,0.1); }
    .border-white-20 { border-color: rgba(255,255,255,0.2) !important; }
</style>
