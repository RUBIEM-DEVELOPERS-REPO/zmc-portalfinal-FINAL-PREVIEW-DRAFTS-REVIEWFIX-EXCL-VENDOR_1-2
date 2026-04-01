# ZMC System Amendments - Requirements

## Overview
Comprehensive amendments to the Zimbabwe Media Commission Integrated Registration & Accreditation System with strict RBAC, server-side validation, and audit logging.

## A) REGISTRAR REMINDERS → APPLICANT PORTALS

### Requirements
1. Registrar creates and sends reminders to:
   - Individual media practitioners
   - Individual media houses
   - Bulk groups by status (Awaiting Payment, Returned, Pending Documents, Levy Reminder)

2. Reminder Delivery UX:
   - Prominent in-app modal/banner/overlay on login and dashboard load
   - Persistent until dismissed
   - High priority: persistent until acknowledged
   - Components: Title, Message, Priority (High/Normal), Related application (optional), Expiry date/time (optional)

3. Reminder Types:
   - Payment outstanding
   - Missing documents
   - Returned for corrections
   - Levy payments reminder (media houses)
   - Collection reminders

4. Data Model:
   - `reminders` table
   - `reminder_reads` table (acknowledgements with timestamp)

5. Audit: Log creation, edit, send, and acknowledgement

## B) PORTAL "HOW TO" PAGES — IMPROVED DESIGN

### Requirements
1. Media Practitioner Portal:
   - Comprehensive "How to get Accredited" page
   - Step-by-step flow, required documents checklist, fees/payment options, timelines, FAQs, contact/support
   - Icons, cards/sections, clear call-to-action buttons

2. Media House Portal:
   - Comprehensive "How to get Registered" page
   - Step-by-step, required documents checklist, application fee + registration fee stages, timelines, FAQs

3. Dashboard Widget:
   - "Requirements Checklist" card on portal dashboards

## C) PREVIOUS APPLICATIONS + PREVIOUS PAYMENTS VISIBILITY

### Requirements
1. Applicant Portals:
   - List of previous applications for logged-in user
   - Include: application type (new/renewal/replacement), submitted date/time, status, reference numbers, actions (view)

2. Staff Dashboards:
   - Accreditation Officer, Auditor, Registrar: Show Media Practitioner name (not just IDs)
   - "Previous Applications" panel (timeline/list) when viewing applicant record
   - Accounts Officer and Auditor: "Previous Payments" panel
     - PayNow references, proof uploads, cash receipt numbers, verification outcome, dates

3. Permissions:
   - Applicant: own history only
   - Staff: based on RBAC and assigned access
   - Auditor: read-only access

## D) RENEWAL UPLOAD RULES — FREELANCER VS EMPLOYED

### Requirements
1. Renewal document step:
   - Applicant chooses: Freelancer OR Employed

2. Conditional uploads:
   - Freelancer: employment letter NOT required (hidden/disabled)
   - Employed: employment letter upload allowed/required

3. Accreditation Officer dashboard:
   - Applications labeled as: New / Renewal / Replacement (display column)
   - Label populated across all relevant queues

## E) PROFILE REQUIREMENTS (LOCAL/FOREIGN + MULTI PHONE)

### Requirements
1. Media Practitioner Profile:
   - Local applicants: National ID Number field (required)
   - Foreign applicants: Passport Number field (required)
   - System enforces correct requirement based on applicant type

2. Phone numbers:
   - Capture at least TWO phone numbers (required minimum)
   - Validate formats and ensure both are stored

## F) LOGIN ACTIVITY HISTORY (SECURITY AUDIT)

### Requirements
Track for all users:
- Machine/device identifier
- Account name (user display name)
- Operating System
- IP address
- Browser name/version
- Date and time (timezone-aware)

Provide:
- User-facing "Login Activity" page in portals (read-only)
- Admin/Auditor access to view login activity records (read-only for Auditor)
- Audit all access to login activity logs

## G) THEME SUPPORT

### Requirements
- Light/Dark mode fully functional and user-toggleable
- Persist preference per user profile
- Apply across portals and staff dashboards

## H) REMOVE TRANSLATIONS + REMOVE SOCIAL SIGNUP

### Requirements
- Remove language translation features completely
- Remove signup/sign-in using social media links completely
- Keep standard login only

## I) PORTAL SIDEBAR — MEDIA HUB LINK

### Requirements
- Add sidebar menu link "Media Hub" in both portals
- Link opens website's Media Hub page (external or internal route)
- Present for all authenticated portal users

## J) MEDIA HOUSE RENEWAL DOCUMENT REQUIREMENTS

### Requirements
For Media House Renewals, require:
- Previous Certificate (upload required)
- Official Letter requesting renewal (upload required)
- Enforce at submission time with server-side validation

## K) MEDIA HOUSE PORTAL — LICENSE STATUS + EXPIRY + LEVY REMINDERS

### Requirements
1. License status widget:
   - Show current license status (Active/Expired/Suspended)
   - Show "Years left before expiry" (or months/days)
   - Query database dynamically on every dashboard load (no stale caching)

2. Levy payment reminders:
   - Registrar can upload/create levy reminders tied to media house
   - Display prominently on media house dashboard
   - Use same attention-grabbing reminder UX (section A)

3. Media House Profile:
   - Add Social Media Platforms fields (Facebook, X/Twitter, Instagram, YouTube, TikTok, Website URL)
   - Validate as URLs/handles

## L) PORTALS — NOTICES & EVENTS WITH IMAGES

### Requirements
- Notices and Events support image uploads
- Display with thumbnails and full-view images
- Enforce file type/size limits
- Include created_at, created_by (staff), and expiry date (optional)

## M) REMOVE STAFF MANAGEMENT

### Requirements
- Remove Staff Management module/pages/menus entirely from UI and routes
- Existing RBAC admin user management handled outside this removed module
- Do not expose Staff Management UI

## N) ACCOUNTS OFFICER — CASH PAYMENTS OPTION

### Requirements
In Accounts/Payments dashboard:
- Add option "Record Cash Payment"
- Accounts Officer enters:
  - Application ID (or search by applicant name/number)
  - Receipt number (required)
  - Amount (required)
  - Date paid (required)
- System links receipt to application's payment record
- Accounts Officer can approve payment verification and push to Production
- Cash entries auditable and immutable (cannot delete; only void with reason + audit)

## O) PHYSICAL FORMS INTAKE — ACCREDITATION OFFICER FAST TRACK TO PRODUCTION

### Requirements
For applicants already in database who submitted physical forms:

1. Accreditation Officer "Physical Intake" action:
   - Enter Accreditation Number (or Registration Number) manually (required)
   - System queries database, retrieves record, displays for confirmation
   - Accreditation Officer confirms identity and details
   - Accreditation Officer enters Receipt Number (required)
   - Receipt number sent/visible to Accounts as payment record (cash-like linkage)
   - System automatically creates internal application entry marked as:
     - source = PHYSICAL_FORM
     - payment_method = RECEIPT
     - status = PaymentVerified (or PendingAccountsVerification)
   - Record automatically added to Production interface as ready to generate card/certificate

2. Guardrails:
   - Only available if number exists in DB
   - Prevent duplicates: if production-ready record already exists for same number and period, block or require supervisor override with audit

3. Audit:
   - Log lookup, confirmation, receipt assignment, and production enqueue actions

## P) PRODUCTION DASHBOARD — DESIGNER INTERFACE + GENERATION + PRINTING

### Requirements
Add within Production Dashboard (Accreditation Officer role):

1. Card Designer (for accreditation cards)
2. Certificate Designer (for registration certificates/licenses)

Designer requirements:
- Allow uploading background template OR building layout with fields
- Allow placing dynamic fields (name, number, category, expiry, QR, etc.) onto design canvas
- Save versions (yearly templates) and allow selecting template at generation time
- After design finalized:
  - Proceed to Generation step: preview + generate final printable artifact
  - Proceed to Printing step: print and log prints

Print tracking:
- Log who printed, when, how many times, printer used (if available), and template version

## Implementation Constraints

### MUST NOT:
- Add language translations
- Add staff management on media house portal sidebar
- Allow social media sign-up/sign-in

### MUST:
- Implement strict RBAC
- Server-side validation for all inputs
- Audit logging for all critical actions
- No reintroduction of removed modules
