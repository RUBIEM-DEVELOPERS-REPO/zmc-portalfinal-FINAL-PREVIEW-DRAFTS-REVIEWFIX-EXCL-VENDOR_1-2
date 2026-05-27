<div class="row g-4">
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white shadow-sm border border-slate-50">
            <h6 class="fw-bold mb-3">Daily System Report</h6>
            <p class="text-muted small">Consolidated summary of applications and errors.</p>
            <a href="{{ route('staff.it.reports.generate', 'pdf') }}" class="btn btn-primary rounded-pill w-100 fw-bold mt-auto py-2">Generate PDF</a>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white shadow-sm border border-slate-50">
            <h6 class="fw-bold mb-3">Security & Access Report</h6>
            <p class="text-muted small">Failed logins, blocked IPs, and admin audit logs.</p>
            <a href="{{ route('staff.it.reports.generate', 'csv') }}" class="btn btn-slate-100 border text-slate-600 rounded-pill w-100 fw-bold mt-auto py-2">Download CSV</a>
        </div>
    </div>
</div>
