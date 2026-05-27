@extends('layouts.portal')

@section('title', 'Accreditation Requirements')
@section('page_title', 'ACCREDITATION REQUIREMENTS')

@push('styles')
<style>
  .requirements-hero {
    background: linear-gradient(135deg, #1e7e34 0%, #155724 100%);
    border-radius: 16px;
    padding: 2.5rem;
    color: white;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(30, 126, 52, 0.2);
    position: relative;
    overflow: hidden;
  }
  
  .requirements-hero::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -5%;
    width: 250px;
    height: 250px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
  }
  
  .requirements-hero::after {
    content: '';
    position: absolute;
    bottom: -20%;
    left: -5%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
  }
  
  .req-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    height: 100%;
    position: relative;
    overflow: hidden;
  }
  
  .req-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #1e7e34, #28a745);
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  
  .req-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
    border-color: #1e7e34;
  }
  
  .req-card:hover::before {
    opacity: 1;
  }
  
  .req-card-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f1f5f9;
  }
  
  .req-icon-box {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
  
  .req-icon-box.local {
    background: linear-gradient(135deg, #1e7e34, #28a745);
  }
  
  .req-icon-box.foreign {
    background: linear-gradient(135deg, #0d6efd, #0a58ca);
  }
  
  .req-icon-box.renewal {
    background: linear-gradient(135deg, #17a2b8, #138496);
  }
  
  .req-icon-box.replacement {
    background: linear-gradient(135deg, #ffc107, #ff9800);
  }
  
  .req-card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
  }
  
  .req-item {
    display: flex;
    align-items-start;
    gap: 1rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    border-radius: 10px;
    background: #f8fafc;
    transition: all 0.3s ease;
  }
  
  .req-item:hover {
    background: #f1f5f9;
    transform: translateX(4px);
  }
  
  .req-check {
    flex-shrink: 0;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #059669);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
  }
  
  .req-content {
    flex: 1;
  }
  
  .req-title {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
  }
  
  .req-desc {
    color: #64748b;
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
  }
  
  .req-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-left: 0.5rem;
  }
  
  .req-badge.employed {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
  }
  
  .req-badge.freelancer {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
  }
  
  .req-badge.stolen {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
  }
  
  .payment-info-card {
    background: linear-gradient(135deg, rgba(30, 126, 52, 0.05), rgba(30, 126, 52, 0.02));
    border-radius: 16px;
    padding: 2rem;
    border: 2px solid rgba(30, 126, 52, 0.1);
  }
  
  .payment-info-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  
  .payment-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    background: linear-gradient(135deg, #1e7e34, #28a745);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 4px 12px rgba(30, 126, 52, 0.3);
  }
  
  .payment-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e7e34;
    margin: 0;
  }
  
  .info-box {
    background: white;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    border-left: 4px solid #1e7e34;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  }
  
  .info-box:last-child {
    margin-bottom: 0;
  }
  
  .cta-button {
    background: linear-gradient(135deg, #1e7e34, #28a745);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(30, 126, 52, 0.3);
    text-decoration: none;
  }
  
  .cta-button:hover {
    background: linear-gradient(135deg, #28a745, #1e7e34);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(30, 126, 52, 0.4);
    color: white;
  }
  
  .fade-in {
    animation: fadeIn 0.5s ease-in;
  }
  
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>
@endpush

@section('content')
<div id="requirements-page" class="zmc-dashboard-wrapper">
  <div class="requirements-hero fade-in">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3" style="position: relative; z-index: 1;">
      <div class="d-flex align-items-center gap-3">
        <i class="ri-file-list-3-line" style="font-size: 2.5rem;"></i>
        <div>
          <h2 class="m-0 fw-bold" style="font-size: 1.75rem;">Accreditation Requirements</h2>
          <p class="m-0 mt-1" style="opacity: 0.9; font-size: 0.95rem;">
            Everything you need to know before applying for accreditation
          </p>
        </div>
      </div>
      <a href="{{ route('accreditation.new') }}" class="cta-button">
        <i class="ri-file-add-line"></i>
        <span>Start Application</span>
      </a>
    </div>
  </div>


  <div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
<<<<<<< HEAD
      <div class="req-card fade-in">
        <div class="req-card-header">
          <div class="req-icon-box local">
            <i class="ri-user-line"></i>
          </div>
          <h3 class="req-card-title">Local Media Practitioner (AP3)</h3>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Passport-size Photo</div>
            <p class="req-desc">Recent colour photo with white background</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">National ID (Certified Copy)</div>
            <p class="req-desc">Clear scan of both sides of your national identity document</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">
              Employment Letter
              <span class="req-badge employed">Employed</span>
            </div>
            <p class="req-desc">Official letter from your employer confirming your role</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">
              Reference / Testimonial / Affidavit
              <span class="req-badge freelancer">Freelancer</span>
            </div>
            <p class="req-desc">At least one professional reference or sworn affidavit</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Educational Certificates</div>
            <p class="req-desc">Journalism or media qualifications (optional but recommended)</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">3 Referees</div>
            <p class="req-desc">Full names, addresses, and phone numbers of three referees</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Preferred Collection Office</div>
            <p class="req-desc">Harare, Bulawayo, Mutare, Masvingo, Gweru, or Chinhoyi</p>
          </div>
        </div>
=======
      <div class="zmc-card h-100">
        <h6 class="fw-bold mb-3" style="color:#1a1a1a;">
          <i class="ri-user-line me-2" style="color:var(--zmc-accent)"></i>Local Media Practitioner (AP3)
        </h6>
        <ul class="list-unstyled mb-0">
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Passport-size Photo</strong><br><span class="text-muted small">Recent colour photo with white background</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>National ID (Certified Copy)</strong><br><span class="text-muted small">Clear scan of both sides of your national identity document</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Employment Letter</strong> <span class="badge bg-warning-subtle text-warning">Employed</span><br><span class="text-muted small">Official letter from your employer confirming your role</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Reference / Testimonial / Affidavit</strong> <span class="badge bg-info-subtle text-info">Freelancer</span><br><span class="text-muted small">At least one professional reference or sworn affidavit</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Educational Certificates</strong><br><span class="text-muted small">Journalism or media qualifications (optional but recommended)</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>3 Referees</strong><br><span class="text-muted small">Full names, addresses, and phone numbers of three referees</span></div>
          </li>
          <li class="d-flex align-items-start gap-2">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Preferred Collection Office</strong><br><span class="text-muted small">Harare, Bulawayo, Mutare, Masvingo, Gweru, or Chinhoyi</span></div>
          </li>
        </ul>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
      </div>
    </div>

    <div class="col-12 col-lg-6">
<<<<<<< HEAD
      <div class="req-card fade-in" style="animation-delay: 0.1s;">
        <div class="req-card-header">
          <div class="req-icon-box foreign">
            <i class="ri-global-line"></i>
          </div>
          <h3 class="req-card-title">Foreign Media Practitioner (AP3)</h3>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Passport-size Photo</div>
            <p class="req-desc">Recent colour photo with white background</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Passport Bio-Data Page</div>
            <p class="req-desc">Clear scan of your passport information page</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Clearance Letter</div>
            <p class="req-desc">From your country's media regulatory authority or embassy</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Employment / Assignment Letter</div>
            <p class="req-desc">Letter from your media house or commissioning agency</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Travel Details</div>
            <p class="req-desc">Country of origin, arrival date, mode, port of entry, departure date</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Zimbabwe Local Address</div>
            <p class="req-desc">Where you will be staying while in Zimbabwe</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">3 Referees</div>
            <p class="req-desc">Full names, addresses, and phone numbers of three referees</p>
          </div>
        </div>
=======
      <div class="zmc-card h-100">
        <h6 class="fw-bold mb-3" style="color:#1a1a1a;">
          <i class="ri-global-line me-2" style="color:var(--zmc-accent)"></i>Foreign Media Practitioner (AP3)
        </h6>
        <ul class="list-unstyled mb-0">
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Passport-size Photo</strong><br><span class="text-muted small">Recent colour photo with white background</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Passport Bio-Data Page</strong><br><span class="text-muted small">Clear scan of your passport information page</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Clearance Letter</strong><br><span class="text-muted small">From your country's media regulatory authority or embassy</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Employment / Assignment Letter</strong><br><span class="text-muted small">Letter from your media house or commissioning agency</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Travel Details</strong><br><span class="text-muted small">Country of origin, arrival date, mode, port of entry, departure date</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Zimbabwe Local Address</strong><br><span class="text-muted small">Where you will be staying while in Zimbabwe</span></div>
          </li>
          <li class="d-flex align-items-start gap-2">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>3 Referees</strong><br><span class="text-muted small">Full names, addresses, and phone numbers of three referees</span></div>
          </li>
        </ul>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
<<<<<<< HEAD
      <div class="req-card fade-in" style="animation-delay: 0.2s;">
        <div class="req-card-header">
          <div class="req-icon-box renewal">
            <i class="ri-refresh-line"></i>
          </div>
          <h3 class="req-card-title">Renewal (AP5)</h3>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Current Accreditation Card</div>
            <p class="req-desc">Scan of your existing ZMC accreditation card</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Previous Reference Number</div>
            <p class="req-desc">Your existing accreditation reference</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Proof of Payment</div>
            <p class="req-desc">Payment receipt or proof of transfer</p>
          </div>
        </div>
=======
      <div class="zmc-card h-100">
        <h6 class="fw-bold mb-3" style="color:#1a1a1a;">
          <i class="ri-refresh-line me-2" style="color:var(--zmc-accent)"></i>Renewal (AP5)
        </h6>
        <ul class="list-unstyled mb-0">
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Current Accreditation Card</strong><br><span class="text-muted small">Scan of your existing ZMC accreditation card</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Previous Reference Number</strong><br><span class="text-muted small">Your existing accreditation reference</span></div>
          </li>
          <li class="d-flex align-items-start gap-2">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Proof of Payment</strong><br><span class="text-muted small">Payment receipt or proof of transfer</span></div>
          </li>
        </ul>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
      </div>
    </div>

    <div class="col-12 col-lg-6">
<<<<<<< HEAD
      <div class="req-card fade-in" style="animation-delay: 0.3s;">
        <div class="req-card-header">
          <div class="req-icon-box replacement">
            <i class="ri-exchange-line"></i>
          </div>
          <h3 class="req-card-title">Replacement (AP5)</h3>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Affidavit</div>
            <p class="req-desc">Sworn statement explaining the loss/damage</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">
              Police Report
              <span class="req-badge stolen">If stolen</span>
            </div>
            <p class="req-desc">Official police report if the card was stolen</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Proof of Payment</div>
            <p class="req-desc">Payment receipt for replacement fee</p>
          </div>
        </div>
=======
      <div class="zmc-card h-100">
        <h6 class="fw-bold mb-3" style="color:#1a1a1a;">
          <i class="ri-exchange-line me-2" style="color:var(--zmc-accent)"></i>Replacement (AP5)
        </h6>
        <ul class="list-unstyled mb-0">
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Affidavit</strong><br><span class="text-muted small">Sworn statement explaining the loss/damage</span></div>
          </li>
          <li class="d-flex align-items-start gap-2 mb-3">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Police Report</strong> <span class="badge bg-danger-subtle text-danger">If stolen</span><br><span class="text-muted small">Official police report if the card was stolen</span></div>
          </li>
          <li class="d-flex align-items-start gap-2">
            <i class="ri-checkbox-circle-fill text-success mt-1"></i>
            <div><strong>Proof of Payment</strong><br><span class="text-muted small">Payment receipt for replacement fee</span></div>
          </li>
        </ul>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
      </div>
    </div>
  </div>

<<<<<<< HEAD
  <div class="payment-info-card fade-in" style="animation-delay: 0.4s;">
    <div class="payment-info-header">
      <div class="payment-icon">
        <i class="ri-money-dollar-circle-line"></i>
      </div>
      <h3 class="payment-title">Payment Information</h3>
    </div>
    
    <div class="info-box">
      <p class="m-0" style="color: #475569; font-size: 0.95rem;">
        <i class="ri-information-line me-2" style="color: #1e7e34;"></i>
        Fees are as per <strong>Statutory Instrument 65 of 2022</strong>.
      </p>
    </div>
    
    <div class="info-box">
      <p class="m-0" style="color: #475569; font-size: 0.95rem;">
        <i class="ri-alert-line me-2" style="color: #ffc107;"></i>
        Fees are also payable in local currency at the prevailing official bank rate on the day of payment.
      </p>
    </div>
=======
  <div class="zmc-card" style="background: linear-gradient(135deg, rgba(46, 125, 50,0.05), rgba(46, 125, 50,0.02));">
    <h6 class="fw-bold mb-3" style="color:#1a1a1a;">
      <i class="ri-money-dollar-circle-line me-2" style="color:var(--zmc-accent)"></i>Payment Information
    </h6>
    <p class="small text-muted mb-2">Fees are as per <strong>Statutory Instrument 65 of 2022</strong>.</p>
    <p class="small mb-0"><i class="ri-error-warning-line me-1 text-warning"></i> Fees are also payable in local currency at the prevailing official bank rate on the day of payment.</p>
>>>>>>> fcc1ae98e3f498fbea6f4be4c875cef714a0817b
  </div>
</div>
@endsection
