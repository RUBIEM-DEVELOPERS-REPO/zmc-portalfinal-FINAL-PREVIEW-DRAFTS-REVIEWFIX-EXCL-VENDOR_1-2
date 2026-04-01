# Accreditation Officer Dashboard & Page Amendments

**Date**: February 25, 2026  
**Type**: UI + Logic Enforcement (NO workflow changes)  
**Scope**: Dashboard, All Applications, Pending Review, Records Section

---

## A) DASHBOARD PAGE CHANGES

### 1. Rename Dashboard Cards
- ✅ "Total Queue" → "Total Applications"

### 2. Media Practitioners Card (Previously "Accredited Journalists")
- ✅ Label: "Accredited Journalists" → "Media Practitioners"
- ✅ Counters:
  - **Total**: Total number of Media Practitioners in entire database
  - **Accredited**: Total with completed accreditation (PaymentVerified + Produced)
- ❌ Remove: "Uncollected" counter
- **Purpose**: Compare total in system vs accredited

### 3. Media Houses Card (Previously "Registered Media Houses")
- ✅ Label: "Registered Media Houses" → "Media Houses"
- ✅ Counters:
  - **Total**: Total number of Media Houses in database
  - **Registered**: Total fully registered (PaymentVerified + Produced)
- ❌ Remove: "Uncollected" counter
- **Purpose**: Compare total in system vs registered

### 4. Remove Dashboard Card
- ❌ Remove: "Rejected" card completely
- ✅ Keep only:
  - Total Applications
  - Pending Applications
  - Corrections

### 5. Incoming Applications Table
- ✅ Add submission TIME alongside date
- ✅ Format: Date + HH:MM (24hr format)

### 6. Rename Column
- ✅ "Region" → "Collection Region"

### 7. Modify Table Columns
- ✅ "Status" column → "New or Renewal"
- ✅ Add new column: "Foreign or Local"
- ❌ Remove: "Action" column completely

---

## B) ALL APPLICATIONS PAGE AMENDMENTS

### 1. Pagination
- ✅ Implement pagination for entire year dataset
- ✅ Applies to: All applications submitted, reviewed by Officer, passed forward
- ✅ Year selector filter (default: current year)

### 2. Search Upgrade
- ✅ Default quick search: Name OR Accreditation Number OR Registration Number
- ✅ "Advanced Filters" button
- ✅ Advanced filter modal with:
  - Gender
  - Age range
  - Organisation
  - Province
  - Collection Region
  - Foreign or Local
  - New or Renewal
  - Other applicant-filled fields
- ✅ Support combination queries

### 3. Remove "All" Tab
- ❌ Remove: "All" tab
- ✅ Keep:
  - Accreditations
  - Registrations (renamed from "Media House Registrations")

### 4. Terminology Change System-Wide
- ✅ Replace: "Journalist" → "Media Practitioner"
- ⚠️ Exception: Statutory references (where legally required)

### 5. Remove Exports
- ❌ Remove: Export buttons from All Applications page
- ✅ Move: Export functionality to Records Section only

### 6. Filtering Relocation
- ✅ All filters from All Applications → Also available in Records Section
- ✅ Include Advanced Filter modal in Records Section

### 7. Divide All Applications into Tabs
- ✅ **Tab 1: Processed Applications**
  - Definition: Applications reviewed by Officer and sent forward (Registrar/Accounts)
- ✅ **Tab 2: Unprocessed Applications**
  - Definition: Applications not yet reviewed by Officer

### 8. Remove Category Column
- ❌ Remove: Category column from table

---

## C) PENDING REVIEW PAGE UPDATE

### Redefine Pending Review
- ✅ Show: Applications processed by Officer
- ✅ But NOT yet attended to by Accounts/Payments Officer
- ✅ Filter condition:
  ```
  Status = AwaitingAccountsVerification 
  OR RegistrationFeeSubmitted_AwaitingAccountsVerification 
  OR RenewalSubmitted_AwaitingAccountsVerification
  ```

---

## D) APPROVED PAGE CHANGE

- ❌ Remove: Approved page from Officer dashboard
- ✅ Move: Approved list to Production Dashboard
- ✅ Production Dashboard shows:
  - Applications approved by Accounts (PaymentVerified)
  - Ready for production

---

## E) RETURNED PAGE UPDATE

- ✅ Rename: "Rejected/Returned" → "Returned"
- ✅ Show: Applications returned by Registrar for corrections
- ❌ Exclude: Accounts payment rejections (remain in Accounts workflow)

---

## F) RECORDS SECTION STRUCTURE

### Show Only COMPLETED PROCESSES

1. **Accredited Media Practitioners**
   - Condition: Fully processed + produced

2. **Registered Media Houses**
   - Condition: Fully processed + produced

### Features
- ✅ Filtering
- ✅ Advanced filter modal
- ✅ Export functionality (moved from All Applications)

---

## G) REMOVE REJECTED PAGE

- ❌ Remove: Standalone Rejected page from Officer
- ✅ Rejections appear contextually only:
  - Returned (Registrar returns)
  - PaymentRejected (Accounts context only)
- ❌ No separate rejected listing page

---

## H) RBAC + DATA GUARDS

### Ensure
- ✅ No workflow logic broken by UI changes
- ✅ Status transitions remain intact
- ✅ Removed "Action" column does NOT remove internal routing logic
- ✅ Dashboard counters compute from database states (not cached UI data)
- ✅ Pagination and filtering are server-side (not client-only)
- ✅ All changes preserve audit logging

---

## IMPLEMENTATION CHECKLIST

### Phase 1: Dashboard Updates
- [ ] Update dashboard KPIs calculation
- [ ] Rename cards
- [ ] Update Media Practitioners card logic
- [ ] Update Media Houses card logic
- [ ] Remove Rejected card
- [ ] Add time to Incoming Applications table
- [ ] Rename "Region" to "Collection Region"
- [ ] Change "Status" to "New or Renewal"
- [ ] Add "Foreign or Local" column
- [ ] Remove "Action" column

### Phase 2: All Applications Page
- [ ] Implement year-based pagination
- [ ] Upgrade search functionality
- [ ] Add Advanced Filters modal
- [ ] Remove "All" tab
- [ ] Rename tabs
- [ ] Replace "Journalist" with "Media Practitioner"
- [ ] Remove export buttons
- [ ] Add Processed/Unprocessed tabs
- [ ] Remove Category column

### Phase 3: Pending Review Page
- [ ] Redefine query logic
- [ ] Update page to show Officer-processed, Accounts-pending

### Phase 4: Approved Page
- [ ] Remove from Officer dashboard
- [ ] Verify Production Dashboard has this functionality

### Phase 5: Returned Page
- [ ] Rename page
- [ ] Update query to show only Registrar returns
- [ ] Exclude Accounts rejections

### Phase 6: Records Section
- [ ] Add filtering
- [ ] Add Advanced Filters modal
- [ ] Add export functionality
- [ ] Ensure only completed processes shown

### Phase 7: Remove Rejected Page
- [ ] Remove route
- [ ] Remove view
- [ ] Remove sidebar link
- [ ] Update controller

### Phase 8: System-Wide Terminology
- [ ] Replace "Journalist" with "Media Practitioner" across all views
- [ ] Update labels, headers, tooltips
- [ ] Preserve statutory references

---

## WORKFLOW PRESERVATION

### Status Transitions (DO NOT MODIFY)
```
SUBMITTED
  → OFFICER_REVIEW
  → OFFICER_APPROVED
  → REGISTRAR_REVIEW
  → REGISTRAR_APPROVED
  → ACCOUNTS_REVIEW
  → PAYMENT_VERIFIED
  → PRODUCTION_QUEUE
  → PRODUCED_READY_FOR_COLLECTION
  → ISSUED
```

### Rejection Flows (DO NOT MODIFY)
```
OFFICER_REVIEW → CORRECTION_REQUESTED → (Applicant fixes) → SUBMITTED
REGISTRAR_REVIEW → RETURNED_TO_OFFICER → OFFICER_REVIEW
ACCOUNTS_REVIEW → PAYMENT_REJECTED → (Applicant resubmits) → ACCOUNTS_REVIEW
```

---

## QUERY DEFINITIONS

### Dashboard KPIs

#### Total Applications
```php
Application::count()
```

#### Pending Applications
```php
Application::whereIn('status', [
    Application::SUBMITTED,
    Application::OFFICER_REVIEW,
    Application::CORRECTION_REQUESTED,
    Application::RETURNED_TO_OFFICER,
])
->where(function($q) use ($user) {
    $q->whereNull('assigned_officer_id')
      ->orWhere('assigned_officer_id', $user->id);
})
->count()
```

#### Corrections
```php
Application::whereIn('status', [
    Application::CORRECTION_REQUESTED,
    'corrections_requested',
    'needs_correction'
])
->count()
```

#### Media Practitioners - Total
```php
// Count all users who have submitted accreditation applications
User::whereHas('applications', function($q) {
    $q->where('application_type', 'accreditation');
})->count()

// OR count all accreditation applications
Application::where('application_type', 'accreditation')->count()
```

#### Media Practitioners - Accredited
```php
AccreditationRecord::whereIn('status', ['active', 'issued'])
    ->whereNotNull('issued_at')
    ->count()
```

#### Media Houses - Total
```php
// Count all users who have submitted registration applications
User::whereHas('applications', function($q) {
    $q->where('application_type', 'registration');
})->count()

// OR count all registration applications
Application::where('application_type', 'registration')->count()
```

#### Media Houses - Registered
```php
RegistrationRecord::whereIn('status', ['active', 'issued'])
    ->whereNotNull('issued_at')
    ->count()
```

### Pending Review Page Query
```php
Application::whereIn('status', [
    Application::ACCOUNTS_REVIEW,
    Application::REG_FEE_SUBMITTED_AWAITING_VERIFICATION,
    'renewal_submitted_awaiting_accounts_verification',
])
->where(function($q) use ($user) {
    $q->whereNull('assigned_officer_id')
      ->orWhere('assigned_officer_id', $user->id);
})
->paginate(20)
```

### Returned Page Query
```php
Application::whereIn('status', [
    Application::RETURNED_TO_OFFICER,
    'returned_from_registrar',
])
->where(function($q) use ($user) {
    $q->whereNull('assigned_officer_id')
      ->orWhere('assigned_officer_id', $user->id);
})
->paginate(20)
```

### Records Section - Accredited Media Practitioners
```php
AccreditationRecord::whereIn('status', ['active', 'issued'])
    ->whereNotNull('issued_at')
    ->whereNotNull('produced_at')
    ->with('holder')
    ->paginate(20)
```

### Records Section - Registered Media Houses
```php
RegistrationRecord::whereIn('status', ['active', 'issued'])
    ->whereNotNull('issued_at')
    ->whereNotNull('produced_at')
    ->with('contact')
    ->paginate(20)
```

---

## CONFIRMATION

✅ **NO workflow state logic altered**  
✅ **NO approval logic modified**  
✅ **NO payment logic modified**  
✅ **NO production logic modified**  
✅ **NO renewal logic modified**  

**Only implementing**: UI, naming, filtering, and structural amendments as specified.
