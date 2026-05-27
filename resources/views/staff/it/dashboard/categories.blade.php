@extends('layouts.portal')

@section('title', 'Category Master Data - IT Admin')

@section('content')
<div class="it-dashboard" style="font-family: 'Inter', sans-serif; color: #1e293b; background: #f8fafc; min-height: 100vh; padding: 20px;">
    
    <!-- Hero Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-4 rounded-4 shadow-sm border border-slate-100">
        <div>
            <h2 class="fw-bold m-0" style="letter-spacing: -0.02em; color: #0f172a;">Category Master Data</h2>
            <p class="text-slate-600 m-0 small mt-1">Manage Journalist & Media House Categories</p>
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
                    <h6 class="fw-bold m-0">Category Definitions</h6>
                    <button class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">Add Category</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle m-0 small">
                        <thead class="bg-slate-50 border-top border-bottom border-slate-100">
                            <tr>
                                <th class="ps-4 uppercase fw-bold py-3">Category Name</th>
                                <th class="uppercase fw-bold py-3">Code</th>
                                <th class="uppercase fw-bold py-3">Type</th>
                                <th class="uppercase fw-bold py-3">Active Rules</th>
                                <th class="text-end pe-4 uppercase fw-bold py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="py-5 text-center text-slate-500">
                                    <i class="ri-node-tree fs-2 mb-3 d-block opacity-25"></i>
                                    <div class="fw-bold">No custom categories defined</div>
                                    <div class="small">Manage Journalist (J) and Media House (M) sub-categories here.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-4">Core Metadata</h6>
                <div class="small text-slate-600">
                    <p class="mb-2 fw-bold text-slate-900">Journalist Types:</p>
                    <ul class="mb-4">
                        <li>Local Journalist</li>
                        <li>Foreign Journalist</li>
                        <li>Student Journalist</li>
                    </ul>
                    <p class="mb-2 fw-bold text-slate-900">Media Types:</p>
                    <ul class="mb-0">
                        <li>Mainstream Media</li>
                        <li>Freelance Studio</li>
                        <li>Community Radio</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
