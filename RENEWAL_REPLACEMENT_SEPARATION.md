# Renewal and Replacement Separation - Implementation Summary

## Overview
Successfully separated the combined "Renewal / Replacement" functionality into two distinct, independent flows for both Accreditation (Journalist) and Media House portals.

## Changes Made

### 1. Controller Methods Added

#### AccreditationPortalController.php
- `replacement()` - Displays the replacement form with drafts
- `saveDraftReplacement()` - Saves replacement draft (delegates to saveDraftAp5 with forced request_type)
- `submitReplacement()` - Submits replacement application (delegates to submitAp5 with forced request_type)

#### MediaHousePortalController.php
- `replacement()` - Displays the replacement form with drafts
- `saveDraftReplacement()` - Saves replacement draft (delegates to saveDraftAp5 with forced request_type)
- `submitReplacement()` - Submits replacement application (delegates to submitAp5 with forced request_type)

### 2. Routes (Already Existed)
All routes were already properly defined in `routes/web.php`:

**Accreditation Portal:**
- GET `/portal/accreditation/renewals` → renewals form (renewal only)
- POST `/portal/accreditation/renewals/save-draft` → save renewal draft
- POST `/portal/accreditation/renewals/submit` → submit renewal
- GET `/portal/accreditation/replacement` → replacement form (replacement only)
- POST `/portal/accreditation/replacement/save-draft` → save replacement draft
- POST `/portal/accreditation/replacement/submit` → submit replacement

**Media House Portal:**
- GET `/media-house/registration/renewals` → renewals form (renewal only)
- POST `/media-house/registration/renewals/save-draft` → save renewal draft
- POST `/media-house/registration/renewals/submit` → submit renewal
- GET `/media-house/registration/replacement` → replacement form (replacement only)
- POST `/media-house/registration/replacement/save-draft` → save replacement draft
- POST `/media-house/registration/replacement/submit` → submit replacement

### 3. View Updates

#### Accreditation Renewals (`resources/views/portal/accreditation/renewals.blade.php`)
- Changed title from "Renewal / Replacement (AP5)" to "Renewal (AP5)"
- Removed the dual-card selection (Renewal vs Replacement)
- Added informational alert directing users to separate Replacement link
- Set `ap5Type` to 'renewal' by default in JavaScript
- Removed replacement reason selection
- Simplified Step 1 validation (no longer checks for type selection)
- Updated form header to "Application for Renewal of Accreditation"

#### Accreditation Replacement (`resources/views/portal/accreditation/replacement.blade.php`)
- Changed title to "Replacement (AP5)"
- Removed the dual-card selection
- Added warning alert directing users to separate Renewal link
- Set `ap5Type` to 'replacement' by default in JavaScript
- Made replacement reason selection always visible (not hidden)
- Updated Step 1 validation to only check replacement reason
- Updated form header to "Application for Replacement of Accreditation"
- Updated draft continue link to use `accreditation.replacement` route

#### Media House Renewals (`resources/views/portal/mediahouse/renewals.blade.php`)
- Changed title from "Renewal / Replacement of Registration (AP5)" to "Renewal of Registration (AP5)"
- Removed the dual-card selection (Renewal vs Replacement)
- Added informational alert directing users to separate Replacement link
- Set `ap5AppType` to 'renewal' by default
- Removed replacement reason selection
- Simplified Step 1 validation
- Updated form header to "AP5 — Renewal"

#### Media House Replacement (`resources/views/portal/mediahouse/replacement.blade.php`)
- Created new file by copying and modifying accreditation replacement
- Changed title to "Replacement (AP5)"
- Updated all references from accreditation to registration
- Changed field labels (Accreditation Number → Registration Number)
- Updated lookup URL to use media house endpoint
- Updated record table fields to show registration-specific data
- Updated changes form to show media house fields (entity name, head office, etc.)
- Removed employment-related document requirements
- Updated document requirements to match media house needs (affidavit, police report)
- Updated submit route to `mediahouse.replacement.submit`
- Updated redirect after submission to `mediahouse.portal`

### 4. Sidebar Navigation (Already Updated)
The sidebar navigation in both `resources/views/layouts/sidebar.blade.php` and `resources/views/layouts/accreditation-portal.blade.php` already had separate links:
- "Renewal (AP5)" → links to renewals route
- "Replacement (AP5)" → links to replacement route

## Key Features

### Renewal Flow
- **Purpose**: Annual renewal of existing accreditation/registration
- **Step 1**: Informational (no selection needed, fixed to renewal)
- **Step 2**: Lookup previous accreditation/registration number
- **Step 3**: Upload required documents (employment letter for employed journalists, previous certificate for media houses)
- **Step 4**: Payment and declaration

### Replacement Flow
- **Purpose**: Replace lost, damaged, or stolen cards/certificates
- **Step 1**: Select replacement reason (lost/damaged/stolen)
- **Step 2**: Lookup previous accreditation/registration number
- **Step 3**: Upload required documents (affidavit required, police report required if stolen)
- **Step 4**: Payment and declaration

## Testing Checklist

### Accreditation Portal
- [ ] Navigate to Renewal link - should show renewal-only form
- [ ] Navigate to Replacement link - should show replacement-only form
- [ ] Save draft for renewal - should create draft with request_type='renewal'
- [ ] Save draft for replacement - should create draft with request_type='replacement'
- [ ] Submit renewal application - should validate and submit correctly
- [ ] Submit replacement application - should validate and submit correctly
- [ ] Verify drafts appear in correct form (renewal drafts in renewals, replacement drafts in replacement)

### Media House Portal
- [ ] Navigate to Renewal link - should show renewal-only form
- [ ] Navigate to Replacement link - should show replacement-only form
- [ ] Save draft for renewal - should create draft with request_type='renewal'
- [ ] Save draft for replacement - should create draft with request_type='replacement'
- [ ] Submit renewal application - should validate and submit correctly
- [ ] Submit replacement application - should validate and submit correctly
- [ ] Verify drafts appear in correct form

### Staff Portal
- [ ] Verify staff can see separate renewal and replacement applications
- [ ] Verify staff navigation shows separate links for renewals and replacements
- [ ] Verify staff can process both types independently

## Benefits of Separation

1. **Clarity**: Users immediately know which form they need
2. **Simplified UX**: No confusing type selection in Step 1
3. **Reduced Errors**: Less chance of selecting wrong type
4. **Better Validation**: Each form validates only relevant fields
5. **Cleaner Code**: Separate concerns, easier to maintain
6. **Staff Efficiency**: Staff can filter and process renewals vs replacements separately

## Files Modified

1. `app/Http/Controllers/AccreditationPortalController.php` - Added 3 methods
2. `app/Http/Controllers/MediaHousePortalController.php` - Added 3 methods
3. `resources/views/portal/accreditation/renewals.blade.php` - Updated to renewal-only
4. `resources/views/portal/accreditation/replacement.blade.php` - Updated to replacement-only
5. `resources/views/portal/mediahouse/renewals.blade.php` - Updated to renewal-only
6. `resources/views/portal/mediahouse/replacement.blade.php` - Created new file for replacement-only

## No Breaking Changes

- All existing routes remain functional
- Existing drafts will continue to work
- Staff portal views remain compatible
- Database schema unchanged
- No migration required

## Completion Status

✅ Controller methods added
✅ Views updated and separated
✅ Routes verified (already existed)
✅ Sidebar navigation verified (already updated)
✅ JavaScript validation updated
✅ Document requirements separated
✅ Form headers and titles updated
✅ Syntax validation passed
✅ No diagnostics errors

## Next Steps for User

1. Clear browser cache to ensure latest JavaScript loads
2. Test both renewal and replacement flows in both portals
3. Verify staff portal displays separate applications correctly
4. Monitor for any user feedback or issues

---

**Implementation Date**: March 15, 2026
**Status**: Complete
