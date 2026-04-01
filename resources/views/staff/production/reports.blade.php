@extends('layouts.portal')
@section('title', 'Production Reports')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">
        Production Reports
      </h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
        <i class="ri-information-line me-1"></i>
        High-level production metrics for your region. (Export widgets can be added next.)
      </div>
    </div>
    <div class="d-flex align-items-center gap-2">
      <span class="zmc-pill zmc-pill-dark">
        <i class="ri-map-pin-user-line"></i>
        <span>Region: {{ auth()->user()->region ?? 'NOT SET' }}</span>
      </span>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="text-muted small fw-bold">In queue</div>
        <div class="h3 fw-black mb-0">{{ $kpis['in_queue'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="text-muted small fw-bold">Pending printing</div>
        <div class="h3 fw-black mb-0">{{ $kpis['to_print'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="text-muted small fw-bold">Printed</div>
        <div class="h3 fw-black mb-0">{{ $kpis['printed'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-12 col-md-3">
      <div class="zmc-card h-100">
        <div class="text-muted small fw-bold">Issued</div>
        <div class="h3 fw-black mb-0">{{ $kpis['issued'] ?? 0 }}</div>
      </div>
    </div>
  </div>

  <div class="zmc-card">
    <div class="fw-bold mb-2"><i class="ri-file-chart-line me-1" style="color:var(--zmc-accent)"></i> Coming next</div>
    <ul class="text-muted mb-0" style="font-size: var(--font-size-base);">
      <li>Daily / monthly production reports (generated, printed, issued)</li>
      <li>Replacement reports (lost/damaged/correction reprints)</li>
      <li>Batch printing log (batch ID, size, operator, time)</li>
      <li>Export PDF/Excel</li>
    </ul>
  </div>

</div>
@endsection
