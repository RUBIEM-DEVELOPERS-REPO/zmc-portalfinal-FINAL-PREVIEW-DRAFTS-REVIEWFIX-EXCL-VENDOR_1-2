# Portal Requirements Display - Requirements Document

## Overview
Display clear, always-available "Requirements" on both applicant portals:
1. Media Practitioner Portal → Accreditation Requirements
2. Media House Portal → Registration Requirements

## Goals
- Provide structured, accessible requirements information
- Enable CMS-style content management by authorized staff
- Maintain audit trail of all changes
- Ensure mobile-friendly, printable layouts
- Support dynamic updates without code deployment

## Scope

### A) Media Practitioner Portal - Accreditation Requirements

#### Dashboard Widget
- Title: "Accreditation Requirements"
- Location: Above application list on dashboard
- Content: Concise checklist (top 5-6 items)
- Action: "View full requirements" link

#### Full Requirements Page
Must include:
1. **Who can apply**
   - Local practitioners
   - Foreign practitioners
   - Temporary accreditation
   - New/Renewal/Replacement types

2. **Step-by-step process**
   - Submit application
   - Review by officer
   - Payment processing
   - Card production

3. **Mandatory rules**
   - No blank fields (use N/A where applicable)
   - All required documents must be uploaded

4. **Required documents**
   - Certified copy of national ID (local) OR passport (foreign)
   - Employment letter (if employed)
   - Additional annexures as required

5. **Document upload rules**
   - Allowed file types: PDF, JPG, PNG
   - Maximum file size: 5MB per file
   - Clear naming conventions

6. **Processing time**
   - Standard processing: 14 working days
   - Express processing: 7 working days (if available)

7. **Category codes**
   - Display journalist/media practitioner categories in table format
   - Code, Description, Requirements

8. **Fees & Payment**
   - Application fee
   - Card production fee
   - Payment methods (PayNow, Manual proof upload)

#### Content Sections
- Before you start
- Documents to upload
- Choose your category
- Fees & Payment options
- What happens after you submit
- Help & Contacts

### B) Media House Portal - Registration Requirements

#### Dashboard Widget
- Title: "Registration Requirements"
- Location: Prominent position on dashboard
- Content: Concise checklist (top 5-6 items)
- Action: "View full requirements" link

#### Full Requirements Page
Must include:
1. **Required Documents Checklist**
   - Projected cash flow statement (3 years)
   - Projected balance sheet (3 years)
   - Editorial charter
   - Code of ethics
   - Code of conduct for employees
   - Market analysis
   - Certified IDs for directors
   - In-house style book
   - Dummy copy of publication
   - Mission statement
   - Certificate of incorporation
   - Memorandum of association
   - Additional statutory/AP1-required annexures

2. **Company Information**
   - Business registration details
   - Director information
   - Physical address requirements

3. **Fees & Payment**
   - Application fee
   - Registration fee
   - Payment options (PayNow, Manual proof, Waiver)

4. **Processing Steps**
   - Application submission
   - Document verification
   - Committee review
   - Payment processing
   - Certificate issuance

5. **Mass Media Categories**
   - Display categories in table format
   - Code, Description, Requirements

#### Content Sections
- Documents you must prepare
- Company information needed
- Fees & payment
- After submission
- Help & contacts

## UX Requirements (Both Portals)

### Visibility
1. Dashboard summary widget
2. Dedicated full page
3. Optional contextual reminders on forms

### Dynamic Content
- Read from database on every load
- Safe caching with invalidation
- Immediate reflection of staff updates

### Attachments Guidance
For each required document:
- "Required" badge
- Accepted file types
- Maximum file size
- Example/template link (optional)

### Accessibility
- Clear heading hierarchy
- Printable view option
- Mobile-friendly responsive layout
- Screen reader compatible
- High contrast support

## Data Model

### Requirements Configuration Table
```
portal_requirements
- id
- portal_type (enum: 'accreditation', 'registration')
- section_key (string: 'before_start', 'documents', 'categories', etc.)
- section_title (string)
- section_order (integer)
- content (json)
- is_active (boolean)
- created_at
- updated_at
```

### Requirements Items Table
```
portal_requirement_items
- id
- requirement_id (foreign key)
- item_type (enum: 'document', 'information', 'fee', 'step')
- title (string)
- description (text)
- is_required (boolean)
- file_types (json: ['pdf', 'jpg', 'png'])
- max_file_size (integer: in KB)
- order (integer)
- metadata (json: additional config)
- created_at
- updated_at
```

### Category Codes Table
```
application_categories
- id
- portal_type (enum: 'accreditation', 'registration')
- code (string)
- name (string)
- description (text)
- requirements (text)
- is_active (boolean)
- order (integer)
- created_at
- updated_at
```

### Audit Log Table
```
portal_requirements_audit
- id
- requirement_id (foreign key)
- user_id (foreign key)
- action (enum: 'created', 'updated', 'deleted')
- old_value (json)
- new_value (json)
- ip_address (string)
- user_agent (string)
- created_at
```

## Security & Audit

### Authorization
- Only Super Admin and IT Admin can edit requirements
- Registrar can view edit history
- Applicants have read-only access

### Audit Trail
Log all changes:
- Who edited (user_id, name, role)
- What changed (field-level diff)
- When (timestamp)
- Portal affected (accreditation/registration)
- IP address and user agent

### Validation
- Content sanitization (XSS prevention)
- File type validation
- Size limit enforcement
- Required field validation

## API Endpoints

### Public (Applicant) Endpoints
```
GET /api/portal/requirements/accreditation
GET /api/portal/requirements/registration
GET /api/portal/categories/accreditation
GET /api/portal/categories/registration
```

### Admin Endpoints (Authenticated + Authorized)
```
GET /admin/portal-requirements
GET /admin/portal-requirements/{id}
POST /admin/portal-requirements
PUT /admin/portal-requirements/{id}
DELETE /admin/portal-requirements/{id}
GET /admin/portal-requirements/{id}/history
GET /admin/portal-requirements/audit-log
```

## Implementation Phases

### Phase 1: Database & Models
- Create migrations
- Create models with relationships
- Seed initial data

### Phase 2: Admin Interface
- Requirements management CRUD
- Category management
- Audit log viewer
- Version comparison

### Phase 3: Portal Display
- Dashboard widgets
- Full requirements pages
- Responsive layouts
- Print styles

### Phase 4: Testing & Refinement
- Unit tests
- Integration tests
- User acceptance testing
- Performance optimization

## Success Criteria
- ✅ Requirements visible on both portals
- ✅ Dashboard widgets show summary
- ✅ Full pages show complete details
- ✅ Admin can edit without code deployment
- ✅ All changes are audited
- ✅ Mobile-friendly and printable
- ✅ Load time < 2 seconds
- ✅ Accessible (WCAG AA)

## Non-Functional Requirements
- Performance: Page load < 2s
- Availability: 99.9% uptime
- Security: Role-based access control
- Scalability: Support 1000+ concurrent users
- Maintainability: Clear code documentation
