@extends('layouts.portal')
@section('title', 'PayNow Settings')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color:#1e293b;">PayNow Settings</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);">View-only integration metadata (masked where necessary).</div>
    </div>
    <a href="{{ url()->current() }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-refresh-line me-1"></i> Refresh</a>
  </div>

  <div class="zmc-card">
    <div class="fw-bold mb-2"><i class="ri-settings-3-line me-2" style="color:var(--zmc-accent)"></i> Gateway status</div>
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead><tr><th>Key</th><th>Value</th></tr></thead>
        <tbody>
          @foreach($settings as $k => $v)
            @php
              $val = $v;
              if (is_string($val) && str_contains(strtolower($k), 'id') && strlen($val) > 6) {
                $val = substr($val, 0, 3) . str_repeat('*', max(strlen($val) - 6, 0)) . substr($val, -3);
              }
            @endphp
            <tr>
              <td class="text-muted small" style="width:320px;">{{ $k }}</td>
              <td class="small">{{ $val ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="text-muted small mt-3">
      Error logs: wire this page to your webhook controller logs.
    </div>
  </div>
</div>
@endsection
