@extends('layouts.portal')
@section('title', 'Financial Reporting')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Financial Reporting</h4>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('staff.accounts.reports.financial') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="applicant_category[]" class="form-select select2" multiple>
                        <optgroup label="Journalist / Practitioner">
                            <option value="JE">JE — Full-time employed</option>
                            <option value="JF">JF — Freelance (local)</option>
                            <option value="JO">JO — Office for foreign media</option>
                            <option value="JS">JS — Stringing for foreign media</option>
                            <option value="JM">JM — Local and abroad</option>
                            <option value="JP">JP — Content / Photo / PR / Digital</option>
                            <option value="JD">JD — Digital social media</option>
                            <option value="JT">JT — Foreign journalist (temp permit)</option>
                        </optgroup>
                        <optgroup label="Media House">
                            <option value="MC">MC — Community Media</option>
                            <option value="MA">MA — Advertising agency</option>
                            <option value="MF">MF — Local office for foreign media</option>
                            <option value="MN">MN — National newspaper</option>
                            <option value="DG">DG — Internet based media</option>
                            <option value="MP">MP — Production house</option>
                            <option value="MS">MS — Multiple categories</option>
                            <option value="MV">MV — Film and video</option>
                        </optgroup>
                    </select>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Update Reports</button>
                </div>
            </form>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-4 mb-4">
        @foreach([
            ['Revenue', $stats['total_revenue'], 'ri-money-dollar-circle-line', 'primary'],
            ['Transactions', $stats['total_transactions'], 'ri-exchange-line', 'info'],
            ['Paid', $stats['paid_total'], 'ri-checkbox-circle-line', 'success'],
            ['Refunded', $stats['refunded_total'], 'ri-refund-2-line', 'warning'],
            ['Outstanding', $stats['outstanding_balance'], 'ri-time-line', 'danger'],
        ] as [$label, $val, $icon, $color])
        <div class="col">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 rounded bg-{{ $color }} bg-opacity-10 text-{{ $color }} me-2">
                            <i class="{{ $icon }} fs-4"></i>
                        </div>
                        <div class="text-muted small fw-bold">{{ $label }}</div>
                    </div>
                    <div class="h4 fw-bold mb-0">
                        {{ is_numeric($val) && $label != 'Transactions' ? '$'.number_format($val, 2) : $val }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Graphs --}}
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Revenue Trend</h6></div>
                <div class="card-body"><canvas id="revenueTrendChart" style="height: 300px;"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Payment Status Breakdown</h6></div>
                <div class="card-body"><canvas id="statusPieChart" style="height: 300px;"></canvas></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Revenue by Service Type</h6></div>
                <div class="card-body"><canvas id="serviceBarChart"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Revenue by Category</h6></div>
                <div class="card-body"><canvas id="categoryBarChart"></canvas></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h6 class="mb-0 fw-bold">Local vs Foreigner Revenue</h6></div>
                <div class="card-body"><canvas id="residencyBarChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxTrend = document.getElementById('revenueTrendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueTrend->keys()) !!},
                datasets: [{
                    label: 'Revenue ($)',
                    data: {!! json_encode($revenueTrend->values()) !!},
                    borderColor: '#3b82f6',
                    tension: 0.1,
                    fill: true,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)'
                }]
            }
        });

        const ctxPie = document.getElementById('statusPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($statusBreakdown->keys()) !!},
                datasets: [{
                    data: {!! json_encode($statusBreakdown->values()->pluck('amount')) !!},
                    backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#6366f1', '#94a3b8']
                }]
            }
        });

        // Add more charts...
        const ctxService = document.getElementById('serviceBarChart').getContext('2d');
        new Chart(ctxService, {
            type: 'bar',
            data: {
                labels: {!! json_encode($revenueByService->keys()) !!},
                datasets: [{
                    label: 'Amount ($)',
                    data: {!! json_encode($revenueByService->values()) !!},
                    backgroundColor: '#8b5cf6'
                }]
            }
        });

        const ctxCategory = document.getElementById('categoryBarChart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'bar',
            data: {
                labels: {!! json_encode($revenueByCategory->keys()) !!},
                datasets: [{
                    label: 'Amount ($)',
                    data: {!! json_encode($revenueByCategory->values()) !!},
                    backgroundColor: '#ec4899'
                }]
            }
        });

        const ctxResidency = document.getElementById('residencyBarChart').getContext('2d');
        new Chart(ctxResidency, {
            type: 'bar',
            data: {
                labels: {!! json_encode($revenueByResidency->keys()) !!},
                datasets: [{
                    label: 'Amount ($)',
                    data: {!! json_encode($revenueByResidency->values()) !!},
                    backgroundColor: '#f97316'
                }]
            }
        });
    });
</script>
@endpush
@endsection
