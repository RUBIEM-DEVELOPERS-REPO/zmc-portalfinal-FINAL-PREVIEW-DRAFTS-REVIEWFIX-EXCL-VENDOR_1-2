# ZMC System Amendments - Implementation Tasks

## Phase 1: Database & Models (Priority: Critical)

### Task 1.1: Create Reminders System
- [ ] Create migration for `reminders` table
- [ ] Create migration for `reminder_reads` table
- [ ] Create `Reminder` model with relationships
- [ ] Create `ReminderRead` model with relationships
- [ ] Add indexes for performance

### Task 1.2: Create Login Activity Tracking
- [ ] Create migration for `login_activities` table
- [ ] Create `LoginActivity` model
- [ ] Add indexes for performance

### Task 1.3: Create Cash Payments System
- [ ] Create migration for `cash_payments` table
- [ ] Create `CashPayment` model with relationships
- [ ] Add unique constraint on receipt_number
- [ ] Add indexes for performance

### Task 1.4: Create Physical Intake System
- [ ] Create migration for `physical_intakes` table
- [ ] Create `PhysicalIntake` model with relationships
- [ ] Add indexes for performance

### Task 1.5: Create Designer & Production System
- [ ] Create migration for `design_templates` table
- [ ] Create migration for `print_logs` table
- [ ] Create `DesignTemplate` model
- [ ] Create `PrintLog` model with relationships
- [ ] Add indexes for performance

### Task 1.6: Profile Enhancements
- [ ] Create migration to add fields to `users` table (national_id_number, passport_number, phone_number_2, theme_preference)
- [ ] Create migration to add social media fields to `media_house_profiles` table
- [ ] Create migration to add license status fields to `media_house_profiles` table
- [ ] Update User model with new fields
- [ ] Update MediaHouseProfile model with new fields

### Task 1.7: Notices & Events Enhancements
- [ ] Create migration to add image fields to `notices` table
- [ ] Create migration to add image fields to `events` table (if separate)
- [ ] Update Notice model with image handling
- [ ] Update Event model with image handling

### Task 1.8: Application History Indexing
- [ ] Create migration to add indexes to `applications` table
- [ ] Create migration to add indexes to `payment_submissions` table

## Phase 2: Authentication & Security (Priority: Critical)

### Task 2.1: Login Activity Tracking
- [ ] Create LoginActivityService
- [ ] Update LoginController to log activity
- [ ] Implement device fingerprinting
- [ ] Implement browser detection
- [ ] Implement OS detection
- [ ] Add timezone-aware timestamps

### Task 2.2: Remove Social Media Login
- [ ] Remove social media login buttons from all auth pages
- [ ] Remove social media authentication routes
- [ ] Remove social media OAuth configuration
- [ ] Update auth views (login, register)

### Task 2.3: Profile Validation
- [ ] Add validation rules for local vs foreign applicants
- [ ] Add validation for dual phone numbers
- [ ] Update profile update controller
- [ ] Update registration controller

## Phase 3: Reminders System (Priority: High)

### Task 3.1: Backend - Reminder Management
- [ ] Create ReminderService
- [ ] Create ReminderController (Registrar)
- [ ] Implement create reminder endpoint
- [ ] Implement send reminder endpoint (individual)
- [ ] Implement send reminder endpoint (bulk)
- [ ] Implement update reminder endpoint
- [ ] Implement delete reminder endpoint
- [ ] Add audit logging for all reminder actions

### Task 3.2: Backend - Reminder Delivery
- [ ] Create ReminderDeliveryService
- [ ] Implement logic to fetch unread reminders for user
- [ ] Implement logic to mark reminder as read
- [ ] Implement logic to acknowledge reminder
- [ ] Add API endpoints for portal users

### Task 3.3: Frontend - Registrar Interface
- [ ] Create reminder management page (Registrar dashboard)
- [ ] Create reminder form (create/edit)
- [ ] Implement target selection (individual/bulk)
- [ ] Implement bulk criteria selection
- [ ] Add reminder list view with filters
- [ ] Add send confirmation modal

### Task 3.4: Frontend - Portal Display
- [ ] Create reminder modal component
- [ ] Implement modal trigger on login
- [ ] Implement modal trigger on dashboard load
- [ ] Add persistent display for high-priority reminders
- [ ] Add acknowledge button
- [ ] Add dismiss button (for normal priority)
- [ ] Style for attention-grabbing UX

### Task 3.5: Levy Reminders (Media Houses)
- [ ] Extend reminder system for levy reminders
- [ ] Add levy reminder creation in Registrar dashboard
- [ ] Display levy reminders on media house dashboard
- [ ] Link levy reminders to media house license status

## Phase 4: Portal Enhancements (Priority: High)

### Task 4.1: How-To Pages - Media Practitioner
- [ ] Design "How to get Accredited" page layout
- [ ] Implement step-by-step flow section
- [ ] Implement required documents checklist
- [ ] Implement fees/payment options section
- [ ] Implement timelines section
- [ ] Implement FAQs section
- [ ] Implement contact/support section
- [ ] Add icons and visual elements
- [ ] Add call-to-action buttons

### Task 4.2: How-To Pages - Media House
- [ ] Design "How to get Registered" page layout
- [ ] Implement step-by-step flow section
- [ ] Implement required documents checklist
- [ ] Implement fees section (application + registration)
- [ ] Implement timelines section
- [ ] Implement FAQs section
- [ ] Add icons and visual elements
- [ ] Add call-to-action buttons

### Task 4.3: Dashboard Requirements Widget
- [ ] Create requirements checklist component
- [ ] Add widget to media practitioner dashboard
- [ ] Add widget to media house dashboard
- [ ] Link to full how-to pages

### Task 4.4: Media Hub Link
- [ ] Add "Media Hub" link to portal sidebar (media practitioner)
- [ ] Add "Media Hub" link to portal sidebar (media house)
- [ ] Configure external/internal route
- [ ] Test link functionality

### Task 4.5: License Status Widget (Media House)
- [ ] Create license status component
- [ ] Implement dynamic status query (no caching)
- [ ] Calculate years/months/days until expiry
- [ ] Add color-coded status display
- [ ] Add countdown display
- [ ] Add to media house dashboard

## Phase 5: Application & Payment History (Priority: High)

### Task 5.1: Backend - Application History
- [ ] Create ApplicationHistoryService
- [ ] Implement query for user's own applications
- [ ] Implement query for staff view (with RBAC)
- [ ] Add API endpoint for portal users
- [ ] Add API endpoint for staff

### Task 5.2: Backend - Payment History
- [ ] Create PaymentHistoryService
- [ ] Implement query for user's own payments
- [ ] Implement query for staff view (with RBAC)
- [ ] Add API endpoint for portal users
- [ ] Add API endpoint for staff

### Task 5.3: Frontend - Portal History Pages
- [ ] Create "Previous Applications" page (portal)
- [ ] Display application type (new/renewal/replacement)
- [ ] Display submitted date/time
- [ ] Display status with badges
- [ ] Display reference numbers
- [ ] Add view action
- [ ] Create "Previous Payments" page (portal)
- [ ] Display payment method
- [ ] Display amount
- [ ] Display date
- [ ] Display verification status

### Task 5.4: Frontend - Staff Dashboard Panels
- [ ] Create "Previous Applications" panel component
- [ ] Add panel to application detail view (Officer, Registrar, Auditor)
- [ ] Display timeline/list view
- [ ] Create "Previous Payments" panel component
- [ ] Add panel to application detail view (Accounts, Auditor)
- [ ] Display PayNow references, proof uploads, cash receipts
- [ ] Display verification outcome and dates

### Task 5.5: Update Staff Tables
- [ ] Update Accreditation Officer dashboard to show applicant names
- [ ] Update Auditor dashboard to show applicant names
- [ ] Update Registrar dashboard to show applicant names
- [ ] Ensure proper eager loading to avoid N+1 queries

## Phase 6: Renewal Enhancements (Priority: High)

### Task 6.1: Freelancer vs Employed Logic
- [ ] Update renewal form to add employment status selection
- [ ] Implement conditional display of employment letter upload
- [ ] Add validation rules (freelancer = no letter required)
- [ ] Update renewal controller validation
- [ ] Test both paths (freelancer and employed)

### Task 6.2: Application Type Labeling
- [ ] Ensure `request_type` field populated on all applications
- [ ] Update Accreditation Officer dashboard to display column
- [ ] Add "New / Renewal / Replacement" badge/label
- [ ] Update all relevant queues (Registrar, Accounts, Production)

### Task 6.3: Media House Renewal Documents
- [ ] Add "Previous Certificate" upload field to renewal form
- [ ] Add "Official Letter" upload field to renewal form
- [ ] Add server-side validation (required for renewals)
- [ ] Update renewal controller validation
- [ ] Test validation enforcement

## Phase 7: Cash Payments (Priority: High)

### Task 7.1: Backend - Cash Payment System
- [ ] Create CashPaymentService
- [ ] Create CashPaymentController (Accounts Officer)
- [ ] Implement record cash payment endpoint
- [ ] Implement verify cash payment endpoint
- [ ] Implement void cash payment endpoint
- [ ] Add validation for duplicate receipt numbers
- [ ] Add audit logging for all cash payment actions

### Task 7.2: Frontend - Cash Payment Interface
- [ ] Create "Record Cash Payment" page (Accounts dashboard)
- [ ] Add application search/lookup
- [ ] Add receipt number input
- [ ] Add amount input
- [ ] Add payment date input
- [ ] Add notes field
- [ ] Create cash payment list view
- [ ] Add verify action
- [ ] Add void action (with reason modal)

### Task 7.3: Integration with Payment Flow
- [ ] Link cash payment to application payment record
- [ ] Update payment verification workflow
- [ ] Enable push to Production after verification
- [ ] Test end-to-end flow

## Phase 8: Physical Forms Intake (Priority: High)

### Task 8.1: Backend - Physical Intake System
- [ ] Create PhysicalIntakeService
- [ ] Create PhysicalIntakeController (Accreditation Officer)
- [ ] Implement lookup by number endpoint
- [ ] Implement confirm intake endpoint
- [ ] Add duplicate check logic
- [ ] Add supervisor override logic (with audit)
- [ ] Create internal application entry (source=PHYSICAL_FORM)
- [ ] Link to production queue
- [ ] Add audit logging for all intake actions

### Task 8.2: Frontend - Physical Intake Interface
- [ ] Create "Physical Intake" page (Officer dashboard)
- [ ] Add number input (accreditation or registration)
- [ ] Add lookup button
- [ ] Display retrieved record for confirmation
- [ ] Add receipt number input
- [ ] Add confirm button
- [ ] Add duplicate warning modal
- [ ] Add supervisor override option
- [ ] Display success confirmation

### Task 8.3: Integration with Production
- [ ] Ensure physical intake records appear in production queue
- [ ] Mark as ready to generate
- [ ] Test end-to-end flow

## Phase 9: Production Dashboard (Priority: Medium)

### Task 9.1: Backend - Designer System
- [ ] Create DesignTemplateService
- [ ] Create DesignTemplateController (Accreditation Officer)
- [ ] Implement create template endpoint
- [ ] Implement update template endpoint
- [ ] Implement activate template endpoint
- [ ] Implement list templates endpoint
- [ ] Add file upload for background images
- [ ] Add JSON storage for layout config

### Task 9.2: Frontend - Card Designer
- [ ] Create card designer page
- [ ] Implement canvas/drag-drop interface
- [ ] Add background image upload
- [ ] Add dynamic field placement (name, number, category, expiry, QR)
- [ ] Add field styling options (font, size, color)
- [ ] Add save template functionality
- [ ] Add version management
- [ ] Add preview functionality

### Task 9.3: Frontend - Certificate Designer
- [ ] Create certificate designer page
- [ ] Implement canvas/drag-drop interface
- [ ] Add background image upload
- [ ] Add dynamic field placement
- [ ] Add field styling options
- [ ] Add save template functionality
- [ ] Add version management
- [ ] Add preview functionality

### Task 9.4: Generation & Printing
- [ ] Create generation service
- [ ] Implement generate card/certificate endpoint
- [ ] Create print logging service
- [ ] Implement print log endpoint
- [ ] Create generation interface (select template, preview, generate)
- [ ] Create printing interface (print button, printer selection)
- [ ] Add print log view (who, when, how many times, template version)

## Phase 10: Theme Support (Priority: Medium)

### Task 10.1: Backend - Theme Preference
- [ ] Add theme preference to user profile
- [ ] Create update theme preference endpoint
- [ ] Ensure theme persists across sessions

### Task 10.2: Frontend - Theme Implementation
- [ ] Create light theme CSS
- [ ] Create dark theme CSS
- [ ] Implement theme toggle component
- [ ] Add toggle to user menu/profile
- [ ] Apply theme to all portal pages
- [ ] Apply theme to all staff dashboard pages
- [ ] Test theme switching
- [ ] Ensure smooth transitions

## Phase 11: Notices & Events with Images (Priority: Medium)

### Task 11.1: Backend - Image Upload
- [ ] Update NoticeController to handle image uploads
- [ ] Update EventController to handle image uploads
- [ ] Implement image validation (type, size)
- [ ] Implement thumbnail generation
- [ ] Add expiry date handling

### Task 11.2: Frontend - Notice/Event Display
- [ ] Update notice list view to show thumbnails
- [ ] Update notice detail view to show full image
- [ ] Update event list view to show thumbnails
- [ ] Update event detail view to show full image
- [ ] Add image lightbox/modal for full view
- [ ] Filter expired notices/events

### Task 11.3: Frontend - Notice/Event Creation (Staff)
- [ ] Add image upload field to notice creation form
- [ ] Add image upload field to event creation form
- [ ] Add expiry date field
- [ ] Add image preview
- [ ] Test image upload and display

## Phase 12: Cleanup & Removal (Priority: High)

### Task 12.1: Remove Translations
- [ ] Remove language translation files
- [ ] Remove translation middleware
- [ ] Remove language switcher from UI
- [ ] Remove translation helper functions
- [ ] Update all views to remove translation calls
- [ ] Test all pages for hardcoded English text

### Task 12.2: Remove Staff Management Module
- [ ] Remove staff management routes
- [ ] Remove staff management controllers
- [ ] Remove staff management views
- [ ] Remove staff management menu items
- [ ] Remove staff management permissions (if separate from RBAC)
- [ ] Test that RBAC admin still works

## Phase 13: Login Activity Display (Priority: Medium)

### Task 13.1: Frontend - User Login Activity Page
- [ ] Create "Login Activity" page (portal)
- [ ] Display table with columns: Date/Time, IP Address, Device, OS, Browser
- [ ] Add pagination
- [ ] Add filters (date range)
- [ ] Make read-only for users

### Task 13.2: Frontend - Admin/Auditor Login Activity Page
- [ ] Create "Login Activity" page (admin/auditor dashboard)
- [ ] Display table with all users' login activities
- [ ] Add user filter
- [ ] Add date range filter
- [ ] Add export functionality
- [ ] Make read-only for auditor

## Phase 14: Testing & Validation (Priority: Critical)

### Task 14.1: Unit Tests
- [ ] Write tests for ReminderService
- [ ] Write tests for LoginActivityService
- [ ] Write tests for CashPaymentService
- [ ] Write tests for PhysicalIntakeService
- [ ] Write tests for DesignTemplateService
- [ ] Write tests for ApplicationHistoryService
- [ ] Write tests for PaymentHistoryService

### Task 14.2: Integration Tests
- [ ] Test reminder creation and delivery flow
- [ ] Test cash payment recording and verification flow
- [ ] Test physical intake and production flow
- [ ] Test designer and printing flow
- [ ] Test application history queries
- [ ] Test payment history queries

### Task 14.3: RBAC Tests
- [ ] Test Registrar permissions (reminders)
- [ ] Test Accounts Officer permissions (cash payments)
- [ ] Test Accreditation Officer permissions (physical intake, production)
- [ ] Test Auditor permissions (read-only access)
- [ ] Test Applicant permissions (own data only)

### Task 14.4: Validation Tests
- [ ] Test profile validation (local vs foreign)
- [ ] Test dual phone number validation
- [ ] Test media house renewal document validation
- [ ] Test cash payment duplicate receipt validation
- [ ] Test physical intake duplicate check

### Task 14.5: Security Tests
- [ ] Test server-side validation enforcement
- [ ] Test RBAC enforcement on all endpoints
- [ ] Test audit logging for all critical actions
- [ ] Test data privacy (users see own data only)
- [ ] Test immutability of cash payments and audit logs

## Phase 15: Documentation & Deployment (Priority: High)

### Task 15.1: User Documentation
- [ ] Write user guide for reminders (Registrar)
- [ ] Write user guide for cash payments (Accounts Officer)
- [ ] Write user guide for physical intake (Accreditation Officer)
- [ ] Write user guide for designer and production
- [ ] Write user guide for how-to pages (applicants)
- [ ] Write user guide for login activity

### Task 15.2: Technical Documentation
- [ ] Document API endpoints
- [ ] Document data models
- [ ] Document permission matrix
- [ ] Document validation rules
- [ ] Document audit logging

### Task 15.3: Deployment
- [ ] Run all migrations
- [ ] Seed initial data (if needed)
- [ ] Deploy to staging environment
- [ ] Perform UAT (User Acceptance Testing)
- [ ] Deploy to production environment
- [ ] Monitor for issues

## Task Dependencies

### Critical Path
1. Phase 1 (Database & Models) → All other phases
2. Phase 2 (Authentication & Security) → Phase 13
3. Phase 3 (Reminders) → Phase 4.5 (Levy Reminders)
4. Phase 5 (History) → Phase 5.4 (Staff Panels)
5. Phase 7 (Cash Payments) → Phase 8 (Physical Intake)
6. Phase 8 (Physical Intake) → Phase 9 (Production)
7. Phase 9 (Production) → Phase 9.4 (Printing)

### Parallel Tracks
- Phase 4 (Portal Enhancements) can run parallel to Phase 5-8
- Phase 6 (Renewal Enhancements) can run parallel to Phase 7-8
- Phase 10 (Theme) can run parallel to Phase 11 (Notices)
- Phase 12 (Cleanup) can run parallel to Phase 13 (Login Activity Display)

## Estimated Timeline

- Phase 1: 3-4 days
- Phase 2: 2-3 days
- Phase 3: 4-5 days
- Phase 4: 3-4 days
- Phase 5: 3-4 days
- Phase 6: 2-3 days
- Phase 7: 3-4 days
- Phase 8: 4-5 days
- Phase 9: 5-7 days
- Phase 10: 2-3 days
- Phase 11: 2-3 days
- Phase 12: 1-2 days
- Phase 13: 2-3 days
- Phase 14: 4-5 days
- Phase 15: 2-3 days

**Total Estimated Time: 42-58 days (8-12 weeks)**

## Priority Order for Implementation

1. **Phase 1** (Database & Models) - Foundation
2. **Phase 2** (Authentication & Security) - Security critical
3. **Phase 12** (Cleanup & Removal) - Remove unwanted features early
4. **Phase 3** (Reminders) - High business value
5. **Phase 5** (History) - High business value
6. **Phase 7** (Cash Payments) - High business value
7. **Phase 8** (Physical Intake) - High business value
8. **Phase 6** (Renewal Enhancements) - Improves existing flow
9. **Phase 4** (Portal Enhancements) - User experience
10. **Phase 9** (Production) - Complex but important
11. **Phase 10** (Theme) - Nice to have
12. **Phase 11** (Notices) - Nice to have
13. **Phase 13** (Login Activity Display) - Audit feature
14. **Phase 14** (Testing) - Quality assurance
15. **Phase 15** (Documentation & Deployment) - Final step
