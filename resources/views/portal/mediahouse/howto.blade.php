@extends('layouts.portal')
 
 @section('title', 'Media House Registration Guide')
 
 @section('content')
 <style>
   :root {
     --glass-bg: rgba(255, 255, 255, 0.7);
     --glass-border: rgba(255, 255, 255, 0.4);
     --brand-primary: #1e3a8a; /* Blue for Media House */
     --brand-accent: #fbbf24; /* Amber */
     --text-main: #0f172a;
     --text-muted: #475569;
   }
 
   .glass-card {
     background: var(--glass-bg);
     backdrop-filter: blur(12px);
     -webkit-backdrop-filter: blur(12px);
     border: 1px solid var(--glass-border);
     border-radius: 24px;
     box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
   }
 
   .howto-hero {
     background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
     border-radius: 30px;
     padding: 60px 40px;
     margin-bottom: 40px;
     color: #fff;
     position: relative;
     overflow: hidden;
   }
 
   .howto-hero::after {
     content: '';
     position: absolute;
     bottom: -20%;
     right: -10%;
     width: 400px;
     height: 400px;
     background: radial-gradient(circle, rgba(251, 191, 36, 0.1) 0%, transparent 70%);
     border-radius: 50%;
   }
 
   .hero-badge {
     background: rgba(255, 255, 255, 0.15);
     padding: 6px 16px;
     border-radius: 100px;
     font-size: 12px;
     font-weight: 700;
     text-transform: uppercase;
     letter-spacing: 2px;
     display: inline-block;
     margin-bottom: 20px;
     border: 1px solid rgba(255, 255, 255, 0.2);
   }
 
   .req-grid {
     display: grid;
     grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
     gap: 24px;
     margin-bottom: 40px;
   }
 
   .req-list {
     list-style: none;
     padding: 0;
     margin: 0;
   }
 
   .req-item {
     display: flex;
     align-items: flex-start;
     gap: 12px;
     padding: 12px;
     background: rgba(255,255,255,0.5);
     margin-bottom: 10px;
     border-radius: 12px;
     border: 1px solid #f1f5f9;
     transition: all 0.3s ease;
     font-size: 14px;
   }
 
   .req-item:hover {
     background: #fff;
     transform: translateX(5px);
     border-color: var(--brand-primary);
   }
 
   .req-item i {
     color: #10b981;
     margin-top: 3px;
   }
 
   .fee-alert {
     background: rgba(251, 191, 36, 0.1);
     border: 1px solid rgba(251, 191, 36, 0.3);
     padding: 24px;
     border-radius: 18px;
     margin-bottom: 40px;
   }
 
   .contact-box {
     padding: 30px;
     text-align: center;
   }
 
   .contact-grid {
     display: grid;
     grid-template-columns: repeat(3, 1fr);
     gap: 20px;
     margin-top: 20px;
   }
 
   .contact-item {
     padding: 15px;
     background: #f8fafc;
     border-radius: 12px;
     font-size: 13px;
   }
 
   .cta-btn {
     background: #fff;
     color: var(--brand-primary) !important;
     padding: 14px 30px;
     border-radius: 14px;
     font-weight: 800;
     text-decoration: none;
     display: inline-flex;
     align-items: center;
     gap: 10px;
     transition: all 0.3s ease;
   }
 
   .cta-btn:hover {
     background: var(--brand-accent);
     transform: translateY(-3px);
   }
 
   @media (max-width: 768px) {
     .contact-grid { grid-template-columns: 1fr; }
   }
 </style>
 
 <div class="howto-hero">
   <div class="d-flex justify-content-between align-items-center flex-wrap gap-4">
     <div>
       <div class="hero-badge">Entity Registration</div>
       <h1 class="fw-black mb-2">Media House (AP1) Guide</h1>
       <p class="opacity-90 max-w-lg">Establishing your media entity requires comprehensive documentation. Follow this guide to ensure a smooth application.</p>
     </div>
     <a href="{{ route('mediahouse.new') }}" class="cta-btn">
       <i class="ri-file-add-line"></i> Start AP1 Form
     </a>
   </div>
 </div>
 
 <div class="glass-card mb-4" style="padding:40px;">
   <h4 class="fw-bold mb-4 d-flex align-items-center gap-3">
     <i class="ri-list-check-2 ri-lg text-primary"></i>
     Core Requirements Checklist
   </h4>
   
   <div class="req-grid">
     <div class="req-list">
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         Three-year projected cash flow statement
       </div>
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         Three-year projected balance sheet
       </div>
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         Editorial Charter & Code of Ethics
       </div>
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         Employee Code of Conduct
       </div>
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         Detailed Market Analysis
       </div>
     </div>
     <div class="req-list">
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         Certified IDs for all Directors
       </div>
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         In-house style book & Mission Statement
       </div>
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         Dummy copies / Publications
       </div>
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         Certificate of Incorporation
       </div>
       <div class="req-item">
         <i class="ri-checkbox-circle-fill"></i>
         Memorandum of Association
       </div>
     </div>
   </div>
 </div>
 
 <div class="fee-alert shadow-sm">
   <div class="d-flex gap-3">
     <i class="ri-information-fill ri-2x text-warning"></i>
     <div>
       <h6 class="fw-bold text-dark">Fees & Statutory Compliance</h6>
       <p class="mb-0 small text-muted">Fees are governed by <strong>Statutory Instrument 65 of 2022</strong>. Payments are accepted in local currency at the prevailing official bank rate on the day of transaction.</p>
     </div>
   </div>
 </div>
 
 <div class="glass-card contact-box">
   <h5 class="fw-bold mb-1">Need Assistance?</h5>
   <p class="text-muted small">Our support teams are available to guide you through the registration process.</p>
   
   <div class="contact-grid">
     <div class="contact-item">
       <i class="ri-whatsapp-fill text-success ri-xl d-block mb-2"></i>
       <strong>WhatsApp Hotline</strong>
       <div class="mt-1">+263 719 299 150</div>
     </div>
     <div class="contact-item">
       <i class="ri-mail-send-fill text-primary ri-xl d-block mb-2"></i>
       <strong>Email Support</strong>
       <div class="mt-1">info@zmc.org.zw</div>
     </div>
     <div class="contact-item">
       <i class="ri-phone-fill text-secondary ri-xl d-block mb-2"></i>
       <strong>Telephone</strong>
       <div class="mt-1">+263 242 253 509</div>
     </div>
   </div>
 </div>
 @endsection
