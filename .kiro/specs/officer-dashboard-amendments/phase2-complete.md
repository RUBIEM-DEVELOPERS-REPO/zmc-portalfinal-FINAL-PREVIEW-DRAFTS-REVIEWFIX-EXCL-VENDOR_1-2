# Phase 2: All Applications Page Amendments - COMPLETE

**Date**: February 25, 2026  
**Status**: ✅ COMPLETE

---

## COMPLETED CHANGES

### 1. Year-Based Pagination ✅
**File**: `resources/views/staff/officer/applications_list_filtered.blade.php`

- Added year selector showing current year and 5 years back
- Default: Current year (2026)
- Year filter applied to all queries
- Preserves other filters when switching years

**Controller**: `app/Http/Controllers/Staff/AccreditationOfficerController.php`
```php
$year = $request->query('year', now()->year);
$q->whereYear('created_at', $year);
```

### 2. Processing Status Tabs ✅
Added three tabs to divide applications:
- **All**: Shows all applications (no filter)
- **Processed**: Applications reviewed by Officer and sent forward (Registrar/Accounts)
- **Unprocessed**: Applications not yet reviewed by Officer

**Processed Statuses**:
- OFFICER_APPROVED
- REGISTRAR_REVIEW
- REGISTRAR_APPROVED
- ACCOUNTS_REVIEW
- PAYMENT_VERIFIED
- production_queue
- produced_ready_for_collection
- issued

**Unprocessed Statuses**:
- SUBMITTED
- OFFICER_REVIEW
- CORRECTION_REQUESTED
- RETURNED_TO_OFFICER

### 3. Search Upgrade ✅

#### Quick Search (Main Form)
- Searches: Name, Accreditation Number, Registration Number
- Single input field with placeholder text
- Simplified from previous multi-field search

#### Advanced Filters Modal
Added modal with comprehensive filtering options:
- **Gender**: Male, Female, Other
- **Age Range**: Min and Max age inputs
- **Organisation**: Text search for employer name
- **Province**: Dropdown with all 10 provinces
- **Collection Region**: Dropdown (Harare, Bulawayo, Mutare, Gweru, Masvingo)
- **Foreign or Local**: Dropdown (Local, Foreign)
- **New or Renewal**: Dropdown (New, Renewal, Replacement)
- **Nationality**: Text search

All filters support combination queries (AND logic).

### 4. Removed "All" Tab ✅
- Removed "All" tab from application type tabs
- Kept only:
  - **Accreditations** (default if no type selected)
  - **Registrations** (renamed from "Media House Registrations")

### 5. Terminology Changes ✅
- "Media House Registrations" → "Registrations"
- Updated placeholder text to be more descriptive
- Removed export buttons (as per requirements)

### 6. Removed Export Buttons ✅
- Removed "Export CSV" button
- Removed "Export PDF" button
- Export functionality to be moved to Records Section only

### 7. Table Column Updates ✅

#### Removed Columns
- **Category** column removed

#### Added Columns
- **New or Renewal**: Shows badge (Renewal = info/blue, New = primary)
- **Foreign or Local**: Shows badge (Foreign = warning/yellow, Local = success/green)

#### Updated Columns
- **Status**: Made smaller text (small class)
- All columns properly aligned

### 8. Advanced Filters Implementation ✅

**Controller Logic** (`applicationsBaseQuery` method):
```php
// Gender filter
if ($gender = $request->query('gender')) {
    if (in_array($gender, ['male','female','other'], true)) {
        $q->where('gender', $gender);
    }
}

// Age range filters
if ($ageMin = $request->query('age_min')) {
    $q->whereRaw('TIMESTAMPDIFF(YEAR, dob, CURDATE()) >= ?', [(int)$ageMin]);
}

// Organisation filter
if ($org = $request->query('organisation')) {
    $q->where('employer_name', 'like', "%{$org}%");
}

// Province filter
if ($province = $request->query('province')) {
    $q->where('province', $province);
}

// Collection region filter
if ($collectionRegion = $request->query('collection_region')) {
    $q->where('collection_region', $collectionRegion);
}

// Nationality filter
if ($nationality = $request->query('nationality')) {
    $q->where('nationality', 'like', "%{$nationality}%");
}

// Scope filter (Foreign/Local)
if ($sc = $request->query('scope')) {
    if ($sc === 'local') {
        $q->where(fn($w) => $w->where('journalist_scope', 'local')->orWhereNull('journalist_scope'));
    } else {
        $q->where('journalist_scope', 'foreign');
    }
}

// Request type filter (New/Renewal)
if ($rt = $request->query('request_type')) {
    if (in_array($rt, ['new','renewal','replacement'], true)) {
        $q->where('request_type', $rt);
    }
}
```

---

## FILES MODIFIED

1. **resources/views/staff/officer/applications_list_filtered.blade.php**
   - Added year selector (current year + 5 years back)
   - Added processing status tabs (All, Processed, Unprocessed)
   - Removed "All" tab from application types
   - Renamed "Media House Registrations" to "Registrations"
   - Updated quick search placeholder
   - Removed export buttons
   - Added Advanced Filters button
   - Created Advanced Filters modal with 8 filter fields
   - Removed Category column
   - Added New or Renewal column
   - Added Foreign or Local column
   - Updated empty state colspan to 8

2. **app/Http/Controllers/Staff/AccreditationOfficerController.php**
   - Updated `applicationsBaseQuery()` method
   - Added year filter (default current year)
   - Added processing status filter logic
   - Added gender filter
   - Added age range filters (min/max)
   - Added organisation filter
   - Added province filter
   - Added collection region filter
   - Added nationality filter
   - Enhanced search to include accreditation/registration numbers
   - All filters use column existence checks for safety

---

## VERIFICATION CHECKLIST

- [x] Year selector displays correctly (current year + 5 years back)
- [x] Processing status tabs work (All, Processed, Unprocessed)
- [x] "All" tab removed from application types
- [x] "Registrations" tab renamed correctly
- [x] Quick search searches name, accreditation number, registration number
- [x] Advanced Filters button opens modal
- [x] Advanced Filters modal has all 8 filter fields
- [x] All filters can be combined
- [x] Export buttons removed
- [x] Category column removed
- [x] New or Renewal column displays correctly
- [x] Foreign or Local column displays correctly
- [x] Table colspan updated to 8
- [x] No PHP/Blade syntax errors
- [x] No diagnostics issues

---

## WORKFLOW PRESERVATION

✅ **NO workflow logic modified**
- Status transitions remain unchanged
- Approval logic intact
- Payment logic intact
- Production logic intact
- Renewal logic intact
- Audit logging preserved
- Pool visibility logic maintained
- Lock timeout logic maintained

---

## FILTER BEHAVIOR

### Year Filter
- Default: Current year (2026)
- Filters by `created_at` year
- Persists across other filter changes

### Processing Status Filter
- **All**: No status filter applied (shows all)
- **Processed**: Shows applications that have been reviewed by Officer and sent forward
- **Unprocessed**: Shows applications not yet reviewed by Officer

### Advanced Filters
- All filters use AND logic (combination queries)
- Filters persist when switching tabs
- Reset button clears all filters
- Modal preserves selected values when reopened

### Search Behavior
- Quick search: OR logic across name, email, reference, accreditation number, registration number
- Advanced filters: AND logic across all selected filters
- Can combine quick search with advanced filters

---

## UI IMPROVEMENTS

### Year Selector
```html
<div class="d-flex gap-2 align-items-center">
  <label class="small fw-bold text-muted mb-0">Year:</label>
  @for($y = now()->year; $y >= now()->year - 5; $y--)
    <a class="btn btn-sm {{ $activeYear == $y ? 'btn-dark' : 'btn-outline-dark' }}" 
       href="{{ request()->fullUrlWithQuery(['year' => $y]) }}">{{ $y }}</a>
  @endfor
</div>
```

### Processing Status Tabs
```html
<div class="d-flex gap-2">
  <a class="btn btn-sm {{ $activeProcessingStatus === 'all' ? 'btn-dark' : 'btn-outline-dark' }}" 
     href="{{ request()->fullUrlWithQuery(['processing_status' => 'all']) }}">All</a>
  <a class="btn btn-sm {{ $activeProcessingStatus === 'processed' ? 'btn-dark' : 'btn-outline-dark' }}" 
     href="{{ request()->fullUrlWithQuery(['processing_status' => 'processed']) }}">Processed</a>
  <a class="btn btn-sm {{ $activeProcessingStatus === 'unprocessed' ? 'btn-dark' : 'btn-outline-dark' }}" 
     href="{{ request()->fullUrlWithQuery(['processing_status' => 'unprocessed']) }}">Unprocessed</a>
</div>
```

### Advanced Filters Modal
- Clean, organized layout
- Two-column grid for better space utilization
- All form controls properly labeled
- Hidden inputs preserve year, processing_status, and application_type
- Cancel and Apply buttons

---

## NEXT STEPS

**Phase 3**: Pending Review Page Update
- Redefine query to show applications processed by Officer but not yet attended to by Accounts

**Phase 4**: Approved Page Change
- Remove from Officer dashboard
- Verify Production Dashboard has this functionality

**Phase 5**: Returned Page Update
- Rename "Rejected/Returned" to "Returned"
- Show only Registrar returns
- Exclude Accounts rejections

**Phase 6**: Records Section Structure
- Add filtering
- Add Advanced Filters modal
- Add export functionality
- Ensure only completed processes shown

**Phase 7**: Remove Rejected Page
- Remove route, view, sidebar link, controller method

**Phase 8**: System-Wide Terminology
- Replace "Journalist" with "Media Practitioner" across all views

---

## NOTES

- All changes are UI and filtering only
- No workflow state transitions modified
- Database queries compute from actual database states
- Server-side filtering implemented (not client-side)
- RBAC enforcement maintained
- Audit logging intact
- Column existence checks prevent errors on missing columns
