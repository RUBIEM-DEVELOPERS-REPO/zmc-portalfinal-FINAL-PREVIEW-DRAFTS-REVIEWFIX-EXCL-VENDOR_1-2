@extends('layouts.portal')

@section('title', 'How to Get Registered')
@section('page_title', 'HOW TO GET REGISTERED')

@section('content')
<div class="zmc-dashboard-wrapper" style="font-family:'Roboto', sans-serif; color:#334155;">

  <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
    <div>
      <h4 class="fw-bold m-0" style="font-size:22px; color:#1e293b;">How to Get Registered</h4>
      <div class="text-muted mt-1" style="font-size:13px;">
        <i class="ri-guide-line me-1"></i>
        Step-by-step guide to the ZMC media house registration process.
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('mediahouse.requirements') }}" class="btn btn-outline-dark btn-sm px-3">
        <i class="ri-list-check-2 me-1"></i> View Requirements
      </a>
      <a href="{{ route('mediahouse.new') }}" class="btn btn-dark btn-sm px-3">
        <i class="ri-file-add-line me-1"></i> Start Registration
      </a>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-lg-3">
      <div class="zmc-card h-100 text-center" style="border-top: 3px solid #2e7d32;">
        <div class="d-flex align-items-center justify-content-center mx-auto mb-3" style="width:50px;height:50px;border-radius:50%;background:rgba(46, 125, 50,0.1);color:#2e7d32;font-size:22px;font-weight:900;">1</div>
        <h6 class="fw-bold" style="color:#2e7d32;">Choose Category</h6>
        <p class="text-muted small mb-0">Select your media service category: <strong>Newspaper</strong>, <strong>Broadcasting</strong>, <strong>Online</strong>, or <strong>News Agency</strong>.</p>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="zmc-card h-100 text-center" style="border-top: 3px solid #2e7d32;">
        <div class="d-flex align-items-center justify-content-center mx-auto mb-3" style="width:50px;height:50px;border-radius:50%;background:rgba(46, 125, 50,0.1);color:#2e7d32;font-size:22px;font-weight:900;">2</div>
        <h6 class="fw-bold" style="color:#2e7d32;">Prepare Documents</h6>
        <p class="text-muted small mb-0">Gather all 12 required documents including financial projections, editorial charter, and incorporation certificates.</p>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="zmc-card h-100 text-center" style="border-top: 3px solid #2e7d32;">
        <div class="d-flex align-items-center justify-content-center mx-auto mb-3" style="width:50px;height:50px;border-radius:50%;background:rgba(46, 125, 50,0.1);color:#2e7d32;font-size:22px;font-weight:900;">3</div>
        <h6 class="fw-bold" style="color:#2e7d32;">Submit AP1</h6>
        <p class="text-muted small mb-0">Complete the AP1 form with organisation details, directors, managers, and upload all required documents.</p>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
      <div class="zmc-card h-100 text-center" style="border-top: 3px solid #2e7d32;">
        <div class="d-flex align-items-center justify-content-center mx-auto mb-3" style="width:50px;height:50px;border-radius:50%;background:rgba(46, 125, 50,0.1);color:#2e7d32;font-size:22px;font-weight:900;">4</div>
        <h6 class="fw-bold" style="color:#2e7d32;">Pay & Receive</h6>
        <p class="text-muted small mb-0">Pay the two-stage fees (application + registration) and receive your registration certificate.</p>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <h6 class="fw-bold mb-3" style="color:#2e7d32;">
          <i class="ri-file-list-3-line me-2" style="color:var(--zmc-accent)"></i>Required Documents (12 Items)
        </h6>
        <div class="row g-2">
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-money-dollar-circle-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Cash Flow (3 years)</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-bar-chart-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Balance Sheet (3 years)</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-newspaper-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Editorial Charter</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-shield-check-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Code of Ethics</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-team-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Employee Code of Conduct</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-line-chart-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Market Analysis</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-id-card-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Directors' Certified IDs</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-book-open-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">In-house Style Book</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-file-copy-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Sample Publication</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-flag-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Mission Statement</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-government-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Certificate of Incorporation</span>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <div class="d-flex align-items-center gap-2 p-2 border rounded mb-1" style="border-radius:8px !important;">
              <i class="ri-file-text-line" style="color:#2e7d32;"></i>
              <span class="small fw-bold">Memorandum of Association</span>
            </div>
          </div>
        </div>
        <div class="mt-3">
          <a href="{{ route('mediahouse.requirements') }}" class="btn btn-sm btn-outline-dark">
            <i class="ri-arrow-right-line me-1"></i> Full Requirements List
          </a>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="zmc-card h-100">
        <h6 class="fw-bold mb-3" style="color:#2e7d32;">
          <i class="ri-bank-card-line me-2" style="color:var(--zmc-accent)"></i>Two-Stage Payment Process
        </h6>
        <div class="mb-3 p-3 border rounded" style="border-radius:10px !important; border-left: 3px solid #2e7d32 !important;">
          <div class="fw-bold small mb-1" style="color:#2e7d32;">Stage 1: Application Fee</div>
          <div class="text-muted small">Paid when submitting your AP1 application. This is a non-refundable processing fee.</div>
        </div>
        <div class="mb-3 p-3 border rounded" style="border-radius:10px !important; border-left: 3px solid var(--zmc-accent) !important;">
          <div class="fw-bold small mb-1" style="color:#2e7d32;">Stage 2: Registration Fee</div>
          <div class="text-muted small">Paid after your application is approved by the Registrar. Your certificate is issued after this payment is confirmed.</div>
        </div>

        <h6 class="fw-bold mb-2 mt-4" style="color:#2e7d32; font-size:13px;">Payment Methods</h6>
        <div class="d-flex align-items-start gap-2 mb-3">
          <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;border-radius:8px;background:rgba(37,99,235,0.1);">
            <i class="ri-smartphone-line" style="color:#2563eb;"></i>
          </div>
          <div>
            <div class="fw-bold small">PayNow (Online)</div>
            <div class="text-muted small">EcoCash, OneMoney, or bank card via PayNow gateway.</div>
          </div>
        </div>
        <div class="d-flex align-items-start gap-2 mb-3">
          <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;border-radius:8px;background:rgba(16,185,129,0.1);">
            <i class="ri-bank-line" style="color:#10b981;"></i>
          </div>
          <div>
            <div class="fw-bold small">Bank Transfer</div>
            <div class="text-muted small">Transfer to ZMC bank account and upload proof of payment.</div>
          </div>
        </div>
        <div class="d-flex align-items-start gap-2">
          <div class="d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;border-radius:8px;background:rgba(245,158,11,0.1);">
            <i class="ri-money-dollar-circle-line" style="color:#f59e0b;"></i>
          </div>
          <div>
            <div class="fw-bold small">Cash Payment</div>
            <div class="text-muted small">Pay in person at any ZMC regional office.</div>
          </div>
        </div>

        <div class="mt-3 p-3 border rounded small" style="border-radius:8px !important; background:rgba(245,158,11,0.05);">
          <i class="ri-error-warning-line me-1 text-warning"></i>
          Fees per <strong>SI 65 of 2022</strong>. Local currency accepted at the prevailing official bank rate.
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-12">
      <div class="zmc-card">
        <h6 class="fw-bold mb-3" style="color:#2e7d32;">
          <i class="ri-customer-service-2-line me-2" style="color:var(--zmc-accent)"></i>Need Help?
        </h6>
        <div class="row g-4">
          <div class="col-12 col-md-4">
            <div class="d-flex align-items-start gap-3">
              <i class="ri-whatsapp-line text-success" style="font-size:20px;margin-top:2px;"></i>
              <div>
                <div class="fw-bold small">WhatsApp Hotline</div>
                <div class="text-muted small">+263 719 299 150</div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="d-flex align-items-start gap-3">
              <i class="ri-mail-line" style="font-size:20px;margin-top:2px;color:#2e7d32;"></i>
              <div>
                <div class="fw-bold small">Email Support</div>
                <div class="text-muted small">info@zmc.org.zw / zmcaccreditation@gmail.com</div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="d-flex align-items-start gap-3">
              <i class="ri-phone-line" style="font-size:20px;margin-top:2px;color:#2e7d32;"></i>
              <div>
                <div class="fw-bold small">Telephone</div>
                <div class="text-muted small">+263 242 253509/10 | +263 242 253572/75/76</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="text-center">
    <a href="{{ route('mediahouse.new') }}" class="btn btn-lg px-5 py-3" style="background:#2e7d32;color:#fff;border-radius:12px;font-weight:900;">
      <i class="ri-file-add-line me-2"></i> Start Your Registration Now
    </a>
    <div class="text-muted small mt-2">You can save your progress as a draft and return later.</div>
  </div>
</div>
@endsection
