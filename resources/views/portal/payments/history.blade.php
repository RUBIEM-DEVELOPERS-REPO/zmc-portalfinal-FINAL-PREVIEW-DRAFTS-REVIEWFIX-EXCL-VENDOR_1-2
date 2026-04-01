@extends('layouts.portal')

@section('title', 'Payment History')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">
                    <i class="fa-solid fa-receipt me-2"></i>
                    Payment History
                </h4>
                <a href="{{ route('portal.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if($payments->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-list me-2"></i>
                            Your Payment Records
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Application</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Status</th>
                                        <th>Receipt #</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->created_at->format('d M Y') }}</td>
                                            <td>
                                                <a href="{{ route('portal.applications.details', $payment->application) }}" class="text-decoration-none">
                                                    {{ $payment->application->reference }}
                                                </a>
                                            </td>
                                            <td class="fw-bold">
                                                {{ $payment->currency }} {{ number_format($payment->amount, 2) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ ucfirst($payment->method) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $payment->status === 'paid' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($payment->status) }}
                                                </span>
                                            </td>
                                            <td class="fw-bold">{{ $payment->receipt_number ?? '—' }}</td>
                                            <td>
                                                @if($payment->receipt_number)
                                                    <a href="{{ route('portal.payments.receipt', $payment) }}" 
                                                       class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fa-solid fa-download me-1"></i>
                                                        Receipt
                                                    </a>
                                                @else
                                                    <span class="text-muted">No receipt</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fa-solid fa-receipt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Payment Records Found</h5>
                        <p class="text-muted">You haven't made any payments yet.</p>
                        <a href="{{ route('portal.dashboard') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus me-1"></i>
                            View Applications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
