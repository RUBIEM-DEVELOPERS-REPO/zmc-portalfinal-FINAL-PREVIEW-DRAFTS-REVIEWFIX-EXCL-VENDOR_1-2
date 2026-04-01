@extends('layouts.portal')
@section('title', $title ?? 'Accreditation Officer')

@section('content')
  <div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
      <div>
        <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">{{ $title ?? 'Page' }}</h4>
        @if(!empty($subtitle))
          <div class="text-muted mt-1" style="font-size: var(--font-size-base);">
            <i class="ri-information-line me-1"></i>{{ $subtitle }}
          </div>
        @endif
      </div>
      <div>
        <a href="{{ url()->previous() }}" class="btn btn-white border shadow-sm btn-sm px-3">
          <i class="ri-arrow-left-line me-1"></i> Back
        </a>
      </div>
    </div>

    <div class="alert alert-info">
      <i class="ri-tools-line me-1"></i>
      This module is enabled in the menu. Functional workflows will appear here as records/data are populated.
    </div>
  </div>
@endsection
