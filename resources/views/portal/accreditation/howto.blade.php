@extends('layouts.portal')
 
 @section('title', 'How to Get Accredited')
 @section('page_title', 'HOW TO GET ACCREDITED')
 
 @push('styles')
 <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
 <meta http-equiv="Pragma" content="no-cache">
 <meta http-equiv="Expires" content="0">
 @endpush
 
 @section('content')
 <!-- File Updated: {{ now() }} -->
 <style>
   :root {
     --glass-bg: rgba(255, 255, 255, 0.7);
     --glass-border: rgba(255, 255, 255, 0.4);
     --brand-primary: #1a4d2e; /* Deeper Green */
     --brand-accent: #ffd700; /* Richer Gold */
     --text-main: #1e293b;
     --text-muted: #64748b;
   }
 
   .howto-wrapper {
     perspective: 1000px;
   }
 
   .glass-card {
     background: #ffffff;
     backdrop-filter: blur(12px);
     -webkit-backdrop-filter: blur(12px);
     border: 1px solid #e2e8f0;
     border-radius: 24px;
     box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
   }
 
   .howto-hero {
     background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
     border-radius: 30px;
     padding: 80px 40px;
     margin-bottom: 50px;
     position: relative;
     overflow: hidden;
     color: #1e293b;
     border: 2px solid #e2e8f0;
   }
 
   .howto-hero::after {
     content: '';
     position: absolute;
     top: -10%;
     right: -10%;
     width: 300px;
     height: 300px;
     background: radial-gradient(circle, rgba(45, 80, 22, 0.08) 0%, transparent 70%);
     border-radius: 50%;
   }
 
   .hero-badge {
     background: rgba(45, 80, 22, 0.1);
     color: #2d5016;
     padding: 8px 20px;
     border-radius: 100px;
     font-size: 12px;
     font-weight: 700;
     text-transform: uppercase;
     letter-spacing: 2px;
     display: inline-block;
     margin-bottom: 24px;
     border: 1px solid rgba(45, 80, 22, 0.2);
   }
 
   .hero-title {
     font-size: clamp(32px, 5vw, 48px);
     font-weight: 800;
     line-height: 1.1;
     margin-bottom: 20px;
     color: #1e293b;
   }
 
   .hero-subtitle {
     font-size: 18px;
     color: #64748b;
     max-width: 600px;
     line-height: 1.6;
   }
 
   .step-grid {
     display: grid;
     grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
     gap: 30px;
     margin-bottom: 60px;
   }
 
   .premium-step {
     padding: 40px;
     transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
     position: relative;
     height: 100%;
     border: 1px solid transparent;
   }
 
   .premium-step:hover {
     transform: translateY(-10px) rotateX(2deg);
     border-color: #2d5016;
     background: #ffffff;
     box-shadow: 0 12px 40px rgba(45, 80, 22, 0.15);
   }
 
   .step-number {
     position: absolute;
     top: 20px;
     right: 30px;
     font-size: 60px;
     font-weight: 900;
     color: rgba(26, 77, 46, 0.05);
     line-height: 1;
     transition: all 0.4s ease;
   }
 
   .premium-step:hover .step-number {
     color: rgba(26, 77, 46, 0.1);
     transform: scale(1.1);
   }
 
   .icon-box {
     width: 64px;
     height: 64px;
     background: rgba(45, 80, 22, 0.08);
     border-radius: 18px;
     display: flex;
     align-items: center;
     justify-content: center;
     font-size: 30px;
     color: #2d5016;
     margin-bottom: 24px;
     transition: all 0.3s ease;
     box-shadow: inset 0 0 0 1px rgba(45, 80, 22, 0.1);
   }
 
   .premium-step:hover .icon-box {
     background: #2d5016;
     color: #ffffff;
     transform: scale(1.1) rotate(5deg);
   }
 
   .step-content h3 {
     font-size: 20px !important;
     font-weight: 700 !important;
     color: #000000 !important;
     margin-bottom: 12px !important;
   }
 
   .step-content p {
     font-size: 15px !important;
     color: #000000 !important;
     line-height: 1.6 !important;
     margin: 0 !important;
   }

   .step-content ul {
     list-style-type: disc !important;
     color: #000000 !important;
     padding-left: 20px !important;
     margin: 0 !important;
   }

   .step-content ul li {
     color: #000000 !important;
     font-size: 14px !important;
     line-height: 1.8 !important;
     margin-bottom: 8px !important;
   }

   /* Force text visibility in all step cards */
   .premium-step h3,
   .premium-step p,
   .premium-step ul,
   .premium-step li,
   .step-content h3,
   .step-content p,
   .step-content ul,
   .step-content li {
     color: #000000 !important;
     opacity: 1 !important;
     visibility: visible !important;
   }
 
   .checklist-wrapper {
     display: grid;
     grid-template-columns: 1.2fr 0.8fr;
     gap: 30px;
     margin-bottom: 60px;
   }
 
   .checklist-card {
     padding: 50px;
   }
 
   .checklist-title {
     font-size: 28px;
     font-weight: 800;
     margin-bottom: 30px;
     display: flex;
     align-items: center;
     gap: 15px;
     color: #000000 !important;
   }
 
   .modern-list {
     list-style: none;
     padding: 0;
     margin: 0;
     display: grid;
     grid-template-columns: 1fr 1fr;
     gap: 20px;
   }
 
   .modern-item {
     display: flex;
     align-items: center;
     gap: 12px;
     padding: 15px;
     background: rgba(248, 250, 252, 0.5);
     border-radius: 16px;
     border: 1px solid #f1f5f9;
     font-weight: 600;
     color: #000000 !important;
     transition: all 0.3s ease;
   }
 
   .modern-item:hover {
     background: #fff;
     border-color: var(--brand-primary);
     transform: translateX(5px);
   }
 
   .modern-item i {
     color: #10b981;
     font-size: 20px;
   }
 
   .promo-card {
     background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
     padding: 40px;
     text-align: center;
     display: flex;
     flex-direction: column;
     justify-content: center;
     align-items: center;
     border-radius: 24px;
     border: 2px dashed #cbd5e1;
   }
 
   .cta-btn {
     background: var(--brand-primary);
     color: var(--brand-accent) !important;
     padding: 20px 50px;
     border-radius: 20px;
     font-size: 18px;
     font-weight: 800;
     text-decoration: none;
     display: inline-flex;
     align-items: center;
     gap: 15px;
     transition: all 0.3s ease;
     box-shadow: 0 20px 40px rgba(26, 77, 46, 0.2);
     border: 2px solid var(--brand-accent);
   }
 
   .cta-btn:hover {
     transform: scale(1.05) translateY(-5px);
     box-shadow: 0 30px 60px rgba(26, 77, 46, 0.3);
     background: var(--brand-accent);
     color: var(--brand-primary) !important;
     border-color: var(--brand-primary);
   }
 
   @media (max-width: 992px) {
     .checklist-wrapper { grid-template-columns: 1fr; }
     .modern-list { grid-template-columns: 1fr; }
     .howto-hero { padding: 60px 30px; }
   }
 </style>
 
 <div class="howto-wrapper">
   <div class="howto-hero">
     <div class="hero-badge">Registration Guide</div>
     <h1 class="hero-title">Seamless Accreditation <br> for Media Professionals</h1>
     <p class="hero-subtitle">The ZMC digital portal streamlines your accreditation journey. Follow these simple steps to secure your credentials.</p>
   </div>
 
   <div class="step-grid">
     <div class="glass-card premium-step">
       <div class="step-number">01</div>
       <div class="icon-box">
         <i class="ri-user-follow-line"></i>
       </div>
       <div class="step-content" style="color: #000000 !important;">
         <h3 style="color: #000000 !important; font-size: 20px !important; font-weight: 700 !important; margin-bottom: 15px !important;">Step 1: Create Account & Profile</h3>
         <ul style="color: #000000 !important; font-size: 14px !important; line-height: 1.8 !important; padding-left: 20px !important; margin: 0 !important;">
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Register on the ZMC portal with your email address</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Verify your email through the confirmation link sent</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Complete your personal details (full name, ID number, contact info)</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Fill in your professional information and media category</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Upload a recent passport-sized photograph</li>
         </ul>
       </div>
     </div>
 
     <div class="glass-card premium-step">
       <div class="step-number">02</div>
       <div class="icon-box">
         <i class="ri-file-upload-line"></i>
       </div>
       <div class="step-content" style="color: #000000 !important;">
         <h3 style="color: #000000 !important; font-size: 20px !important; font-weight: 700 !important; margin-bottom: 15px !important;">Step 2: Submit Application & Documents</h3>
         <ul style="color: #000000 !important; font-size: 14px !important; line-height: 1.8 !important; padding-left: 20px !important; margin: 0 !important;">
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Click "New Application" from your dashboard</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Select your accreditation type (Journalist, Photographer, etc.)</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Upload National ID or Passport (clear, legible scans)</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Upload academic certificates and professional qualifications</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Attach employer letter or proof of freelance work</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Review and submit your application</li>
         </ul>
       </div>
     </div>
 
     <div class="glass-card premium-step">
       <div class="step-number">03</div>
       <div class="icon-box">
         <i class="ri-bank-card-line"></i>
       </div>
       <div class="step-content" style="color: #000000 !important;">
         <h3 style="color: #000000 !important; font-size: 20px !important; font-weight: 700 !important; margin-bottom: 15px !important;">Step 3: Pay Application Fee</h3>
         <ul style="color: #000000 !important; font-size: 14px !important; line-height: 1.8 !important; padding-left: 20px !important; margin: 0 !important;">
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Receive payment notification via email and SMS</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Choose payment method: Paynow (EcoCash, OneMoney) or Bank Transfer</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">For Paynow: Complete instant payment through secure gateway</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">For Bank Transfer: Upload proof of payment (deposit slip/receipt)</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Wait for payment verification (usually within 24-48 hours)</li>
         </ul>
       </div>
     </div>
 
     <div class="glass-card premium-step">
       <div class="step-number">04</div>
       <div class="icon-box">
         <i class="ri-id-card-line"></i>
       </div>
       <div class="step-content" style="color: #000000 !important;">
         <h3 style="color: #000000 !important; font-size: 20px !important; font-weight: 700 !important; margin-bottom: 15px !important;">Step 4: Review & Card Issuance</h3>
         <ul style="color: #000000 !important; font-size: 14px !important; line-height: 1.8 !important; padding-left: 20px !important; margin: 0 !important;">
           <li style="color: #000000 !important; margin-bottom: 8px !important;">ZMC officers review your application and documents</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">You may receive requests for corrections or additional documents</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Once approved, your card enters production</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Track card production status in real-time from your dashboard</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Collect your accreditation card from ZMC offices or via courier</li>
           <li style="color: #000000 !important; margin-bottom: 8px !important;">Typical processing time: 5-10 working days after approval</li>
         </ul>
       </div>
     </div>
   </div>
 
   <div class="checklist-wrapper">
     <div class="glass-card checklist-card">
       <h3 class="checklist-title">
         <i class="ri-checkbox-multiple-line text-success"></i>
         Readiness Checklist
       </h3>
       <div class="modern-list">
         <div class="modern-item">
           <i class="ri-check-double-line"></i>
           National ID / Passport
         </div>
         <div class="modern-item">
           <i class="ri-check-double-line"></i>
           Academic Certificates
         </div>
         <div class="modern-item">
           <i class="ri-check-double-line"></i>
           Professional Bio-Data
         </div>
         <div class="modern-item">
           <i class="ri-check-double-line"></i>
           Passport-sized Photo
         </div>
         <div class="modern-item">
           <i class="ri-check-double-line"></i>
           Employer Letter
         </div>
         <div class="modern-item">
           <i class="ri-check-double-line"></i>
           Previous Card (if renewal)
         </div>
       </div>
     </div>
 
     <div class="promo-card">
       <div class="icon-box mb-4" style="width:80px; height:80px; font-size:40px;">
         <i class="ri-flashlight-line"></i>
       </div>
       <h4 class="fw-bold mb-3">Instant Processing</h4>
       <p class="text-muted small mb-4">Most digital applications are reviewed within 48-72 working hours once all documents are verified.</p>
       <a href="{{ route('accreditation.new') }}" class="cta-btn">
         Apply Now <i class="ri-arrow-right-up-line"></i>
       </a>
     </div>
   </div>
 </div>

 <style>
   /* Critical override - load after all other styles */
   .premium-step .step-content h3,
   .premium-step .step-content p,
   .premium-step .step-content ul,
   .premium-step .step-content li,
   .step-content h3,
   .step-content p,
   .step-content ul,
   .step-content li {
     color: #000000 !important;
     opacity: 1 !important;
     visibility: visible !important;
     display: block !important;
   }

   .step-content ul {
     display: block !important;
     list-style-type: disc !important;
   }

   .step-content li {
     display: list-item !important;
   }
 </style>
 @endsection
