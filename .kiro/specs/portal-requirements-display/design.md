# Portal Requirements Display - Design Document

## Architecture Overview

### Component Structure
```
┌─────────────────────────────────────────┐
│         Applicant Portal Layer          │
│  ┌─────────────┐    ┌────────────────┐ │
│  │  Dashboard  │    │  Requirements  │ │
│  │   Widget    │───▶│   Full Page    │ │
│  └─────────────┘    └────────────────┘ │
└─────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│        Requirements Service Layer       │
│  ┌──────────────────────────────────┐  │
│  │  RequirementsService             │  │
│  │  - getByPortalType()             │  │
│  │  - getCategoriesByPortalType()   │  │
│  │  - formatForDisplay()            │  │
│  └──────────────────────────────────┘  │
└─────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│          Data Access Layer              │
│  ┌──────────────┐  ┌─────────────────┐ │
│  │PortalRequire-│  │ApplicationCate- │ │
│  │ment Model    │  │gory Model       │ │
│  └──────────────┘  └─────────────────┘ │
└─────────────────────────────────────────┘
                 │
                 ▼
┌─────────────────────────────────────────┐
│            Database Layer               │
│  portal_requirements                    │
│  portal_requirement_items               │
│  application_categories                 │
│  portal_requirements_audit              │
└─────────────────────────────────────────┘
```

### Admin Management Structure
```
┌─────────────────────────────────────────┐
│         Admin Interface Layer           │
│  ┌─────────────┐    ┌────────────────┐ │
│  │Requirements │    │   Category     │ │
│  │   Manager   │    │   Manager      │ │
│  └─────────────┘    └────────────────┘ │
│  ┌─────────────────────────────────┐   │
│  │      Audit Log Viewer           │   │
│  └─────────────────────────────────┘   │
└─────────────────────────────────────────┘
```

## Database Schema

### portal_requirements
```sql
CREATE TABLE portal_requirements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    portal_type ENUM('accreditation', 'registration') NOT NULL,
    section_key VARCHAR(100) NOT NULL,
    section_title VARCHAR(255) NOT NULL,
    section_order INT NOT NULL DEFAULT 0,
    content JSON NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_portal_type (portal_type),
    INDEX idx_section_key (section_key),
    INDEX idx_is_active (is_active),
    UNIQUE KEY unique_portal_section (portal_type, section_key)
);
```

### portal_requirement_items
```sql
CREATE TABLE portal_requirement_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    requirement_id BIGINT UNSIGNED NOT NULL,
    item_type ENUM('document', 'information', 'fee', 'step') NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_required BOOLEAN DEFAULT TRUE,
    file_types JSON,
    max_file_size INT COMMENT 'Size in KB',
    item_order INT NOT NULL DEFAULT 0,
    metadata JSON,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (requirement_id) REFERENCES portal_requirements(id) ON DELETE CASCADE,
    INDEX idx_requirement_id (requirement_id),
    INDEX idx_item_type (item_type),
    INDEX idx_is_required (is_required)
);
```

### application_categories
```sql
CREATE TABLE application_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    portal_type ENUM('accreditation', 'registration') NOT NULL,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    requirements TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    category_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_portal_type (portal_type),
    INDEX idx_code (code),
    INDEX idx_is_active (is_active),
    UNIQUE KEY unique_portal_code (portal_type, code)
);
```

### portal_requirements_audit
```sql
CREATE TABLE portal_requirements_audit (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    requirement_id BIGINT UNSIGNED,
    user_id BIGINT UNSIGNED NOT NULL,
    action ENUM('created', 'updated', 'deleted') NOT NULL,
    old_value JSON,
    new_value JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_requirement_id (requirement_id),
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);
```

## Content Structure (JSON Schema)

### Section Content Schema
```json
{
  "summary": "Brief overview for dashboard widget",
  "introduction": "Full introduction text",
  "highlights": [
    "Key point 1",
    "Key point 2",
    "Key point 3"
  ],
  "details": {
    "subsection1": {
      "title": "Subsection Title",
      "content": "Detailed content",
      "items": ["item1", "item2"]
    }
  },
  "help_text": "Additional guidance",
  "links": [
    {
      "text": "Link text",
      "url": "/path/to/resource"
    }
  ]
}
```

### Item Metadata Schema
```json
{
  "example_url": "/downloads/example.pdf",
  "template_url": "/downloads/template.docx",
  "help_text": "Additional guidance for this item",
  "validation_rules": {
    "min_pages": 1,
    "max_pages": 10,
    "must_be_certified": true
  }
}
```

## UI Design Specifications

### Dashboard Widget Layout
```
┌─────────────────────────────────────────────┐
│ 📋 Accreditation Requirements               │
├─────────────────────────────────────────────┤
│ Before you apply, make sure you have:      │
│                                             │
│ ✓ Valid national ID or passport            │
│ ✓ Recent passport photo                    │
│ ✓ Employment letter (if employed)          │
│ ✓ Payment ready (USD $XX)                  │
│ ✓ All documents in PDF format              │
│                                             │
│ [View Full Requirements →]                  │
└─────────────────────────────────────────────┘
```

### Full Requirements Page Layout
```
┌─────────────────────────────────────────────┐
│ Header: Accreditation Requirements          │
│ [Print] [Download Checklist]                │
├─────────────────────────────────────────────┤
│                                             │
│ 📌 Before You Start                         │
│ ├─ Who can apply                            │
│ ├─ What you need                            │
│ └─ Processing time                          │
│                                             │
│ 📄 Documents to Upload                      │
│ ├─ National ID/Passport [Required]          │
│ │  └─ PDF, JPG, PNG | Max 5MB              │
│ ├─ Passport Photo [Required]                │
│ └─ Employment Letter [If Employed]          │
│                                             │
│ 🏷️ Choose Your Category                     │
│ ┌─────────────────────────────────────┐    │
│ │ Code │ Category      │ Description  │    │
│ ├──────┼───────────────┼──────────────┤    │
│ │ J01  │ Journalist    │ Print media  │    │
│ │ J02  │ Broadcaster   │ Radio/TV     │    │
│ └─────────────────────────────────────┘    │
│                                             │
│ 💰 Fees & Payment                           │
│ ├─ Application fee: USD $XX                 │
│ ├─ Card production: USD $XX                 │
│ └─ Payment methods: PayNow, Manual          │
│                                             │
│ ⏱️ What Happens After You Submit            │
│ 1. Application review (3-5 days)            │
│ 2. Payment processing (1-2 days)            │
│ 3. Card production (7-10 days)              │
│                                             │
│ 📞 Help & Contacts                          │
│ └─ Email: info@zmc.co.zw                    │
│                                             │
└─────────────────────────────────────────────┘
```

## Service Layer Design

### RequirementsService
```php
class RequirementsService
{
    public function getRequirementsByPortalType(string $portalType): Collection
    {
        // Fetch active requirements ordered by section_order
        // Include related items
        // Format for display
    }
    
    public function getDashboardSummary(string $portalType): array
    {
        // Get top 5-6 key requirements
        // Format as checklist
    }
    
    public function getCategoriesByPortalType(string $portalType): Collection
    {
        // Fetch active categories
        // Order by category_order
    }
    
    public function updateRequirement(int $id, array $data, User $user): PortalRequirement
    {
        // Update requirement
        // Log audit trail
        // Clear cache
    }
    
    public function getAuditHistory(int $requirementId): Collection
    {
        // Fetch audit logs
        // Include user information
        // Format diffs
    }
}
```

## Controller Design

### Portal Controllers
```php
// For applicants
class RequirementsController extends Controller
{
    public function accreditation()
    {
        // Show accreditation requirements page
    }
    
    public function registration()
    {
        // Show registration requirements page
    }
}
```

### Admin Controllers
```php
// For staff management
class AdminRequirementsController extends Controller
{
    public function index()
    {
        // List all requirements
    }
    
    public function edit($id)
    {
        // Edit requirement form
    }
    
    public function update(Request $request, $id)
    {
        // Update requirement
        // Log audit
    }
    
    public function history($id)
    {
        // Show audit history
    }
}
```

## Routes Design

### Public Routes
```php
Route::middleware(['auth'])->group(function () {
    // Media Practitioner Portal
    Route::get('/portal/requirements', [RequirementsController::class, 'accreditation'])
        ->name('portal.requirements');
    
    // Media House Portal
    Route::get('/mediahouse/requirements', [RequirementsController::class, 'registration'])
        ->name('mediahouse.requirements');
});
```

### Admin Routes
```php
Route::middleware(['auth', 'role:super_admin|it_admin'])->prefix('admin')->group(function () {
    Route::resource('portal-requirements', AdminRequirementsController::class);
    Route::get('portal-requirements/{id}/history', [AdminRequirementsController::class, 'history'])
        ->name('admin.portal-requirements.history');
    Route::resource('application-categories', AdminCategoriesController::class);
});
```

## Caching Strategy

### Cache Keys
```
portal_requirements:accreditation
portal_requirements:registration
portal_categories:accreditation
portal_categories:registration
```

### Cache Invalidation
- On requirement update
- On category update
- Manual flush option in admin
- TTL: 1 hour (with tag-based invalidation)

## Security Considerations

### Authorization
- Middleware: `role:super_admin|it_admin` for admin routes
- Policy: `PortalRequirementPolicy` for fine-grained control
- Audit all changes with user context

### Input Validation
- Sanitize HTML content (allow safe tags only)
- Validate JSON structure
- File type whitelist
- Size limit enforcement

### XSS Prevention
- Use Blade's `{{ }}` for output
- Sanitize rich text content
- CSP headers

## Performance Optimization

### Database
- Indexes on frequently queried columns
- Eager loading of relationships
- Query result caching

### Frontend
- Lazy load images
- Minify CSS/JS
- CDN for static assets
- Service worker for offline access

## Accessibility Features

### WCAG AA Compliance
- Semantic HTML structure
- ARIA labels where needed
- Keyboard navigation support
- Screen reader compatibility
- High contrast mode support
- Focus indicators
- Skip links

### Print Styles
- Clean print layout
- Page break optimization
- Remove navigation elements
- Optimize for black & white printing

## Mobile Responsiveness

### Breakpoints
- Mobile: < 768px
- Tablet: 768px - 1024px
- Desktop: > 1024px

### Mobile Optimizations
- Collapsible sections
- Touch-friendly buttons
- Optimized table display
- Reduced image sizes

## Testing Strategy

### Unit Tests
- Model methods
- Service layer logic
- Validation rules

### Integration Tests
- Controller actions
- Database transactions
- Cache behavior

### E2E Tests
- User flows
- Admin workflows
- Cross-browser testing

## Monitoring & Analytics

### Metrics to Track
- Page views (requirements pages)
- Time on page
- Print/download actions
- Admin edit frequency
- Error rates

### Logging
- All admin actions
- Failed validations
- Cache misses
- Performance bottlenecks
