@extends('layouts.portal')

@section('title', 'Confirm Batch - Media House Portal')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="mb-4">
    <a href="{{ route('mediahouse.staff.index') }}" class="text-muted text-decoration-none small">
      <i class="ri-arrow-left-line me-1"></i> Back to Staff Management
    </a>
    <h4 class="fw-bold m-0 mt-2" style="font-size:22px; color:#1e293b;">Confirm Batch Selection</h4>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="zmc-card p-0 shadow-sm border-0 mb-4">
        <div class="p-3 border-bottom">
          <h6 class="fw-bold m-0"><i class="ri-user-line me-2" style="color:var(--zmc-accent)"></i>Selected Practitioners</h6>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0 zmc-mini-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Fee</th>
              </tr>
            </thead>
            <tbody>
              @foreach($journalists as $journalist)
                <tr>
                  <td class="fw-bold text-dark">{{ $journalist->name }}</td>
                  <td>{{ $journalist->email }}</td>
                  <td>{{ number_format(20, 2) }} USD</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="zmc-card shadow-sm border-0 sticky-top" style="top: 20px;">
        <h6 class="fw-bold mb-3">Batch Summary</h6>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Practitioners:</span>
          <span>{{ count($journalists) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-4">
          <span class="text-muted">Total Amount:</span>
          <span class="fw-bold text-dark" style="font-size: 1.2rem;">{{ number_format($total, 2) }} USD</span>
        </div>

        <form action="{{ route('mediahouse.batch.store') }}" method="POST">
          @csrf
          @foreach($journalistIds as $id)
            <input type="hidden" name="journalist_ids[]" value="{{ $id }}">
          @endforeach
          <input type="hidden" name="total" value="{{ $total }}">
          
          <button type="submit" class="btn btn-dark w-100 py-3 fw-bold">
            <i class="ri-check-line me-1"></i> Confirm & Start Batch
          </button>
        </form>

        <p class="small text-muted text-center mt-3 mb-0">
          <i class="ri-information-line me-1"></i>
          Clicking confirm will create a batch record and a renewal application for each selected practitioner.
        </p>
      </div>
    </div>
  </div>

</div>
@endsection
