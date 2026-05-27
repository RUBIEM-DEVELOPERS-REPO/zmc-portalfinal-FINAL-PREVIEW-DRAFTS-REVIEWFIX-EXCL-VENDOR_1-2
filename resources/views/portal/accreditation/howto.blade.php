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
     --brand-primary: #1a1a1a;
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
     background: radial-gradient(circle, rgba(245, 197, 24, 0.08) 0%, transparent 70%);
     border-radius: 50%;
   }
 
   .hero-badge {
     background: rgba(245, 197, 24, 0.1);
     color: #1a1a1a;
     padding: 8px 20px;
     border-radius: 100px;
     font-size: 11px;
     font-weight: 600;
     text-transform: uppercase;
     letter-spacing: 1.5px;
     display: inline-block;
     margin-bottom: 24px;
     border: 1px solid rgba(0, 0, 0, 0.15);
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
     border-color: #1a1a1a;
     background: #ffffff;
     box-shadow: 0 12px 40px rgba(245, 197, 24, 0.15);
   }
 
   .step-number {
     position: absolute;
     top: 20px;
     right: 30px;
     font-size: 80px;
     font-weight: 900;
     color: rgba(245, 197, 24, 0.28);
     line-height: 1;
     transition: all 0.4s ease;
   }
 
   .premium-step:hover .step-number {
     color: rgba(245, 197, 24, 0.45);
     transform: scale(1.15);
   }
 
   .icon-box {
     width: 64px;
     height: 64px;
     background: rgba(245, 197, 24, 0.08);
     border-radius: 18px;
     display: flex;
     align-items: center;
     justify-content: center;
     font-size: 30px;
     color: #1a1a1a;
     margin-bottom: 24px;
     transition: all 0.3s ease;
     box-shadow: inset 0 0 0 1px rgba(245, 197, 24, 0.1);
   }
 
   .premium-step:hover .icon-box {
     background: #1a1a1a;
     color: #ffffff;
     transform: scale(1.1) rotate(5deg);
   }
 
   .step-content h3 {
     font-size: 22px !important;
     font-weight: 800 !important;
     color: #1a1a1a !important;
     margin-bottom: 15px !important;
   }
 
   .step-content p {
     font-size: 15px !important;
     color: #1a1a1a !important;
     line-height: 1.7 !important;
     margin: 0 !important;
     font-weight: 500 !important;
   }

   .step-content ul {
     list-style-type: disc !important;
     color: #000000 !important;
     padding-left: 20px !important;
     margin: 0 !important;
   }

   .step-content ul li {
     color: #1a1a1a !important;
     font-size: 15px !important;
     line-height: 1.8 !important;
     margin-bottom: 10px !important;
     font-weight: 500 !important;
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
     color: #f5c518;
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
     <div class="hero-badge">Accreditation Guide</div>
     <h1 class="hero-title">How to Get Accredited as a Media Practitioner</h1>
     <p class="hero-subtitle">Follow these simple steps to secure your media practitioner accreditation with the Zimbabwe Media Commission.</p>
   </div>

   <div class="form-container" style="margin-bottom: 40px;">
     <div class="form-header">
       <h3 style="margin: 0; color: #111827; font-weight: 700; font-size: 20px;">New Accreditation (AP3)</h3>
       <p style="margin: 8px 0 0 0; color: #64748b; font-size: 14px;">Apply for your first media practitioner accreditation</p>
     </div>
     <div class="form-steps-container">
       <ol style="color: #1a1a1a; font-size: 15px; line-height: 1.8; padding-left: 20px; margin: 0;">
         <li style="margin-bottom: 18px;">
           <strong>Prepare Your Documents</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Gather a certified copy of your National ID or Passport (clear, legible scan), academic certificates and professional qualifications, employer letter or proof of freelance work, and a recent passport-sized photograph.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Choose Your Accreditation Category</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Select the category that matches your profession: Journalist, Photographer, Cameraman, Sound Technician, Producer, or other relevant media roles.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Complete the Accreditation Form</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Fill in all required fields on form AP3. Ensure all information is accurate and complete. Do not leave any required fields blank.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Upload Supporting Documents</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Upload your certified ID, academic certificates, professional qualifications, employer letter, and photograph in the required file formats. Ensure all documents are clear and legible.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Submit Your Application</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Review all information carefully before submitting. After submission, you will receive a reference number via email to track your application status.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Pay the Accreditation Fee</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Pay the applicable accreditation fee through the portal. Choose your payment method: Paynow (EcoCash, OneMoney) for immediate payment or Bank Transfer with proof of payment upload.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Await Review and Approval</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">ZMC officers will review your application and documents. You may be contacted for clarifications or requested to provide additional information if needed.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Collect Your Accreditation Card</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Once approved, your accreditation card will be issued. You can collect it from ZMC offices or request courier delivery. Track your card status in real-time from your dashboard.</p>
         </li>
       </ol>
     </div>
   </div>

   <div class="form-container" style="margin-bottom: 40px;">
     <div class="form-header">
       <h3 style="margin: 0; color: #111827; font-weight: 700; font-size: 20px;">Renewal (AP5)</h3>
       <p style="margin: 8px 0 0 0; color: #64748b; font-size: 14px;">Renew your accreditation when your card expires</p>
     </div>
     <div class="form-steps-container">
       <ol style="color: #1a1a1a; font-size: 15px; line-height: 1.8; padding-left: 20px; margin: 0;">
         <li style="margin-bottom: 18px;">
           <strong>Check Your Renewal Eligibility</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Log into your portal to view your current accreditation status and renewal date. You can renew your accreditation before the expiration date.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Complete the Renewal Form (AP5)</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Fill in the renewal form with updated information. Include details of any changes in your professional position, employer, or contact information since your last accreditation.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Upload Required Documents</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Upload your current certified ID, proof of continued professional engagement, and any relevant updated qualifications. If your previous card is lost or damaged, indicate this in the application.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Submit and Pay Renewal Fee</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">Submit your renewal application and pay the applicable renewal fee. Payment can be made via Paynow or Bank Transfer with proof of payment.</p>
         </li>
         <li style="margin-bottom: 18px;">
           <strong>Await Approval</strong>
           <p style="margin: 8px 0 0 0; color: #1a1a1a;">ZMC will verify your information and issue your renewed accreditation card. You will be notified via email once your renewal is processed.</p>
         </li>
       </ol>
     </div>
   </div>

   <div class="form-container">
     <div class="form-header">
       <h3 style="margin: 0; color: #111827; font-weight: 700; font-size: 20px;">Important Notes</h3>
     </div>
     <div class="form-steps-container">
       <ul style="color: #1a1a1a; font-size: 15px; line-height: 1.8; padding-left: 20px; margin: 0; list-style-type: disc;">
         <li style="margin-bottom: 12px;">Incomplete applications or missing attachments will delay processing. Ensure all required documents are uploaded before submitting.</li>
         <li style="margin-bottom: 12px;">All documents must be clear, legible, and in the specified file formats (PDF, JPG, PNG).</li>
         <li style="margin-bottom: 12px;">If you have lost your accreditation card, apply for a Replacement (AP7) through your dashboard.</li>
         <li style="margin-bottom: 12px;">Payment must be received and verified before your application can proceed to the review stage.</li>
         <li style="margin-bottom: 12px;">You can track the status of your application at any time using your reference number in the dashboard.</li>
         <li style="margin-bottom: 12px;">If you have any questions, contact the ZMC support team through the "Help" section in your portal.</li>
       </ul>
     </div>
   </div>

   <div class="promo-card" style="margin-top: 40px;">
     <div class="icon-box mb-4" style="width:80px; height:80px; font-size:40px;">
       <i class="ri-flashlight-line"></i>
     </div>
     <h4 class="fw-bold mb-3">Ready to Apply?</h4>
     <p class="text-muted small mb-4">Start your accreditation application today using the portal.</p>
     <a href="{{ route('accreditation.new') }}" class="cta-btn">
       Apply Now <i class="ri-arrow-right-up-line"></i>
     </a>

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
