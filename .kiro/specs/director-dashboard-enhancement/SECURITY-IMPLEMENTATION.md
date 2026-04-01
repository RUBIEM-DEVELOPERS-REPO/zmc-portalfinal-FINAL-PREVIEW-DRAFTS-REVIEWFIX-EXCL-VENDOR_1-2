# Director Dashboard Security Implementation

## Overview

The Director Dashboard implements **strict view-only access control** to ensure that Directors can oversee operations without interfering with operational workflows. This document describes the multi-layered security approach implemented.

## Security Requirements (Requirement 11)

Directors SHALL NOT be able to:
1. Edit application data
2. Approve or reject applications
3. Assign or reassign applications
4. Generate certificates or cards
5. Print or reprint documents
6. Modify payment records
7. Grant waivers
8. Perform any operational actions

Directors SHALL be able to:
1. View all data and analytics
2. Generate and download reports (PDF, Excel, CSV)

## Multi-Layered Security Implementation

### Layer 1: Middleware - DirectorViewOnly

**File:** `app/Http/Middleware/DirectorViewOnly.php`

This middleware enforces view-only access at the HTTP request level:

- **Allows:** All GET requests (viewing data)
- **Allows:** POST requests ONLY to report generation endpoints:
  - `/staff/director/generate/monthly-accreditation`
  - `/staff/director/generate/revenue-financial`
  - `/staff/director/generate/compliance-audit`
  - `/staff/director/generate/mediahouse-status`
  - `/staff/director/generate/operational-performance`
- **Blocks:** All other POST, PUT, PATCH, DELETE requests
- **Response:** 403 Forbidden with appropriate error message

**Registration:** `bootstrap/app.php`
```php
'director.view_only' => \App\Http\Middleware\DirectorViewOnly::class,
```

### Layer 2: Route-Level Protection

**File:** `routes/web.php`

Director routes are protected with multiple middleware:
```php
Route::middleware(['staff.portal','role:director','director.view_only'])
    ->prefix('staff/director')
    ->name('staff.director.')
    ->group(function () {
        // All director routes
    });
```

**Middleware Stack:**
1. `staff.portal` - Ensures staff portal access
2. `role:director` - Verifies director role
3. `director.view_only` - Enforces view-only access

### Layer 3: Controller-Level Protection

**File:** `app/Http/Controllers/Staff/DirectorController.php`

The controller implements defense-in-depth with:

1. **Constructor Middleware:**
   ```php
   $this->middleware(['auth', 'role:director', 'director.view_only']);
   ```

2. **Helper Methods:**
   - `ensureViewOnlyAccess()` - Verifies director role
   - `ensureReadOnlyOperation()` - Validates request method

3. **Method-Level Checks:**
   All report generation methods call `ensureViewOnlyAccess()` before processing

4. **Documentation:**
   Every method includes security documentation stating it's read-only

### Layer 4: Operational Route Protection

**File:** `routes/web.php`

All operational routes (Officer, Registrar, Accounts, Production) are protected with:
```php
Route::middleware(['staff.portal','role:xxx','block.director.operational'])
```

**Middleware:** `app/Http/Middleware/BlockDirectorOperationalRoles.php`

This middleware blocks ANY director from accessing operational workflows:
- Accreditation Officer workflows
- Registrar workflows
- Accounts/Payments workflows
- Production workflows

**Response:** 403 Forbidden - "Directors have oversight access only and cannot perform operational workflow actions."

## Route Analysis

### Director Routes (View-Only)

All routes are GET except report generation:

**Viewing Routes (GET):**
- `/staff/director/` - Dashboard
- `/staff/director/reports/accreditation` - Accreditation report view
- `/staff/director/reports/financial` - Financial report view
- `/staff/director/reports/compliance` - Compliance report view
- `/staff/director/reports/mediahouses` - Media house report view
- `/staff/director/reports/staff` - Staff performance view
- `/staff/director/reports/issuance` - Issuance oversight view
- `/staff/director/reports/geographic` - Geographic distribution view
- `/staff/director/reports/downloads` - Report downloads page

**Report Generation Routes (POST - Read-Only Operations):**
- `/staff/director/generate/monthly-accreditation` - Generate PDF/Excel/CSV
- `/staff/director/generate/revenue-financial` - Generate PDF/Excel/CSV
- `/staff/director/generate/compliance-audit` - Generate PDF/Excel/CSV
- `/staff/director/generate/mediahouse-status` - Generate PDF/Excel/CSV
- `/staff/director/generate/operational-performance` - Generate PDF/Excel/CSV

**Note:** Report generation is POST but does NOT modify system data - it only creates downloadable files.

### Blocked Operational Routes

Directors CANNOT access these routes (403 Forbidden):

**Officer Routes:**
- `/staff/accreditation-officer/*` - All officer operations
- POST `/staff/officer/applications/{id}/approve` - Approve applications
- POST `/staff/officer/applications/{id}/request-correction` - Request corrections
- POST `/staff/officer/applications/{id}/message` - Send messages

**Registrar Routes:**
- `/staff/registrar/*` - All registrar operations
- POST `/staff/registrar/applications/{id}/assign` - Assign applications
- POST `/staff/registrar/applications/{id}/reassign` - Reassign applications
- POST `/staff/registrar/applications/{id}/approve` - Final approval
- POST `/staff/registrar/applications/{id}/reject` - Reject applications

**Accounts Routes:**
- `/staff/accounts/*` - All accounts operations
- POST `/staff/accounts/payments/{id}/verify` - Verify payments
- POST `/staff/accounts/payments/{id}/waiver` - Grant waivers
- POST `/staff/accounts/payments/{id}/override` - Override payments

**Production Routes:**
- `/staff/production/*` - All production operations
- POST `/staff/production/certificates/{id}/generate` - Generate certificates
- POST `/staff/production/certificates/{id}/print` - Print certificates
- POST `/staff/production/certificates/{id}/reprint` - Reprint certificates

## Security Testing

### Manual Testing Checklist

1. **View Access (Should Succeed):**
   - [ ] Director can view dashboard
   - [ ] Director can view all report pages
   - [ ] Director can generate PDF reports
   - [ ] Director can generate Excel reports
   - [ ] Director can generate CSV reports

2. **Operational Access (Should Fail - 403):**
   - [ ] Director cannot access officer dashboard
   - [ ] Director cannot approve applications
   - [ ] Director cannot reject applications
   - [ ] Director cannot assign applications
   - [ ] Director cannot verify payments
   - [ ] Director cannot grant waivers
   - [ ] Director cannot generate certificates
   - [ ] Director cannot print documents

3. **HTTP Method Testing (Should Fail - 403):**
   - [ ] POST to non-report endpoints blocked
   - [ ] PUT requests blocked
   - [ ] PATCH requests blocked
   - [ ] DELETE requests blocked

### Automated Testing

See `tests/Feature/DirectorViewOnlyAccessTest.php` (if implemented in Task 21.2)

## Error Responses

### JSON Response (AJAX Requests)
```json
{
    "error": "Access Denied",
    "message": "Directors have view-only access and cannot perform operational actions."
}
```
**Status Code:** 403 Forbidden

### HTML Response (Browser Requests)
**Status Code:** 403 Forbidden
**Message:** "Directors have view-only access and cannot perform operational actions."

## Compliance with Requirements

| Requirement | Implementation | Status |
|------------|----------------|--------|
| 11.1 - Prevent editing application data | DirectorViewOnly middleware blocks POST/PUT/PATCH/DELETE | ✅ |
| 11.2 - Prevent approving/rejecting | BlockDirectorOperationalRoles blocks officer routes | ✅ |
| 11.3 - Prevent assigning applications | BlockDirectorOperationalRoles blocks registrar routes | ✅ |
| 11.4 - Prevent generating certificates | BlockDirectorOperationalRoles blocks production routes | ✅ |
| 11.5 - Prevent printing documents | BlockDirectorOperationalRoles blocks production routes | ✅ |
| 11.6 - Prevent modifying payments | BlockDirectorOperationalRoles blocks accounts routes | ✅ |
| 11.7 - Prevent granting waivers | BlockDirectorOperationalRoles blocks accounts routes | ✅ |
| 11.8 - Allow viewing data | All GET routes allowed | ✅ |
| 11.9 - Display access denied message | 403 responses with clear messages | ✅ |

## Defense in Depth Summary

The implementation uses **4 layers of security**:

1. **Middleware Layer** - HTTP request filtering
2. **Route Layer** - Route group protection
3. **Controller Layer** - Method-level validation
4. **Operational Blocking** - Explicit operational route blocking

This multi-layered approach ensures that even if one layer fails, the others provide protection.

## Maintenance Notes

When adding new routes to DirectorController:
1. Ensure they are GET requests (viewing) or POST for report generation only
2. Add security documentation to method PHPDoc
3. For report generation, add route to DirectorViewOnly middleware allowlist
4. Never add routes that modify application, payment, or certificate data

When adding new operational routes:
1. Always include `block.director.operational` middleware
2. Test that directors receive 403 Forbidden
3. Document in this file if it's a new operational category

## References

- **Requirements:** `.kiro/specs/director-dashboard-enhancement/requirements.md` (Requirement 11)
- **Design:** `.kiro/specs/director-dashboard-enhancement/design.md`
- **Tasks:** `.kiro/specs/director-dashboard-enhancement/tasks.md` (Task 21)
