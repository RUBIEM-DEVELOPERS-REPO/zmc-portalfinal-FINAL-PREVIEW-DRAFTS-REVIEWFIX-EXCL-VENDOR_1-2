@extends('layouts.portal')

@section('title', 'Hardware & Printing - IT Admin')

@section('content')
<div class="it-dashboard" style="font-family: 'Inter', sans-serif; color: #1e293b; background: #f8fafc; min-height: 100vh; padding: 20px;">
    
    <!-- Hero Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border border-slate-100">
        <div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.02em; color: #0f172a;">Hardware & Printing Configuration</h2>
            <p class="text-slate-600 m-0 small mt-1">Configure Card Printers & Certificate Printing Setup</p>
        </div>
        <div>
            <a href="{{ route('staff.it.dashboard') }}" class="btn btn-slate-100 rounded-pill px-3 fw-bold small text-slate-600">
                <i class="ri-arrow-left-line"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold m-0">Detected Printers & Devices</h6>
                    <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">Scan for New Hardware</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0 small">
                        <thead class="bg-slate-50 border-top border-bottom border-slate-100">
                            <tr>
                                <th class="ps-4 uppercase fw-bold py-3">Device Name</th>
                                <th class="uppercase fw-bold py-3">Office Location</th>
                                <th class="uppercase fw-bold py-3">Type</th>
                                <th class="uppercase fw-bold py-3">Status</th>
                                <th class="text-end pe-4 uppercase fw-bold py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="py-5 text-center text-slate-500">
                                    <i class="ri-printer-line fs-2 mb-3 d-block opacity-25"></i>
                                    <div class="fw-bold">No connected devices detected</div>
                                    <div class="small">Connect card printers (Evolis, Zebra, Magicard) or network laser printers.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-4">Driver Compatibility</h6>
                <div class="small text-slate-600">
                    <div class="mb-4 d-flex align-items-center gap-2">
                        <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                        <span class="fw-bold">Evolis Primacy/Zenius</span>
                    </div>
                    <div class="mb-4 d-flex align-items-center gap-2">
                        <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                        <span class="fw-bold">Zebra ZXP Series</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-error-warning-fill text-warning fs-5"></i>
                        <span class="fw-bold">Universal PCL6 (Generic)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
