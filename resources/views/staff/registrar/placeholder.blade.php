@extends('layouts.portal')
@section('title', $title)

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">{{ $title }}</h4>
      @if(!empty($hint))
        <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
          <i class="ri-information-line me-1"></i> {{ $hint }}
        </div>
      @endif
    </div>

    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('staff.registrar.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3">
        <i class="ri-arrow-left-line me-1"></i> Back
      </a>
    </div>
  </div>

  <div class="zmc-card">
    <div class="d-flex align-items-start gap-3">
      <div class="icon-box text-primary"><i class="ri-tools-line"></i></div>
      <div>
        <div class="fw-bold mb-1">Page ready for wiring</div>
        <div class="text-muted">
          This page is linked from the Registrar sidebar. If you want it fully functional,
          tell me which data source/table and actions should live here, and I will wire it end-to-end.
        </div>
      </div>
    </div>
  </div>

</div>
@endsection
