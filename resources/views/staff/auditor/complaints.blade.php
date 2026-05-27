@extends('layouts.staff')
@section('title','Auditor - Complaints & Appeals Oversight')
@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">
  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">
        <i class="ri-chat-1-line me-2" style="color:var(--zmc-accent);"></i>Complaints & Appeals Oversight
      </h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        Read-only view of all complaints and appeals for auditing purposes.
      </div>
    </div>
    <a href="{{ route('staff.auditor.complaints') }}" class="btn btn-light">
      <i class="ri-refresh-line me-1"></i>Refresh
    </a>
  </div>

  {{-- Statistics Cards with Doughnut Charts --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
      <div class="zmc-card text-center">
        <div class="mb-2" style="width:100px;height:100px;margin:0 auto;">
          <canvas id="totalChart"></canvas>
        </div>
        <h3 class="fw-bold m-0" style="color:var(--zmc-primary);">{{ number_format($stats['total']) }}</h3>
        <p class="text-muted small m-0">Total Records</p>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="zmc-card text-center">
        <div class="mb-2" style="width:100px;height:100px;margin:0 auto;">
          <canvas id="pendingChart"></canvas>
        </div>
        <h3 class="fw-bold m-0 text-warning">{{ number_format($stats['pending']) }}</h3>
        <p class="text-muted small m-0">Pending</p>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="zmc-card text-center">
        <div class="mb-2" style="width:100px;height:100px;margin:0 auto;">
          <canvas id="resolvedChart"></canvas>
        </div>
        <h3 class="fw-bold m-0 text-success">{{ number_format($stats['resolved']) }}</h3>
        <p class="text-muted small m-0">Resolved</p>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="zmc-card text-center">
        <div class="mb-2" style="width:100px;height:100px;margin:0 auto;">
          <canvas id="typeChart"></canvas>
        </div>
        <div class="d-flex justify-content-center gap-3 small">
          <span><span class="badge bg-primary">{{ $stats['complaints'] }}</span> Complaints</span>
          <span><span class="badge bg-info">{{ $stats['appeals'] }}</span> Appeals</span>
        </div>
      </div>
    </div>
  </div>

  {{-- Complaints Table --}}
  <div class="zmc-card">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 zmc-table">
        <thead>
          <tr>
            <th>Type</th>
            <th>Name</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Submitted</th>
            <th class="text-end">Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse($complaints as $c)
            <tr>
              <td>
                <span class="badge rounded-pill bg-{{ $c->type === 'complaint' ? 'primary' : 'info' }}">
                  {{ ucfirst($c->type) }}
                </span>
              </td>
              <td>
                <div class="fw-bold">{{ $c->name }}</div>
                <div class="small text-muted">{{ $c->email }}</div>
              </td>
              <td>{{ Str::limit($c->subject, 50) }}</td>
              <td>
                <span class="badge rounded-pill bg-{{ $c->status === 'resolved' ? 'success' : 'warning' }}">
                  {{ ucfirst($c->status) }}
                </span>
              </td>
              <td class="small">{{ $c->created_at->format('d M Y H:i') }}</td>
              <td class="text-end">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewComplaint{{ $c->id }}">
                  <i class="ri-eye-line"></i> View
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">No complaints or appeals found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $complaints->links() }}</div>
  </div>
</div>

{{-- View Modals --}}
@foreach($complaints as $c)
  <div class="modal fade" id="viewComplaint{{ $c->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header zmc-modal-header">
          <div class="zmc-modal-title">View {{ ucfirst($c->type) }}</div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label zmc-lbl">Type</label>
              <p class="form-control-plaintext">
                <span class="badge bg-{{ $c->type === 'complaint' ? 'primary' : 'info' }}">{{ ucfirst($c->type) }}</span>
              </p>
            </div>
            <div class="col-md-6">
              <label class="form-label zmc-lbl">Status</label>
              <p class="form-control-plaintext">
                <span class="badge bg-{{ $c->status === 'resolved' ? 'success' : 'warning' }}">{{ ucfirst($c->status) }}</span>
              </p>
            </div>
            <div class="col-md-6">
              <label class="form-label zmc-lbl">Name</label>
              <p class="form-control-plaintext fw-bold">{{ $c->name }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label zmc-lbl">Email</label>
              <p class="form-control-plaintext">{{ $c->email }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label zmc-lbl">Phone</label>
              <p class="form-control-plaintext">{{ $c->phone ?? '—' }}</p>
            </div>
            <div class="col-md-6">
              <label class="form-label zmc-lbl">Submitted</label>
              <p class="form-control-plaintext">{{ $c->created_at->format('d M Y H:i') }}</p>
            </div>
            <div class="col-12">
              <label class="form-label zmc-lbl">Subject</label>
              <p class="form-control-plaintext fw-bold">{{ $c->subject }}</p>
            </div>
            <div class="col-12">
              <label class="form-label zmc-lbl">Message</label>
              <div class="p-3 bg-light rounded">{{ $c->message }}</div>
            </div>
            @if($c->resolution_notes)
              <div class="col-12">
                <label class="form-label zmc-lbl">Resolution Notes</label>
                <div class="p-3 bg-success-subtle rounded">{{ $c->resolution_notes }}</div>
              </div>
            @endif
            @if($c->resolved_at)
              <div class="col-12">
                <label class="form-label zmc-lbl">Resolved At</label>
                <p class="form-control-plaintext">{{ $c->resolved_at->format('d M Y H:i') }}</p>
              </div>
            @endif
          </div>
        </div>
        <div class="modal-footer zmc-modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endforeach

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Doughnut charts for statistics
  const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    cutout: '65%',
    plugins: {
      legend: { display: false },
      tooltip: { enabled: false }
    }
  };

  // Total Records Chart (filled ring)
  new Chart(document.getElementById('totalChart'), {
    type: 'doughnut',
    data: {
      labels: ['Total'],
      datasets: [{
        data: [{{ $stats['total'] }}, {{ $stats['total'] > 0 ? 0 : 1 }}],
        backgroundColor: ['#1e293b', '#e2e8f0'],
        borderWidth: 0
      }]
    },
    options: chartOptions
  });

  // Pending Chart
  new Chart(document.getElementById('pendingChart'), {
    type: 'doughnut',
    data: {
      labels: ['Pending', 'Others'],
      datasets: [{
        data: [{{ $stats['pending'] }}, {{ $stats['total'] - $stats['pending'] }}],
        backgroundColor: ['#f59e0b', '#e2e8f0'],
        borderWidth: 0
      }]
    },
    options: chartOptions
  });

  // Resolved Chart
  new Chart(document.getElementById('resolvedChart'), {
    type: 'doughnut',
    data: {
      labels: ['Resolved', 'Others'],
      datasets: [{
        data: [{{ $stats['resolved'] }}, {{ $stats['total'] - $stats['resolved'] }}],
        backgroundColor: ['#10b981', '#e2e8f0'],
        borderWidth: 0
      }]
    },
    options: chartOptions
  });

  // Type Chart (Complaints vs Appeals)
  new Chart(document.getElementById('typeChart'), {
    type: 'doughnut',
    data: {
      labels: ['Complaints', 'Appeals'],
      datasets: [{
        data: [{{ $stats['complaints'] }}, {{ $stats['appeals'] }}],
        backgroundColor: ['#3b82f6', '#06b6d4'],
        borderWidth: 0
      }]
    },
    options: {
      ...chartOptions,
      plugins: {
        legend: {
          display: true,
          position: 'bottom',
          labels: { boxWidth: 12, font: { size: 10 } }
        }
      }
    }
  });
</script>
@endsection
