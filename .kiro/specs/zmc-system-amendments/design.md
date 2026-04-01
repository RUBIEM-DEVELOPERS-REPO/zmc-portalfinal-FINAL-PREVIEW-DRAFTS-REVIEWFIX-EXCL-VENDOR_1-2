# ZMC System Amendments - Design Document

## Data Model Updates

### 1. Reminders System

```sql
-- reminders table
CREATE TABLE reminders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_by BIGINT UNSIGNED NOT NULL,
    target_type ENUM('media_practitioner', 'media_house', 'bulk') NOT NULL,
    target_id BIGINT UNSIGNED NULL, -- NULL for bulk
    bulk_criteria JSON NULL, -- For bulk: {status: 'awaiting_payment', etc}
    priority ENUM('high', 'normal') DEFAULT 'normal',
    reminder_type VARCHAR(50) NOT NULL, -- payment_outstanding, missing_documents, etc
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    related_application_id BIGINT UNSIGNED NULL,
    link_url VARCHAR(500) NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (related_application_id) REFERENCES applications(id),
    INDEX idx_target (target_type, target_id),
    INDEX idx_expires (expires_at),
    INDEX idx_priority (priority)
);

-- reminder_reads table
CREATE TABLE reminder_reads (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reminder_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    read_at TIMESTAMP NULL,
    acknowledged_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reminder_id) REFERENCES reminders(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_reminder_user (reminder_id, user_id),
    INDEX idx_user_unread (user_id, read_at),
    INDEX idx_acknowledged (acknowledged_at)
);
```

### 2. Login Activity Tracking

```sql
-- login_activities table
CREATE TABLE login_activities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NOT NULL,
    device_identifier VARCHAR(255) NULL,
    operating_system VARCHAR(100) NULL,
    browser_name VARCHAR(100) NULL,
    browser_version VARCHAR(50) NULL,
    login_at TIMESTAMP NOT NULL,
    logout_at TIMESTAMP NULL,
    session_duration INT NULL, -- in seconds
    login_successful BOOLEAN DEFAULT TRUE,
    failure_reason VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_user_login (user_id, login_at),
    INDEX idx_ip (ip_address),
    INDEX idx_login_time (login_at)
);
```

### 3. Cash Payments

```sql
-- cash_payments table
CREATE TABLE cash_payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id BIGINT UNSIGNED NOT NULL,
    receipt_number VARCHAR(100) NOT NULL UNIQUE,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    recorded_by BIGINT UNSIGNED NOT NULL,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    status ENUM('pending', 'verified', 'voided') DEFAULT 'pending',
    void_reason TEXT NULL,
    voided_by BIGINT UNSIGNED NULL,
    voided_at TIMESTAMP NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id),
    FOREIGN KEY (recorded_by) REFERENCES users(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    FOREIGN KEY (voided_by) REFERENCES users(id),
    INDEX idx_application (application_id),
    INDEX idx_receipt (receipt_number),
    INDEX idx_status (status)
);
```

### 4. Physical Forms Intake

```sql
-- physical_intakes table
CREATE TABLE physical_intakes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accreditation_number VARCHAR(50) NULL,
    registration_number VARCHAR(50) NULL,
    intake_type ENUM('accreditation', 'registration') NOT NULL,
    applicant_name VARCHAR(255) NOT NULL,
    receipt_number VARCHAR(100) NOT NULL,
    processed_by BIGINT UNSIGNED NOT NULL,
    confirmed_at TIMESTAMP NOT NULL,
    application_id BIGINT UNSIGNED NULL, -- Created application
    production_record_id BIGINT UNSIGNED NULL,
    status ENUM('pending', 'confirmed', 'in_production', 'completed') DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (processed_by) REFERENCES users(id),
    FOREIGN KEY (application_id) REFERENCES applications(id),
    INDEX idx_numbers (accreditation_number, registration_number),
    INDEX idx_status (status)
);
```

### 5. Card/Certificate Designer

```sql
-- design_templates table
CREATE TABLE design_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    template_type ENUM('accreditation_card', 'registration_certificate') NOT NULL,
    template_name VARCHAR(255) NOT NULL,
    version VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    background_image_path VARCHAR(500) NULL,
    layout_config JSON NOT NULL, -- Field positions, fonts, sizes, etc
    is_active BOOLEAN DEFAULT FALSE,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_type_active (template_type, is_active),
    INDEX idx_year (year)
);

-- print_logs table
CREATE TABLE print_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    record_type ENUM('accreditation', 'registration') NOT NULL,
    record_id BIGINT UNSIGNED NOT NULL,
    template_id BIGINT UNSIGNED NOT NULL,
    printed_by BIGINT UNSIGNED NOT NULL,
    printer_name VARCHAR(255) NULL,
    print_count INT DEFAULT 1,
    printed_at TIMESTAMP NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES design_templates(id),
    FOREIGN KEY (printed_by) REFERENCES users(id),
    INDEX idx_record (record_type, record_id),
    INDEX idx_printed_by (printed_by),
    INDEX idx_printed_at (printed_at)
);
```

### 6. Profile Enhancements

```sql
-- Add to users table
ALTER TABLE users ADD COLUMN national_id_number VARCHAR(50) NULL AFTER email;
ALTER TABLE users ADD COLUMN passport_number VARCHAR(50) NULL AFTER national_id_number;
ALTER TABLE users ADD COLUMN phone_number_2 VARCHAR(20) NULL AFTER phone_number;
ALTER TABLE users ADD COLUMN theme_preference ENUM('light', 'dark') DEFAULT 'light';

-- Add to media_house_profiles table
ALTER TABLE media_house_profiles ADD COLUMN facebook_url VARCHAR(500) NULL;
ALTER TABLE media_house_profiles ADD COLUMN twitter_url VARCHAR(500) NULL;
ALTER TABLE media_house_profiles ADD COLUMN instagram_url VARCHAR(500) NULL;
ALTER TABLE media_house_profiles ADD COLUMN youtube_url VARCHAR(500) NULL;
ALTER TABLE media_house_profiles ADD COLUMN tiktok_url VARCHAR(500) NULL;
ALTER TABLE media_house_profiles ADD COLUMN website_url VARCHAR(500) NULL;
ALTER TABLE media_house_profiles ADD COLUMN license_status ENUM('active', 'expired', 'suspended') DEFAULT 'active';
ALTER TABLE media_house_profiles ADD COLUMN license_expires_at DATE NULL;
```

### 7. Notices & Events with Images

```sql
-- Add to notices table
ALTER TABLE notices ADD COLUMN image_path VARCHAR(500) NULL;
ALTER TABLE notices ADD COLUMN thumbnail_path VARCHAR(500) NULL;
ALTER TABLE notices ADD COLUMN expires_at TIMESTAMP NULL;

-- Add to events table (if separate)
ALTER TABLE events ADD COLUMN image_path VARCHAR(500) NULL;
ALTER TABLE events ADD COLUMN thumbnail_path VARCHAR(500) NULL;
ALTER TABLE events ADD COLUMN expires_at TIMESTAMP NULL;
```

### 8. Application History Tracking

```sql
-- Ensure applications table has proper indexing
ALTER TABLE applications ADD INDEX idx_applicant_created (applicant_id, created_at);
ALTER TABLE applications ADD INDEX idx_request_type (request_type);

-- Ensure payment_submissions table has proper indexing
ALTER TABLE payment_submissions ADD INDEX idx_application_created (application_id, created_at);
```

## Permission Matrix Updates

### Registrar Permissions
- `reminders.create` - Create reminders
- `reminders.send` - Send reminders
- `reminders.view_all` - View all reminders
- `reminders.edit` - Edit reminders
- `reminders.delete` - Delete reminders
- `levy_reminders.create` - Create levy reminders
- `levy_reminders.manage` - Manage levy reminders

### Accounts Officer Permissions
- `cash_payments.record` - Record cash payments
- `cash_payments.verify` - Verify cash payments
- `cash_payments.void` - Void cash payments (with reason)
- `payment_history.view` - View payment history

### Accreditation Officer Permissions
- `physical_intake.process` - Process physical form intakes
- `physical_intake.confirm` - Confirm physical intakes
- `production.design` - Access card/certificate designer
- `production.generate` - Generate cards/certificates
- `production.print` - Print cards/certificates
- `production.view_logs` - View print logs

### Auditor Permissions
- `login_activity.view_all` - View all login activities (read-only)
- `reminders.view_all` - View all reminders (read-only)
- `cash_payments.view_all` - View all cash payments (read-only)
- `physical_intake.view_all` - View all physical intakes (read-only)
- `print_logs.view_all` - View all print logs (read-only)

### Applicant Permissions
- `profile.view_own` - View own profile
- `profile.edit_own` - Edit own profile
- `applications.view_own_history` - View own application history
- `payments.view_own_history` - View own payment history
- `reminders.view_own` - View own reminders
- `reminders.acknowledge` - Acknowledge reminders
- `login_activity.view_own` - View own login activity

## API Endpoints Required

### Reminders
- `POST /api/staff/registrar/reminders` - Create reminder
- `GET /api/staff/registrar/reminders` - List reminders
- `PUT /api/staff/registrar/reminders/{id}` - Update reminder
- `DELETE /api/staff/registrar/reminders/{id}` - Delete reminder
- `POST /api/staff/registrar/reminders/{id}/send` - Send reminder
- `GET /api/portal/reminders` - Get user's reminders
- `POST /api/portal/reminders/{id}/acknowledge` - Acknowledge reminder

### Login Activity
- `GET /api/portal/login-activity` - Get own login activity
- `GET /api/staff/admin/login-activity` - Get all login activities (admin)
- `GET /api/staff/auditor/login-activity` - Get all login activities (auditor, read-only)

### Application History
- `GET /api/portal/applications/history` - Get own application history
- `GET /api/staff/applications/{id}/history` - Get application history (staff)

### Payment History
- `GET /api/portal/payments/history` - Get own payment history
- `GET /api/staff/applications/{id}/payments` - Get application payment history (staff)

### Cash Payments
- `POST /api/staff/accounts/cash-payments` - Record cash payment
- `PUT /api/staff/accounts/cash-payments/{id}/verify` - Verify cash payment
- `PUT /api/staff/accounts/cash-payments/{id}/void` - Void cash payment
- `GET /api/staff/accounts/cash-payments` - List cash payments

### Physical Intake
- `POST /api/staff/officer/physical-intake/lookup` - Lookup by number
- `POST /api/staff/officer/physical-intake/confirm` - Confirm intake
- `GET /api/staff/officer/physical-intake` - List physical intakes

### Designer & Production
- `GET /api/staff/production/templates` - List templates
- `POST /api/staff/production/templates` - Create template
- `PUT /api/staff/production/templates/{id}` - Update template
- `POST /api/staff/production/templates/{id}/activate` - Activate template
- `POST /api/staff/production/generate` - Generate card/certificate
- `POST /api/staff/production/print` - Log print action
- `GET /api/staff/production/print-logs` - Get print logs

### Theme
- `PUT /api/portal/profile/theme` - Update theme preference

## Validation Rules

### Reminders
- `title`: required, string, max:255
- `message`: required, string
- `priority`: required, in:high,normal
- `target_type`: required, in:media_practitioner,media_house,bulk
- `target_id`: required_unless:target_type,bulk
- `expires_at`: nullable, date, after:now

### Cash Payments
- `application_id`: required, exists:applications,id
- `receipt_number`: required, string, unique:cash_payments,receipt_number
- `amount`: required, numeric, min:0
- `payment_date`: required, date, before_or_equal:today

### Physical Intake
- `accreditation_number` or `registration_number`: required
- `receipt_number`: required, string
- Number must exist in database
- No duplicate production-ready record for same number and period

### Profile
- Local applicants: `national_id_number` required
- Foreign applicants: `passport_number` required
- `phone_number`: required, valid phone format
- `phone_number_2`: required, valid phone format, different from phone_number

### Media House Renewals
- `previous_certificate`: required, file, mimes:pdf,jpg,jpeg,png, max:5120
- `official_letter`: required, file, mimes:pdf, max:5120

## Edge Cases

### Reminders
- Expired reminders not shown to users
- High priority reminders persist until acknowledged
- Bulk reminders create individual reminder_reads for each target user
- Reminder deletion soft-deletes (deleted_at)

### Cash Payments
- Duplicate receipt numbers blocked at database level
- Voided payments cannot be un-voided
- Void requires reason and audit log

### Physical Intake
- Duplicate check: query production_records for same number + current year
- If duplicate found: require supervisor override (logged)
- Receipt number must be unique across all payment methods

### Login Activity
- Failed login attempts also logged
- Session duration calculated on logout
- Timezone-aware timestamps

### Theme
- Default to light theme for new users
- Theme preference persists across sessions
- Applied to all pages (portals and staff dashboards)

## Security Considerations

### Server-Side Validation
- All inputs validated on server
- No client-side only validation
- File uploads: type, size, and content validation

### RBAC Enforcement
- All endpoints check permissions
- Middleware enforces role-based access
- Auditor role: read-only access enforced at database level

### Audit Logging
- All critical actions logged to activity_logs table
- Include: user_id, action, resource_type, resource_id, old_values, new_values, ip_address, timestamp
- Immutable logs (no deletion, only archival)

### Data Privacy
- Users can only see own data unless staff with proper permissions
- Login activity: users see own, admin/auditor see all
- Payment history: users see own, staff see assigned applications

## UI/UX Considerations

### Reminder Display
- Modal overlay on login if unread high-priority reminders
- Dashboard banner for normal priority reminders
- Dismissible but persistent until acknowledged
- Clear visual hierarchy (high priority = red/urgent styling)

### How-To Pages
- Step-by-step accordion or tabs
- Icons for each step
- Downloadable checklist PDF
- Embedded video tutorials (optional)
- FAQ section with search

### Dashboard Widgets
- License status: color-coded (green=active, red=expired, yellow=expiring soon)
- Years/months/days until expiry: countdown display
- Previous applications: timeline view with status badges
- Previous payments: table with verification status

### Theme Toggle
- Toggle switch in user menu/profile
- Smooth transition between themes
- Consistent color palette for both themes
