# ZMC Workflow Enforcement - Production Deployment Guide

## Document Information
**Version**: 1.0  
**Date**: 2026-02-25  
**System**: Zimbabwe Media Commission Integrated Registration & Accreditation System  
**Deployment Type**: Enhancement Release (Non-Breaking)

---

## Pre-Deployment Checklist

### 1. Environment Verification
- [ ] Production server accessible
- [ ] Database backup completed
- [ ] PHP version: 8.1+ verified
- [ ] Laravel version: 10.x verified
- [ ] Composer installed and updated
- [ ] Node.js and npm available (for assets)
- [ ] Storage permissions correct (755 for directories, 644 for files)
- [ ] .env file configured correctly

### 2. Database Backup
```bash
# Create full database backup
php artisan backup:run --only-db

# Or manual backup
mysqldump -u [username] -p [database_name] > backup_$(date +%Y%m%d_%H%M%S).sql

# Verify backup file exists and has content
ls -lh backup_*.sql
```

### 3. Code Preparation
```bash
# Ensure on correct branch
git status
git branch

# Pull latest changes
git pull origin main

# Verify no uncommitted changes
git status
```

### 4. Dependency Check
```bash
# Update composer dependencies
composer install --no-dev --optimize-autoloader

# Clear and rebuild autoload
composer dump-autoload
```

---

## Deployment Steps

### Step 1: Enable Maintenance Mode
```bash
# Put application in maintenance mode
php artisan down --message="System upgrade in progress. Back shortly." --retry=60

# Verify maintenance mode active
curl -I https://your-domain.com
# Should return 503 Service Unavailable
```

### Step 2: Pull Latest Code
```bash
# Navigate to application directory
cd /path/to/zmc-portal

# Stash any local changes (if any)
git stash

# Pull latest code
git pull origin main

# Verify correct commit
git log -1
```

### Step 3: Run Database Migrations
```bash
# Check migration status
php artisan migrate:status

# Run migrations (2 new migrations)
php artisan migrate --force

# Expected output:
# Migrating: 2026_02_25_073905_create_fix_requests_table
# Migrated:  2026_02_25_073905_create_fix_requests_table (XX.XXms)
# Migrating: 2026_02_25_073915_add_payment_submission_method_to_applications_table
# Migrated:  2026_02_25_073915_add_payment_submission_method_to_applications_table (XX.XXms)

# Verify migrations completed
php artisan migrate:status
```

### Step 4: Clear All Caches
```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear view cache
php artisan view:clear

# Clear compiled classes
php artisan clear-compiled

# Optimize for production
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 5: Verify Routes
```bash
# Check new routes registered
php artisan route:list --name=fix-request

# Expected output:
# GET|HEAD   staff/accreditation-officer/fix-requests
# POST       staff/accreditation-officer/fix-requests/{fixRequest}/resolve
# POST       staff/registrar/applications/{application}/send-fix-request
# GET|HEAD   staff/registrar/fix-requests
```

### Step 6: Set Permissions
```bash
# Set correct permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Verify permissions
ls -la storage/
ls -la bootstrap/cache/
```

### Step 7: Disable Maintenance Mode
```bash
# Bring application back online
php artisan up

# Verify application accessible
curl -I https://your-domain.com
# Should return 200 OK
```

---

## Post-Deployment Verification

### 1. Database Verification
```bash
# Connect to database
mysql -u [username] -p [database_name]

# Verify new table exists
SHOW TABLES LIKE 'fix_requests';

# Verify new columns exist
DESCRIBE applications;
# Should show: payment_submission_method, payment_submitted_at

# Check table structure
DESCRIBE fix_requests;

# Exit database
exit;
```

### 2. Application Health Check
```bash
# Check application status
php artisan about

# Check for errors in logs
tail -n 50 storage/logs/laravel.log

# Check queue status (if using queues)
php artisan queue:work --once
```

### 3. Browser Testing

#### Test as Registrar
1. Login as Registrar user
2. Navigate to dashboard
3. Open an application in REGISTRAR_REVIEW status
4. Verify "Fix Request" button visible
5. Click "Fix Request" button
6. Fill form and submit
7. Verify success message
8. Navigate to "Fix Requests" page
9. Verify fix request appears in list

#### Test as Accreditation Officer
1. Login as Officer user
2. Navigate to dashboard
3. Verify "Fix Requests" link in sidebar
4. Click "Fix Requests" link
5. Verify pending fix requests display
6. Click "View Application" on a fix request
7. Return to fix requests page
8. Click resolve button
9. Fill resolution form and submit
10. Verify success message
11. Verify application returned to queue

#### Test as Accounts Officer
1. Login as Accounts user
2. Navigate to dashboard
3. Verify filter section displays
4. Verify KPI badges show counts
5. Select "PayNow" from filter dropdown
6. Click Filter button
7. Verify only PayNow submissions show
8. Verify payment method column displays
9. Verify badges show correct icons
10. Click Reset button
11. Verify all applications show again

### 4. Functional Testing Checklist

**Fix Request Workflow**:
- [ ] Registrar can send fix request
- [ ] Fix request creates database record
- [ ] Application status changes to RETURNED_TO_OFFICER
- [ ] Officer sees fix request in queue
- [ ] Badge counter shows correct count
- [ ] Officer can resolve fix request
- [ ] Application returns to OFFICER_REVIEW
- [ ] Audit log captures all actions

**Payment Submission Tracking**:
- [ ] PayNow initiation sets submission method
- [ ] Proof upload sets submission method
- [ ] Waiver upload sets submission method
- [ ] Submission timestamp recorded
- [ ] Accounts dashboard filter works
- [ ] KPIs calculate correctly
- [ ] Payment method badges display
- [ ] Icons show correctly

**Existing Functionality**:
- [ ] Application submission works
- [ ] Officer approval works
- [ ] Registrar approval works
- [ ] Payment verification works
- [ ] Production workflow works
- [ ] Audit logs continue working
- [ ] User authentication works
- [ ] Role-based access enforced

---

## Rollback Procedure

If issues are encountered, follow this rollback procedure:

### Step 1: Enable Maintenance Mode
```bash
php artisan down --message="Rolling back changes. Back shortly."
```

### Step 2: Restore Database
```bash
# Rollback migrations
php artisan migrate:rollback --step=2

# Or restore from backup
mysql -u [username] -p [database_name] < backup_YYYYMMDD_HHMMSS.sql
```

### Step 3: Restore Code
```bash
# Revert to previous commit
git log --oneline -10  # Find previous commit hash
git reset --hard [previous-commit-hash]

# Or checkout previous tag
git checkout [previous-tag]
```

### Step 4: Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize
```

### Step 5: Disable Maintenance Mode
```bash
php artisan up
```

### Step 6: Verify Rollback
- Test application functionality
- Check error logs
- Verify database state

---

## Monitoring

### 1. Application Logs
```bash
# Monitor logs in real-time
tail -f storage/logs/laravel.log

# Check for errors
grep -i "error" storage/logs/laravel.log | tail -20

# Check for exceptions
grep -i "exception" storage/logs/laravel.log | tail -20
```

### 2. Database Monitoring
```sql
-- Check fix_requests table growth
SELECT COUNT(*) FROM fix_requests;

-- Check fix requests by status
SELECT status, COUNT(*) FROM fix_requests GROUP BY status;

-- Check payment submission methods
SELECT payment_submission_method, COUNT(*) 
FROM applications 
WHERE payment_submission_method IS NOT NULL 
GROUP BY payment_submission_method;

-- Check recent fix requests
SELECT * FROM fix_requests ORDER BY created_at DESC LIMIT 10;
```

### 3. Performance Monitoring
```bash
# Check response times
curl -w "@curl-format.txt" -o /dev/null -s https://your-domain.com/staff/registrar/dashboard

# Monitor database queries
# Enable query logging in .env
DB_LOG_QUERIES=true

# Check slow queries
grep "slow query" storage/logs/laravel.log
```

### 4. User Activity Monitoring
```sql
-- Check fix request activity
SELECT DATE(created_at) as date, COUNT(*) as count 
FROM fix_requests 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at);

-- Check payment submission activity
SELECT DATE(payment_submitted_at) as date, 
       payment_submission_method, 
       COUNT(*) as count 
FROM applications 
WHERE payment_submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(payment_submitted_at), payment_submission_method;
```

---

## Troubleshooting

### Issue: Fix Request Button Not Showing

**Symptoms**: Registrar cannot see "Fix Request" button

**Diagnosis**:
```bash
# Check if view file exists
ls -la resources/views/staff/registrar/show.blade.php

# Check for syntax errors
php artisan view:clear
```

**Solution**:
1. Clear view cache: `php artisan view:clear`
2. Check user role: Verify user has 'registrar' role
3. Check application status: Button only shows for REGISTRAR_REVIEW status

### Issue: Fix Request Not Creating

**Symptoms**: Form submits but no record created

**Diagnosis**:
```bash
# Check logs
tail -50 storage/logs/laravel.log

# Check database
mysql -u [username] -p [database_name]
SELECT * FROM fix_requests ORDER BY id DESC LIMIT 5;
```

**Solution**:
1. Check database connection
2. Verify table exists: `SHOW TABLES LIKE 'fix_requests';`
3. Check permissions: User must have 'registrar' role
4. Review validation errors in logs

### Issue: Payment Method Not Tracking

**Symptoms**: Payment method column shows "None"

**Diagnosis**:
```bash
# Check if column exists
mysql -u [username] -p [database_name]
DESCRIBE applications;
# Should show payment_submission_method column
```

**Solution**:
1. Run migrations: `php artisan migrate`
2. Clear cache: `php artisan cache:clear`
3. Check controller updates: Verify PaynowController and ManualPaymentController updated

### Issue: Filter Not Working

**Symptoms**: Filter dropdown doesn't filter results

**Diagnosis**:
```bash
# Check route
php artisan route:list --name=accounts.dashboard

# Check logs for errors
tail -50 storage/logs/laravel.log
```

**Solution**:
1. Clear route cache: `php artisan route:clear`
2. Clear config cache: `php artisan config:clear`
3. Verify query parameter: Check URL has `?submission_method=...`

### Issue: 500 Internal Server Error

**Symptoms**: Application returns 500 error

**Diagnosis**:
```bash
# Check error logs
tail -100 storage/logs/laravel.log

# Check PHP error log
tail -100 /var/log/php/error.log

# Check web server error log
tail -100 /var/log/nginx/error.log  # or apache2/error.log
```

**Solution**:
1. Check file permissions: `sudo chmod -R 775 storage bootstrap/cache`
2. Clear all caches: `php artisan optimize:clear`
3. Check .env configuration
4. Review error message in logs

---

## Performance Optimization

### 1. Database Indexing
```sql
-- Verify indexes exist
SHOW INDEX FROM fix_requests;
SHOW INDEX FROM applications;

-- Expected indexes on fix_requests:
-- - PRIMARY KEY (id)
-- - INDEX (application_id, status)
-- - INDEX (requested_by, status)

-- Expected indexes on applications:
-- - PRIMARY KEY (id)
-- - INDEX (status)
-- - INDEX (payment_submission_method)
```

### 2. Query Optimization
```bash
# Enable query logging
# Add to .env:
DB_LOG_QUERIES=true

# Monitor slow queries
grep "slow query" storage/logs/laravel.log

# Optimize tables
mysql -u [username] -p [database_name]
OPTIMIZE TABLE fix_requests;
OPTIMIZE TABLE applications;
```

### 3. Cache Configuration
```bash
# Use Redis for cache (recommended)
# Update .env:
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Or use file cache
CACHE_DRIVER=file
SESSION_DRIVER=file
```

---

## Security Checklist

### 1. Access Control
- [ ] RBAC middleware enforced on all new routes
- [ ] CSRF protection enabled on all forms
- [ ] Input validation on all form submissions
- [ ] SQL injection prevention (using Eloquent)
- [ ] XSS prevention (Blade escaping)

### 2. Data Protection
- [ ] Sensitive data encrypted in database
- [ ] Audit logs capture all actions
- [ ] User actions tracked with IP and user agent
- [ ] File uploads validated and sanitized

### 3. Authentication
- [ ] Session timeout configured
- [ ] Password requirements enforced
- [ ] Failed login attempts tracked
- [ ] Two-factor authentication available (if enabled)

---

## Support Information

### Technical Contacts
- **System Administrator**: [Name] - [Email] - [Phone]
- **Database Administrator**: [Name] - [Email] - [Phone]
- **Development Team Lead**: [Name] - [Email] - [Phone]

### Escalation Path
1. **Level 1**: IT Support Desk
2. **Level 2**: System Administrator
3. **Level 3**: Development Team
4. **Level 4**: Technical Architect

### Documentation Links
- System Documentation: `/docs/system-overview.md`
- API Documentation: `/docs/api-reference.md`
- User Guides: `/docs/user-guides/`
- Troubleshooting: `/docs/troubleshooting.md`

---

## Post-Deployment Tasks

### Immediate (Within 24 hours)
- [ ] Monitor error logs continuously
- [ ] Check database performance
- [ ] Verify all user roles can access their features
- [ ] Collect initial user feedback
- [ ] Document any issues encountered

### Short-term (Within 1 week)
- [ ] Analyze fix request usage patterns
- [ ] Review payment submission method distribution
- [ ] Check audit log completeness
- [ ] Optimize slow queries if any
- [ ] Conduct user training sessions

### Long-term (Within 1 month)
- [ ] Generate usage reports
- [ ] Analyze workflow efficiency improvements
- [ ] Plan Phase 4 enhancements
- [ ] Review system performance metrics
- [ ] Update documentation based on feedback

---

## Success Criteria

### Technical Success
- [ ] All migrations completed successfully
- [ ] No errors in application logs
- [ ] All routes accessible
- [ ] Database queries performing well
- [ ] No security vulnerabilities introduced

### Functional Success
- [ ] Fix request workflow operational
- [ ] Payment tracking functional
- [ ] Filters working correctly
- [ ] Audit logs capturing all actions
- [ ] Existing functionality preserved

### User Success
- [ ] Registrars can send fix requests
- [ ] Officers can resolve fix requests
- [ ] Accounts can filter by payment method
- [ ] All roles report positive experience
- [ ] No workflow disruptions

---

## Appendix

### A. Migration Files
1. `2026_02_25_073905_create_fix_requests_table.php`
2. `2026_02_25_073915_add_payment_submission_method_to_applications_table.php`

### B. New Routes
```
GET    /staff/accreditation-officer/fix-requests
POST   /staff/accreditation-officer/fix-requests/{fixRequest}/resolve
GET    /staff/registrar/fix-requests
POST   /staff/registrar/applications/{application}/send-fix-request
```

### C. Modified Controllers
1. `AccreditationOfficerController.php`
2. `RegistrarController.php`
3. `AccountsPaymentsController.php`
4. `PaynowController.php`
5. `ManualPaymentController.php`

### D. New Views
1. `resources/views/staff/officer/fix_requests.blade.php`
2. `resources/views/staff/registrar/fix_requests.blade.php`

### E. Modified Views
1. `resources/views/layouts/sidebar_staff.blade.php`
2. `resources/views/staff/registrar/show.blade.php`
3. `resources/views/staff/accounts/dashboard.blade.php`

---

**Deployment Prepared By**: Kiro AI  
**Deployment Date**: 2026-02-25  
**Document Version**: 1.0  
**Status**: Ready for Production ✅
