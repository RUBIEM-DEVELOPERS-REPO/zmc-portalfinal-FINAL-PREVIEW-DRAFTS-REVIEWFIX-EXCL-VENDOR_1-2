@extends('layouts.portal')
@section('title', $title ?? 'Documents')

@section('content')
@php
  // Defensive defaults: prevent 500s if a controller forgets to pass variables.
  $title = $title ?? 'Documents';
  $documents = $documents ?? ($docs ?? collect());
@endphp
<div class="zmc-dashboard-wrapper" style="font-family: var(--font-primary); color: var(--zmc-text-dark);">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size: var(--font-size-2xl); color: var(--zmc-text-dark);">{{ $title }}</h4>
      <div class="text-muted mt-1" style="font-size: var(--font-size-base);"><i class="ri-information-line me-1"></i>Preview and verify uploaded documents.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('staff.officer.dashboard') }}" class="btn btn-white border shadow-sm btn-sm px-3"><i class="ri-dashboard-3-line me-1"></i>Dashboard</a>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Application</th>
              <th>Applicant</th>
              <th>Doc Type</th>
              <th>Status</th>
              <th>Uploaded</th>
              <th class="text-end">File</th>
            </tr>
          </thead>
          <tbody>
          @forelse($documents as $doc)
            <tr>
              <td>{{ $doc->application->reference ?? ('#'.$doc->application_id) }}</td>
              <td>{{ $doc->application->applicant->name ?? '—' }}</td>
              <td class="fw-semibold">{{ $doc->doc_type }}</td>
              <td>
                <span class="badge bg-secondary">{{ $doc->verification_status ?? ($doc->status ?? 'pending') }}</span>
              </td>
              <td>{{ optional($doc->created_at)->format('Y-m-d H:i') }}</td>
              <td class="text-end">
                @if(!empty($doc->file_path))
                  <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ asset('storage/'.ltrim($doc->file_path,'/')) }}">
                    <i class="ri-eye-line me-1"></i>Preview
                  </a>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No documents found.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>

      @if(method_exists($documents, 'links'))
        <div class="mt-3">{{ $documents->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection
