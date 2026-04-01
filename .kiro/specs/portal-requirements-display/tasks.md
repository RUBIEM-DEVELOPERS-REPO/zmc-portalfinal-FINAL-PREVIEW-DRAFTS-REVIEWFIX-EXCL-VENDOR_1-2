# Portal Requirements Display - Implementation Tasks

## Phase 1: Database & Models (Priority: HIGH)

### Task 1.1: Create Migrations
- [ ] Create `portal_requirements` table migration
- [ ] Create `portal_requirement_items` table migration
- [ ] Create `application_categories` table migration
- [ ] Create `portal_requirements_audit` table migration
- [ ] Add indexes for performance
- [ ] Test migrations up/down

### Task 1.2: Create Models
- [ ] Create `PortalRequirement` model
  - [ ] Define fillable fields
  - [ ] Add relationships (hasMany items)
  - [ ] Add scopes (byPortalType, active)
  - [ ] Add accessors/mutators for JSON fields
- [ ] Create `PortalRequirementItem` model
  - [ ] Define fillable fields
  - [ ] Add relationship (belongsTo requirement)
  - [ ] Add scopes
- [ ] Create `ApplicationCategory` model
  - [ ] Define fillable fields
  - [ ] Add scopes (byPortalType, active)
- [ ] Create `PortalRequirementAudit` model
  - [ ] Define fillable fields
  - [ ] Add relationships (belongsTo user, requirement)

### Task 1.3: Create Seeders
- [ ] Seed accreditation requirements
  - [ ] Before you start section
  - [ ] Documents section
  - [ ] Categories section
  - [ ] Fees section
  - [ ] Process section
  - [ ] Help section
- [ ] Seed registration requirements
  - [ ] Documents section
  - [ ] Company info section
  - [ ] Fees section
  - [ ] Process section
  - [ ] Help section
- [ ] Seed accreditation categories (J01, J02, etc.)
- [ ] Seed registration categories (mass media types)

## Phase 2: Service Layer (Priority: HIGH)

### Task 2.1: Create RequirementsService
- [ ] Implement `getRequirementsByPortalType()`
- [ ] Implement `getDashboardSummary()`
- [ ] Implement `getCategoriesByPortalType()`
- [ ] Implement `updateRequirement()` with audit logging
- [ ] Implement `getAuditHistory()`
- [ ] Implement cache management methods
- [ ] Add unit tests

### Task 2.2: Create RequirementsAuditService
- [ ] Implement `logChange()` method
- [ ] Implement `getHistory()` method
- [ ] Implement `compareVersions()` method
- [ ] Implement `formatDiff()` method
- [ ] Add unit tests

## Phase 3: Portal Display (Priority: HIGH)

### Task 3.1: Media Practitioner Portal - Dashboard Widget
- [ ] Create dashboard widget component
- [ ] Fetch summary data from service
- [ ] Display top 5-6 requirements
- [ ] Add "View full requirements" link
- [ ] Style with yellow/black theme
- [ ] Test responsive layout
- [ ] Test on mobile devices

### Task 3.2: Media Practitioner Portal - Full Page
- [ ] Create requirements page route
- [ ] Create requirements controller method
- [ ] Create requirements blade view
- [ ] Implement sections:
  - [ ] Before you start
  - [ ] Documents to upload (with file type/size badges)
  - [ ] Choose your category (table display)
  - [ ] Fees & payment
  - [ ] What happens after submit
  - [ ] Help & contacts
- [ ] Add print stylesheet
- [ ] Add download checklist button
- [ ] Test accessibility
- [ ] Test responsive layout

### Task 3.3: Media House Portal - Dashboard Widget
- [ ] Create dashboard widget component
- [ ] Fetch summary data from service
- [ ] Display top 5-6 requirements
- [ ] Add "View full requirements" link
- [ ] Style with yellow/black theme
- [ ] Test responsive layout

### Task 3.4: Media House Portal - Full Page
- [ ] Create requirements page route
- [ ] Create requirements controller method
- [ ] Create requirements blade view
- [ ] Implement sections:
  - [ ] Documents you must prepare (checklist)
  - [ ] Company information needed
  - [ ] Fees & payment
  - [ ] After submission
  - [ ] Help & contacts
- [ ] Add print stylesheet
- [ ] Add download checklist button
- [ ] Test accessibility
- [ ] Test responsive layout

## Phase 4: Admin Interface (Priority: MEDIUM)

### Task 4.1: Requirements Management
- [ ] Create admin routes (resource controller)
- [ ] Create `AdminRequirementsController`
  - [ ] index() - list all requirements
  - [ ] create() - show create form
  - [ ] store() - save new requirement
  - [ ] edit() - show edit form
  - [ ] update() - save changes with audit
  - [ ] destroy() - soft delete
- [ ] Create admin views:
  - [ ] Index page with filters
  - [ ] Create/Edit form with JSON editor
  - [ ] Preview panel
- [ ] Add authorization middleware
- [ ] Add validation rules
- [ ] Test CRUD operations

### Task 4.2: Category Management
- [ ] Create admin routes
- [ ] Create `AdminCategoriesController`
  - [ ] index() - list categories
  - [ ] create() - show create form
  - [ ] store() - save new category
  - [ ] edit() - show edit form
  - [ ] update() - save changes
  - [ ] destroy() - soft delete
- [ ] Create admin views
- [ ] Add authorization
- [ ] Add validation
- [ ] Test CRUD operations

### Task 4.3: Audit Log Viewer
- [ ] Create audit log route
- [ ] Create audit log controller method
- [ ] Create audit log view
  - [ ] List all changes
  - [ ] Filter by user, date, action
  - [ ] Show diff comparison
  - [ ] Export to CSV
- [ ] Add pagination
- [ ] Test filtering

### Task 4.4: Version Comparison
- [ ] Create comparison route
- [ ] Create comparison controller method
- [ ] Create comparison view
  - [ ] Side-by-side diff
  - [ ] Highlight changes
  - [ ] Restore previous version option
- [ ] Test comparison logic

## Phase 5: API Endpoints (Priority: MEDIUM)

### Task 5.1: Public API
- [ ] Create API routes
- [ ] Create `RequirementsApiController`
  - [ ] getAccreditationRequirements()
  - [ ] getRegistrationRequirements()
  - [ ] getAccreditationCategories()
  - [ ] getRegistrationCategories()
- [ ] Add rate limiting
- [ ] Add caching
- [ ] Create API documentation
- [ ] Test endpoints

### Task 5.2: Admin API
- [ ] Create admin API routes
- [ ] Add authentication middleware
- [ ] Add authorization checks
- [ ] Implement CRUD endpoints
- [ ] Implement audit endpoints
- [ ] Test with Postman/Insomnia

## Phase 6: Security & Authorization (Priority: HIGH)

### Task 6.1: Policies
- [ ] Create `PortalRequirementPolicy`
  - [ ] viewAny() - list requirements
  - [ ] view() - view single requirement
  - [ ] create() - create new requirement
  - [ ] update() - update requirement
  - [ ] delete() - delete requirement
  - [ ] viewHistory() - view audit log
- [ ] Register policy in AuthServiceProvider
- [ ] Test policy methods

### Task 6.2: Middleware
- [ ] Create `RequirementsAdminMiddleware`
- [ ] Add role checks (super_admin, it_admin)
- [ ] Add IP whitelist (optional)
- [ ] Test middleware

### Task 6.3: Input Sanitization
- [ ] Implement HTML sanitizer for content fields
- [ ] Validate JSON structure
- [ ] Validate file types
- [ ] Validate file sizes
- [ ] Test with malicious input

## Phase 7: Caching (Priority: MEDIUM)

### Task 7.1: Implement Caching
- [ ] Add cache layer in service
- [ ] Define cache keys
- [ ] Set TTL (1 hour)
- [ ] Implement cache tags
- [ ] Test cache hits/misses

### Task 7.2: Cache Invalidation
- [ ] Invalidate on requirement update
- [ ] Invalidate on category update
- [ ] Add manual flush in admin
- [ ] Test invalidation logic

## Phase 8: Testing (Priority: HIGH)

### Task 8.1: Unit Tests
- [ ] Test PortalRequirement model
- [ ] Test PortalRequirementItem model
- [ ] Test ApplicationCategory model
- [ ] Test RequirementsService
- [ ] Test RequirementsAuditService
- [ ] Achieve 80%+ code coverage

### Task 8.2: Feature Tests
- [ ] Test portal requirements display
- [ ] Test admin CRUD operations
- [ ] Test audit logging
- [ ] Test authorization
- [ ] Test caching behavior

### Task 8.3: Browser Tests
- [ ] Test dashboard widget display
- [ ] Test full requirements page
- [ ] Test print functionality
- [ ] Test mobile responsiveness
- [ ] Test accessibility with screen reader

## Phase 9: Documentation (Priority: MEDIUM)

### Task 9.1: User Documentation
- [ ] Create admin user guide
  - [ ] How to edit requirements
  - [ ] How to manage categories
  - [ ] How to view audit logs
- [ ] Create applicant guide
  - [ ] How to view requirements
  - [ ] How to print checklist
- [ ] Add inline help text

### Task 9.2: Developer Documentation
- [ ] Document database schema
- [ ] Document API endpoints
- [ ] Document service methods
- [ ] Add code comments
- [ ] Create architecture diagram

## Phase 10: Deployment (Priority: HIGH)

### Task 10.1: Pre-Deployment
- [ ] Run all tests
- [ ] Check code quality (PHPStan, Psalm)
- [ ] Review security checklist
- [ ] Backup database
- [ ] Create rollback plan

### Task 10.2: Deployment
- [ ] Run migrations
- [ ] Run seeders
- [ ] Clear cache
- [ ] Test on staging
- [ ] Deploy to production
- [ ] Verify deployment

### Task 10.3: Post-Deployment
- [ ] Monitor error logs
- [ ] Check performance metrics
- [ ] Verify cache behavior
- [ ] Test user flows
- [ ] Gather user feedback

## Phase 11: Optimization (Priority: LOW)

### Task 11.1: Performance
- [ ] Optimize database queries
- [ ] Add database indexes
- [ ] Implement lazy loading
- [ ] Optimize images
- [ ] Minify CSS/JS

### Task 11.2: SEO
- [ ] Add meta tags
- [ ] Add structured data
- [ ] Optimize page titles
- [ ] Add canonical URLs

## Success Metrics

### Functional
- [ ] Requirements visible on both portals
- [ ] Dashboard widgets functional
- [ ] Full pages display correctly
- [ ] Admin can edit without code changes
- [ ] All changes audited
- [ ] Mobile-friendly
- [ ] Printable

### Performance
- [ ] Page load < 2 seconds
- [ ] Cache hit rate > 80%
- [ ] Zero critical errors
- [ ] 99.9% uptime

### Quality
- [ ] Code coverage > 80%
- [ ] Zero security vulnerabilities
- [ ] WCAG AA compliant
- [ ] Cross-browser compatible

## Timeline Estimate

- Phase 1: 2 days
- Phase 2: 2 days
- Phase 3: 3 days
- Phase 4: 3 days
- Phase 5: 1 day
- Phase 6: 1 day
- Phase 7: 1 day
- Phase 8: 2 days
- Phase 9: 1 day
- Phase 10: 1 day
- Phase 11: 1 day

**Total: ~18 days** (with buffer for testing and refinement)
