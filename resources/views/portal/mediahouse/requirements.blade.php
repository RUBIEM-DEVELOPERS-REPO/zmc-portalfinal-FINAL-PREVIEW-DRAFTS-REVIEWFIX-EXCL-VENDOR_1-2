@extends('layouts.portal')

@section('title', 'Registration Requirements')
@section('page_title', 'REGISTRATION REQUIREMENTS')

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
  
  .req-icon-box.new-reg {
    background: linear-gradient(135deg, #1e7e34, #28a745);
  }
  
  .req-icon-box.additional {
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
        <i class="ri-building-2-line" style="font-size: 2.5rem;"></i>
        <div>
          <h2 class="m-0 fw-bold" style="font-size: 1.75rem;">Media House Registration Requirements</h2>
          <p class="m-0 mt-1" style="opacity: 0.9; font-size: 0.95rem;">
            Everything you need to know before registering your media house
          </p>
        </div>
      </div>
      <a href="{{ route('mediahouse.new') }}" class="cta-button">
        <i class="ri-file-add-line"></i>
        <span>Start Registration</span>
      </a>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
      <div class="req-card fade-in">
        <div class="req-card-header">
          <div class="req-icon-box new-reg">
            <i class="ri-building-2-line"></i>
          </div>
          <h3 class="req-card-title">New Registration (AP1) - Documents</h3>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Projected Cash Flow Statement</div>
            <p class="req-desc">For a minimum of three (3) years</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Projected Balance Sheet</div>
            <p class="req-desc">For a minimum of three (3) years</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Editorial Charter</div>
            <p class="req-desc">Outlines editorial policies and principles</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Code of Ethics</div>
            <p class="req-desc">Ethical guidelines for media operations</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Code of Conduct for Employees</div>
            <p class="req-desc">Employee conduct and behaviour standards</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Market Analysis</div>
            <p class="req-desc">Analysis of the target market and media landscape</p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="req-card fade-in" style="animation-delay: 0.1s;">
        <div class="req-card-header">
          <div class="req-icon-box additional">
            <i class="ri-attachment-2"></i>
          </div>
          <h3 class="req-card-title">Additional Required Attachments</h3>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Certified IDs for Directors</div>
            <p class="req-desc">Certified copies of identification for all company directors</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">In-house Style Book</div>
            <p class="req-desc">Internal editorial style guide</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Dummy / Sample Publication</div>
            <p class="req-desc">Copy or mock-up of the publication</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Mission Statement</div>
            <p class="req-desc">Organisation's mission and objectives</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Certificate of Incorporation</div>
            <p class="req-desc">Company registration certificate from CIPC</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Memorandum of Association</div>
            <p class="req-desc">Articles and memorandum of the company</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-12 col-lg-6">
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
            <div class="req-title">Current Registration Certificate</div>
            <p class="req-desc">Scan of your existing ZMC registration certificate</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Previous Reference Number</div>
            <p class="req-desc">Your existing registration reference</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Supporting Documents</div>
            <p class="req-desc">Any changes in directors, operations, or details</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Proof of Payment</div>
            <p class="req-desc">Payment receipt or proof of transfer</p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
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
            <p class="req-desc">Sworn statement explaining the loss or damage</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">
              Police Report
              <span class="req-badge stolen">If stolen</span>
            </div>
            <p class="req-desc">Official police report if the certificate was stolen</p>
          </div>
        </div>
        
        <div class="req-item">
          <div class="req-check"><i class="ri-check-line"></i></div>
          <div class="req-content">
            <div class="req-title">Proof of Payment</div>
            <p class="req-desc">Payment receipt for replacement fee</p>
          </div>
        </div>
      </div>
    </div>
  </div>

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
        Application and registration fees are as per <strong>Statutory Instrument 65 of 2022</strong>.
      </p>
    </div>
    
    <div class="info-box">
      <p class="m-0" style="color: #475569; font-size: 0.95rem;">
        <i class="ri-information-line me-2" style="color: #1e7e34;"></i>
        Media house registration involves a <strong>two-stage payment</strong>: an application fee at submission and a registration fee upon approval.
      </p>
    </div>
    
    <div class="info-box">
      <p class="m-0" style="color: #475569; font-size: 0.95rem;">
        <i class="ri-alert-line me-2" style="color: #ffc107;"></i>
        Fees are also payable in local currency at the prevailing official bank rate on the day of payment.
      </p>
    </div>
  </div>
</div>
@endsection
