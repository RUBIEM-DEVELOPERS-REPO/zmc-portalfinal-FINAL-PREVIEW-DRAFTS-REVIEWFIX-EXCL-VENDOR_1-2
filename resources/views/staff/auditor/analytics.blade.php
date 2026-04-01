@extends('layouts.portal')
@section('title', 'Auditor Analytics')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Inter', sans-serif; color:#334155;">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold m-0 text-slate-900" style="font-size:24px; letter-spacing: -0.02em;">Auditor Analytics</h4>
            <div class="text-slate-500 mt-1 d-flex align-items-center" style="font-size:14px;">
                <i class="ri-shield-check-line me-2 text-primary"></i>
                Advanced oversight & integrity metrics
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-white border shadow-sm btn-sm px-3" href="{{ route('staff.auditor.dashboard') }}">
                <i class="ri-arrow-left-line me-1"></i> Dashboard
            </a>
            <div class="dropdown">
                <button class="btn btn-primary btn-sm px-3 shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="ri-calendar-line me-1"></i> {{ (int)request('days', $days) }} Days
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                    @foreach([7, 14, 30, 90, 180, 365] as $d)
                        <li><a class="dropdown-item @if((int)request('days', $days) == $d) active @endif" href="?days={{ $d }}">{{ $d }} Days</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Stats Overview Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="text-muted" style="font-size:12px;">Total Applications</div>
                    <div class="fw-bold" style="font-size:20px;">{{ number_format(array_sum($seriesApplications)) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="text-muted" style="font-size:12px;">Approval Velocity</div>
                    <div class="fw-bold text-success" style="font-size:20px;">{{ number_format(array_sum($seriesApproved)) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="text-muted" style="font-size:12px;">Total Anomalies</div>
                    <div class="fw-bold text-danger" style="font-size:20px;">{{ number_format(array_sum($anomalySeries)) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2 px-3">
                    <div class="text-muted" style="font-size:12px;">Period Verifications</div>
                    <div class="fw-bold" style="font-size:20px;">{{ number_format(array_sum($paymentSeries)) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <div class="fw-bold text-slate-800"><i class="ri-pulse-line me-2 text-primary"></i>Application & Decision Trends</div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="trendChart"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <div class="fw-bold text-slate-800"><i class="ri-pie-chart-2-line me-2 text-danger"></i>Audit Finding Severity</div>
                </div>
                <div class="card-body px-4 pb-4 d-flex flex-column justify-content-center">
                    <div id="anomalyChart"></div>
                    <div class="mt-4 px-2">
                        @foreach($anomalyLabels as $index => $label)
                            <div class="d-flex justify-content-between align-items-center mb-2 small">
                                <span class="text-slate-600"><i class="ri-checkbox-blank-circle-fill me-2" style="color: {{ ['#EF4444', '#F59E0B', '#10B981'][$index] }}"></i>{{ $label }}</span>
                                <span class="fw-bold text-slate-800">{{ $anomalySeries[$index] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <div class="fw-bold text-slate-800"><i class="ri-bank-card-line me-2 text-primary"></i>Payment Oversight</div>
                </div>
                <div class="card-body px-4 pb-4 d-flex align-items-center">
                    <div id="paymentChart" class="w-100"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <div class="fw-bold text-slate-800"><i class="ri-layout-grid-line me-2 text-indigo-500"></i>Application Typology</div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="bucketChart"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ApexCharts SDK -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($labels);
        
        // 1. Trend Chart (Line/Area)
        const trendOptions = {
            series: [
                { name: 'Total Apps', data: @json($seriesApplications) },
                { name: 'Approvals', data: @json($seriesApproved) },
                { name: 'Returned for Correction', data: @json($seriesRejected) }
            ],
            chart: {
                height: 350,
                type: 'area',
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#3b82f6', '#10b981', '#ef4444'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.45, opacityTo: 0.05, stops: [20, 100] }
            },
            xaxis: {
                type: 'datetime',
                categories: labels,
                labels: { style: { colors: '#64748b' } }
            },
            yaxis: { labels: { style: { colors: '#64748b' } } },
            grid: { borderColor: '#f1f5f9' },
            legend: { position: 'top', horizontalAlign: 'right' }
        };
        new ApexCharts(document.querySelector("#trendChart"), trendOptions).render();

        // 2. Anomaly Chart (Donut)
        const anomalyOptions = {
            series: @json($anomalySeries),
            chart: { type: 'donut', height: 260 },
            labels: @json($anomalyLabels),
            colors: ['#EF4444', '#F59E0B', '#10B981'],
            legend: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            total: { show: true, label: 'TOTAL', fontSize: '12px', fontWeight: 600, color: '#64748b' }
                        }
                    }
                }
            },
            dataLabels: { enabled: false }
        };
        new ApexCharts(document.querySelector("#anomalyChart"), anomalyOptions).render();

        // 3. Payment Oversight (Bar)
        const paymentOptions = {
            series: [{ name: 'Count', data: @json($paymentSeries) }],
            chart: { type: 'bar', height: 280, toolbar: { show: false } },
            colors: ['#6366f1'],
            plotOptions: { bar: { borderRadius: 8, distributed: true, columnWidth: '50%' } },
            dataLabels: { enabled: false },
            legend: { show: false },
            xaxis: {
                categories: @json($paymentLabels),
                labels: { style: { colors: '#64748b', fontSize: '11px' } }
            },
            yaxis: { labels: { show: false } },
            grid: { show: false }
        };
        new ApexCharts(document.querySelector("#paymentChart"), paymentOptions).render();

        // 4. Bucket Chart (Horizontal Bar)
        const bucketOptions = {
            series: [{ name: 'Applications', data: @json($pieValues) }],
            chart: { type: 'bar', height: 320, toolbar: { show: false } },
            plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '60%' } },
            colors: ['#4f46e5'],
            dataLabels: { 
                enabled: true, 
                textAnchor: 'start', 
                style: { colors: ['#fff'] },
                formatter: function (val, opt) { return opt.w.globals.labels[opt.dataPointIndex] + ": " + val },
                offsetX: 0,
            },
            xaxis: {
                categories: @json($pieLabels),
                labels: { show: false }
            },
            yaxis: { labels: { show: false } },
            grid: { borderColor: '#f1f5f9', xaxis: { lines: { show: true } } }
        };
        new ApexCharts(document.querySelector("#bucketChart"), bucketOptions).render();
    });
</script>

<style>
    body { background-color: #f8fafc; }
    .card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important; }
    .text-slate-900 { color: #0f172a; }
    .text-slate-800 { color: #1e293b; }
    .text-slate-600 { color: #475569; }
    .text-slate-500 { color: #64748b; }
    .btn-white { background: #fff; color: #1e293b; }
    .dropdown-item.active { background-color: #3b82f6; }
</style>
@endsection
