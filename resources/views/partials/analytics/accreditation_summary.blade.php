<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3 h-100 bg-white rounded-4">
        <div class="text-muted small fw-bold text-uppercase mb-1">Total Queue</div>
        <div class="fw-bold fs-3" style="color: #0f172a;">{{ number_format($kpis['total_applications'] ?? 0) }}</div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3 h-100 bg-white rounded-4 border-start border-4 border-primary">
        <div class="text-muted small fw-bold text-uppercase mb-1">Pending Review</div>
        <div class="fw-bold fs-3 text-primary">{{ number_format($kpis['pending_applications'] ?? 0) }}</div>
      </div>
    </div>

    <div class="col-12 col-md-4">
      <div class="zmc-card shadow-sm border-0 p-3 h-100 bg-white rounded-4 border-start border-4 border-warning">
        <div class="text-muted small fw-bold text-uppercase mb-1">Returned for Correction</div>
        <div class="fw-bold fs-3 text-warning">{{ number_format($kpis['rejected_applications'] ?? 0) }}</div>
      </div>
    </div>
</div>
