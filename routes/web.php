<?php

use Illuminate\Support\Facades\Route;

// System health (used by IT Admin Dashboard uptime indicator)
Route::get('/health', \App\Http\Controllers\HealthController::class)->name('system.health');
use Illuminate\Http\Request;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MiscRoutesController;
use App\Http\Controllers\PortalController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;

use App\Http\Controllers\AccreditationPortalController;
use App\Http\Controllers\MediaHousePortalController;
use App\Http\Controllers\MediaHouseBatchController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\Portal\PortalApplicationDetailsController;

use App\Http\Controllers\Staff\StaffAuthController;
use App\Http\Controllers\Staff\RoleSelectController;
use App\Http\Controllers\Staff\AccreditationOfficerController;
use App\Http\Controllers\Staff\AccountsPaymentsController;
use App\Http\Controllers\Staff\RegistrarController;
use App\Http\Controllers\Staff\ProductionController;
use App\Http\Controllers\Staff\ApplicationDetailsController;
use App\Http\Controllers\Staff\ItAdminController;
use App\Http\Controllers\Staff\ItDashboardController;
use App\Http\Controllers\Staff\AuditorController;
use App\Http\Controllers\Staff\DirectorController;

use App\Http\Controllers\Admin\UserApprovalController;

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminApplicationsController;
use App\Http\Controllers\Admin\UserAccessController;
use App\Http\Controllers\Admin\RolePermissionController;
use App\Http\Controllers\Admin\AdminSystemController;
use App\Http\Controllers\Admin\SuperAdminConfigController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\DownloadsController;
use App\Http\Controllers\Admin\ComplaintsController;
use App\Http\Controllers\SettingsController;

use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| PUBLIC LANDING ( / ) -> home.blade.php
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('public.home');

/*
|--------------------------------------------------------------------------
| PUBLIC PORTAL SELECTION
|--------------------------------------------------------------------------
| Stores public portal choice then routes user to login/signup.
*/
Route::post('/choose-portal', [MiscRoutesController::class, 'choosePortal'])->name('public.choose_portal');

/*
|--------------------------------------------------------------------------
| STAFF ENTRY (STRICT)
|--------------------------------------------------------------------------
| /staff is staff landing page (role tiles)
| staff login is separate from public.
*/
Route::get('/staff', function() { return redirect()->route('staff.login'); })->name('staff.entry');
Route::get('/staff/switch-role', [StaffAuthController::class, 'switchRole'])->name('staff.switch-role');
Route::get('/staff/select-role', [RoleSelectController::class, 'index'])->name('staff.select_role');
Route::post('/staff/select-role', [RoleSelectController::class, 'choose'])->name('staff.choose_role');

Route::get('/staff/login', [StaffAuthController::class, 'show'])->name('staff.login');
Route::post('/staff/login', [StaffAuthController::class, 'login'])->middleware('guest')->name('staff.login.store');
Route::get('/staff/otp', [StaffAuthController::class, 'showOTP'])->name('staff.otp.show');
Route::post('/staff/otp', [StaffAuthController::class, 'verifyOTP'])->name('staff.otp.verify');
Route::post('/staff/logout', [StaffAuthController::class, 'logout'])->middleware('auth')->name('staff.logout');

/*
|--------------------------------------------------------------------------
| PUBLIC AUTH (GUEST)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('auth.login.store');

    Route::get('/signup',  [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/signup', [RegisterController::class, 'store'])->name('auth.register.store');

    // Password reset (public + staff can use)
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

// Language switcher
Route::get('/lang/{locale}', [MiscRoutesController::class, 'switchLang'])->name('lang.switch');

/*
|--------------------------------------------------------------------------
| PAYNOW CALLBACKS (PUBLIC - No Auth Required)
|--------------------------------------------------------------------------
*/
Route::post('/paynow/callback', [\App\Http\Controllers\Portal\PaynowController::class, 'callback'])
    ->name('paynow.callback');
Route::get('/paynow/return', [\App\Http\Controllers\Portal\PaynowController::class, 'return'])
    ->name('paynow.return');

/*
|--------------------------------------------------------------------------
| PUBLIC CONTENT API (EXTERNAL)
|--------------------------------------------------------------------------
*/
Route::get('/api/v1/external-content', [\App\Http\Controllers\Api\PublicContentController::class, 'getContent'])->name('api.v1.external-content');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Unified Settings (staff + public)
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'edit'])->name('settings');
    Route::post('/settings/profile', [\App\Http\Controllers\SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/theme', [\App\Http\Controllers\SettingsController::class, 'updateTheme'])->name('settings.theme');
    Route::post('/settings/theme/ajax', [\App\Http\Controllers\SettingsController::class, 'updateThemeAjax'])->name('settings.theme.ajax');
    Route::post('/settings/security', [\App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security');
    Route::post('/settings/notifications', [\App\Http\Controllers\SettingsController::class, 'updateNotifications'])->name('settings.notifications');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Session management routes
    Route::post('/session/extend', [\App\Http\Controllers\SessionController::class, 'extend'])->name('session.extend');
    Route::get('/session/status', [\App\Http\Controllers\SessionController::class, 'status'])->name('session.status');
    Route::post('/session/timeout-logout', [\App\Http\Controllers\SessionController::class, 'timeoutLogout'])->name('session.timeout-logout');

    /*
    |--------------------------------------------------------------------------
    | AFTER LOGIN ROUTER
    |--------------------------------------------------------------------------
    | routes user into portal chosen on public landing page
    */
    Route::get('/home', [MiscRoutesController::class, 'home'])->name('home');

    /*
    |--------------------------------------------------------------------------
    | Authenticated portal hub (optional)
    |--------------------------------------------------------------------------
    */
    Route::get('/portal', [PortalController::class, 'index'])->name('portal');

    /*
    |--------------------------------------------------------------------------
    | JOURNALIST PORTAL (APPLICANT)
    |--------------------------------------------------------------------------
    */
    Route::prefix('portal/accreditation')
        ->middleware('applicant.portal')
        ->name('accreditation.')
        ->group(function () {

            Route::get('/',          [AccreditationPortalController::class, 'index'])->name('portal');
            Route::get('/dashboard', [AccreditationPortalController::class, 'dashboard'])->name('home');

            Route::get('/new',         [AccreditationPortalController::class, 'new'])->name('new');
            // Correction flow: applicant edits and resubmits
            Route::get('/applications/{application}/edit', [AccreditationPortalController::class, 'editCorrection'])
                ->name('applications.edit');
            Route::post('/applications/{application}/resubmit', [AccreditationPortalController::class, 'resubmitCorrection'])
                ->name('applications.resubmit');
            Route::post('/save-draft', [AccreditationPortalController::class, 'saveDraft'])->name('saveDraft');
            Route::post('/submit',     [AccreditationPortalController::class, 'submit'])->name('submit');
            Route::delete('/applications/{application}', [AccreditationPortalController::class, 'deleteDraft'])->name('delete_draft');
            Route::post('/applications/{application}/withdraw', [AccreditationPortalController::class, 'withdraw'])->name('withdraw');

            Route::post('/applications/{application}/resubmit', [AccreditationPortalController::class, 'resubmitCorrection'])
                ->name('applications.resubmit');

            // AP5 - Renewal
            Route::get('/renewals',          [AccreditationPortalController::class, 'renewals'])->name('renewals');
            Route::post('/renewals/save-draft', [AccreditationPortalController::class, 'saveDraftAp5'])->name('renewals.saveDraft');
            Route::post('/renewals/submit',  [AccreditationPortalController::class, 'submitAp5'])->name('submitAp5');
            
            // AP5 - Replacement
            Route::get('/replacement',       [AccreditationPortalController::class, 'replacement'])->name('replacement');
            Route::post('/replacement/save-draft', [AccreditationPortalController::class, 'saveDraftReplacement'])->name('replacement.saveDraft');
            Route::post('/replacement/submit', [AccreditationPortalController::class, 'submitReplacement'])->name('replacement.submit');

            Route::get('/lookup-number/{number}', [AccreditationPortalController::class, 'lookupAccreditationNumber'])
                ->name('lookupNumber');

            Route::get('/payments',  [AccreditationPortalController::class, 'payments'])->name('payments');
            Route::get('/notices',   [AccreditationPortalController::class, 'notices'])->name('notices');
            Route::get('/howto',     [AccreditationPortalController::class, 'howto'])->name('howto');
            Route::get('/requirements', [AccreditationPortalController::class, 'requirements'])->name('requirements');
            Route::get('/profile',   [AccreditationPortalController::class, 'profile'])->name('profile');
            Route::post('/profile',  [AccreditationPortalController::class, 'updateProfile'])->name('profile.update');
            Route::get('/settings',  [AccreditationPortalController::class, 'settings'])->name('settings');
            Route::get('/communication', [AccreditationPortalController::class, 'communication'])->name('communication');

            // Downloads
            Route::get('/downloads', [\App\Http\Controllers\Portal\DownloadsController::class, 'index'])
                ->name('downloads');
            Route::get('/downloads/file/{doc}', [\App\Http\Controllers\Portal\DownloadsController::class, 'download'])
                ->name('downloads.file');
        });

    /*
    |--------------------------------------------------------------------------
    | MASS MEDIA PORTAL (APPLICANT)
    |--------------------------------------------------------------------------
    */
    Route::prefix('media-house/registration')
        ->middleware('applicant.portal')
        ->name('mediahouse.')
        ->group(function () {

            Route::get('/',            [MediaHousePortalController::class, 'dashboard'])->name('portal');

            Route::get('/new',         [MediaHousePortalController::class, 'newRegistration'])->name('new');
            // Correction flow: applicant edits and resubmits
            Route::get('/applications/{application}/edit', [MediaHousePortalController::class, 'editCorrection'])->name('applications.edit');
            Route::post('/applications/{application}/resubmit', [MediaHousePortalController::class, 'resubmitCorrection'])->name('applications.resubmit');
            Route::post('/save-draft', [MediaHousePortalController::class, 'saveDraft'])->name('saveDraft');
            Route::post('/submit',     [MediaHousePortalController::class, 'submit'])->name('submit');
            Route::delete('/applications/{application}', [MediaHousePortalController::class, 'deleteDraft'])->name('delete_draft');
            Route::post('/applications/{application}/withdraw', [MediaHousePortalController::class, 'withdraw'])->name('withdraw');

            // NEW: Batch Processing
            Route::get('/batches', [MediaHouseBatchController::class, 'index'])->name('batch.index');
            Route::post('/batches/initiate', [MediaHouseBatchController::class, 'initiate'])->name('batch.initiate');
            Route::post('/batches', [MediaHouseBatchController::class, 'store'])->name('batch.store');
            Route::get('/batches/{batch}', [MediaHouseBatchController::class, 'show'])->name('batch.show');
            Route::post('/batches/{batch}/payment', [MediaHouseBatchController::class, 'submitPayment'])->name('batch.payment');

            // Staff Management (Linking Journalists)
            Route::get('/staff-members', [\App\Http\Controllers\MediaHouseStaffController::class, 'index'])->name('staff.index');
            Route::post('/staff-members/link', [\App\Http\Controllers\MediaHouseStaffController::class, 'link'])->name('staff.link');
            Route::delete('/staff-members/{staff}', [\App\Http\Controllers\MediaHouseStaffController::class, 'unlink'])->name('staff.unlink');

            Route::get('/renewals',    [MediaHousePortalController::class, 'renewals'])->name('renewals');
            Route::post('/renewals/save-draft', [MediaHousePortalController::class, 'saveDraftAp5'])->name('ap5.saveDraft');
            Route::post('/renewals/submit', [MediaHousePortalController::class, 'submitAp5'])
                ->name('ap5.submit');
            
            // AP5 - Replacement
            Route::get('/replacement', [MediaHousePortalController::class, 'replacement'])->name('replacement');
            Route::post('/replacement/save-draft', [MediaHousePortalController::class, 'saveDraftReplacement'])->name('replacement.saveDraft');
            Route::post('/replacement/submit', [MediaHousePortalController::class, 'submitReplacement'])->name('replacement.submit');
            Route::get('/lookup-number/{number}', [MediaHousePortalController::class, 'lookupRegistrationNumber'])
                ->name('lookupNumber');

            Route::get('/payments',    [MediaHousePortalController::class, 'payments'])->name('payments');
            Route::get('/notices',     [MediaHousePortalController::class, 'notices'])->name('notices');
            Route::get('/howto',       [MediaHousePortalController::class, 'howto'])->name('howto');
            Route::get('/requirements', [MediaHousePortalController::class, 'requirements'])->name('requirements');
            Route::get('/profile',     [MediaHousePortalController::class, 'profile'])->name('profile');
            Route::post('/profile',    [MediaHousePortalController::class, 'updateProfile'])->name('profile.update');
            Route::get('/settings',    [MediaHousePortalController::class, 'settings'])->name('settings');

            // Downloads
            Route::get('/downloads', [\App\Http\Controllers\Portal\DownloadsController::class, 'index'])
                ->defaults('portal', 'mediahouse')
                ->name('downloads');
            Route::get('/downloads/file/{doc}', [\App\Http\Controllers\Portal\DownloadsController::class, 'download'])
                ->defaults('portal', 'mediahouse')
                ->name('downloads.file');
        });

    /*
    |--------------------------------------------------------------------------
    | MESSAGING (APPLICANTS + STAFF)
    |--------------------------------------------------------------------------
    */
    Route::get('/messages', [MessagesController::class, 'index'])->name('messages.index');
    Route::get('/messages/application/{application}', [MessagesController::class, 'thread'])->name('messages.thread');
    Route::post('/messages/application/{application}', [MessagesController::class, 'send'])->name('messages.send');

    /*
    |--------------------------------------------------------------------------
    | APPLICANT JSON DETAILS (for applicant dashboard View modal)
    |--------------------------------------------------------------------------
    */
    Route::get('/portal/applications/{application}/details', [PortalApplicationDetailsController::class, 'show'])
        ->name('portal.applications.details');

    Route::get('/portal/payments/history', [\App\Http\Controllers\Portal\PaymentHistoryController::class, 'index'])
        ->name('portal.payments.history')
        ->middleware('auth');

    Route::get('/portal/payments/{payment}/receipt', [\App\Http\Controllers\Portal\PaymentHistoryController::class, 'showReceipt'])
        ->name('portal.payments.receipt')
        ->middleware('auth');

    /*
    |--------------------------------------------------------------------------
    | Notices & Events (Applicant view)
    |--------------------------------------------------------------------------
    */
    Route::get('/portal/notices-events', [\App\Http\Controllers\Portal\NoticesEventsController::class, 'index'])
        ->name('portal.notices-events.index');

    /*
    |--------------------------------------------------------------------------
    | PAYNOW PAYMENTS
    |--------------------------------------------------------------------------
    */
    Route::post('/payments/{application}/initiate', [\App\Http\Controllers\Portal\PaynowController::class, 'initiate'])
        ->name('paynow.initiate');
    Route::post('/payments/{application}/initiate-mobile', [\App\Http\Controllers\Portal\PaynowController::class, 'initiateMobile'])
        ->name('paynow.initiate.mobile');
    Route::get('/payments/{application}/status', [\App\Http\Controllers\Portal\PaynowController::class, 'checkStatus'])
        ->name('paynow.status');

    /*
    |--------------------------------------------------------------------------
    | MANUAL PAYMENTS (Proof / Waiver uploads)
    |--------------------------------------------------------------------------
    */
    Route::post('/payments/{application}/upload-proof', [\App\Http\Controllers\Portal\ManualPaymentController::class, 'uploadProof'])
        ->name('payments.upload_proof');
    Route::post('/payments/{application}/upload-waiver', [\App\Http\Controllers\Portal\ManualPaymentController::class, 'uploadWaiver'])
        ->name('payments.upload_waiver');
    Route::post('/payments/{application}/submit-reference', [\App\Http\Controllers\Portal\ManualPaymentController::class, 'submitReference'])
        ->name('payments.submit_reference');
    Route::get('/payments/{application}/receipt', [\App\Http\Controllers\Portal\PaymentReceiptController::class, 'download'])
        ->name('payments.receipt');

    /*
    |--------------------------------------------------------------------------
    | SUPER ADMIN PANEL
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:super_admin|director|it_admin|registrar'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
            // Live counters (no demo data)
            Route::get('/dashboard/stats', [AdminDashboardController::class, 'stats'])->name('dashboard.stats');

            // Analytics
            Route::get('/analytics', [AdminAnalyticsController::class, 'index'])
                ->name('analytics');

            // Downloads / exports
            Route::get('/downloads', [\App\Http\Controllers\Admin\DownloadsController::class, 'index'])
                ->name('downloads.index');
            Route::get('/downloads/csv/{type}', [\App\Http\Controllers\Admin\DownloadsController::class, 'csv'])
                ->name('downloads.csv');

            // Read-only application lists for admin oversight
            Route::get('/media-house-registrations', [AdminApplicationsController::class, 'mediaHouse'])->name('mediahouse.index');
            Route::get('/journalist-accreditations', [AdminApplicationsController::class, 'accreditation'])->name('accreditation.index');
            Route::get('/applications/{application}', [AdminApplicationsController::class, 'show'])->name('applications.show');
            // Content: Notices & Events
            Route::get('/content', [\App\Http\Controllers\Admin\ContentController::class, 'index'])
                ->middleware('module.enabled:notices')
                ->name('content.index');
            Route::post('/content/notices', [\App\Http\Controllers\Admin\ContentController::class, 'storeNotice'])
                        ->middleware(['role:super_admin|pr_officer','module.enabled:notices'])
                        ->name('content.notices.store');
            Route::put('/content/notices/{notice}', [\App\Http\Controllers\Admin\ContentController::class, 'updateNotice'])
                        ->middleware(['role:super_admin|pr_officer','module.enabled:notices'])
                        ->name('content.notices.update');
            Route::delete('/content/notices/{notice}', [\App\Http\Controllers\Admin\ContentController::class, 'destroyNotice'])
                        ->middleware(['role:super_admin|pr_officer','module.enabled:notices'])
                        ->name('content.notices.destroy');

            Route::post('/content/events', [\App\Http\Controllers\Admin\ContentController::class, 'storeEvent'])
                        ->middleware(['role:super_admin|pr_officer','module.enabled:events'])
                        ->name('content.events.store');
            Route::put('/content/events/{event}', [\App\Http\Controllers\Admin\ContentController::class, 'updateEvent'])
                        ->middleware(['role:super_admin|pr_officer','module.enabled:events'])
                        ->name('content.events.update');
            Route::delete('/content/events/{event}', [\App\Http\Controllers\Admin\ContentController::class, 'destroyEvent'])
                        ->middleware(['role:super_admin|pr_officer','module.enabled:events'])
                        ->name('content.events.destroy');

            // Vacancies
            Route::post('/content/vacancies', [\App\Http\Controllers\Admin\ContentController::class, 'storeVacancy'])
                        ->middleware(['role:super_admin|pr_officer'])
                        ->name('content.vacancies.store');
            Route::put('/content/vacancies/{vacancy}', [\App\Http\Controllers\Admin\ContentController::class, 'updateVacancy'])
                        ->middleware(['role:super_admin|pr_officer'])
                        ->name('content.vacancies.update');
            Route::delete('/content/vacancies/{vacancy}', [\App\Http\Controllers\Admin\ContentController::class, 'destroyVacancy'])
                        ->middleware(['role:super_admin|pr_officer'])
                        ->name('content.vacancies.destroy');

            // Tenders
            Route::post('/content/tenders', [\App\Http\Controllers\Admin\ContentController::class, 'storeTender'])
                        ->middleware(['role:super_admin|pr_officer'])
                        ->name('content.tenders.store');
            Route::put('/content/tenders/{tender}', [\App\Http\Controllers\Admin\ContentController::class, 'updateTender'])
                        ->middleware(['role:super_admin|pr_officer'])
                        ->name('content.tenders.update');
            Route::delete('/content/tenders/{tender}', [\App\Http\Controllers\Admin\ContentController::class, 'destroyTender'])
                        ->middleware(['role:super_admin|pr_officer'])
                        ->name('content.tenders.destroy');


            // News (for website)
            Route::get('/news', [\App\Http\Controllers\Admin\NewsController::class, 'index'])
                ->middleware('module.enabled:news')
                ->name('news.index');
            Route::post('/news', [\App\Http\Controllers\Admin\NewsController::class, 'store'])
                        ->middleware(['role:super_admin|pr_officer','module.enabled:news'])
                        ->name('news.store');
            Route::put('/news/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'update'])
                        ->middleware(['role:super_admin|pr_officer','module.enabled:news'])
                        ->name('news.update');
            Route::delete('/news/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'destroy'])
                        ->middleware(['role:super_admin|pr_officer','module.enabled:news'])
                        ->name('news.destroy');

            // Complaints & Appeals (from website)
            Route::get('/complaints', [\App\Http\Controllers\Admin\ComplaintsController::class, 'index'])
                ->middleware('role:super_admin|director|research_training_standards|public_info_compliance')
                ->name('complaints.index');
            Route::put('/complaints/{complaint}', [\App\Http\Controllers\Admin\ComplaintsController::class, 'update'])
                ->middleware('role:super_admin|director|research_training_standards|public_info_compliance')
                ->name('complaints.update');

            // System governance (Super Admin)
            Route::get('/workflow', [AdminSystemController::class, 'workflow'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('workflow.index');

            Route::get('/templates', [AdminSystemController::class, 'templates'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('templates.index');

            Route::get('/fees', [AdminSystemController::class, 'fees'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('fees.index');

            Route::get('/audit', [AdminSystemController::class, 'audit'])
                ->middleware('role:super_admin|it_admin|director|registrar')
                ->name('audit.index');

            Route::get('/regions', [AdminSystemController::class, 'regions'])
                ->middleware('role:super_admin|it_admin|director|registrar')
                ->name('regions.index');

            Route::get('/system-health', [AdminSystemController::class, 'health'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('health.index');

            // Super Admin advanced console
            Route::get('/login-activity', [SuperAdminConfigController::class, 'loginActivity'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('users.login_activity');

            Route::match(['get','post'], '/workflow-config', [SuperAdminConfigController::class, 'workflowConfig'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('workflow.config');

            Route::match(['get','post'], '/fees-config', [SuperAdminConfigController::class, 'feesConfig'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('fees.config');

            Route::match(['get','post'], '/templates-config', [SuperAdminConfigController::class, 'templates'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('templates.config');

            Route::match(['get','post'], '/content-control', [SuperAdminConfigController::class, 'contentControl'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('content.control');

            Route::match(['get','post'], '/regions-offices', [SuperAdminConfigController::class, 'regionsOffices'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('regions.offices');

            Route::match(['get','post'], '/master-settings', [SuperAdminConfigController::class, 'masterSettings'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('system.master_settings');

            // Backward compatible route (old link)
            Route::get('/system-settings', fn() => redirect()->route('admin.system.master_settings'))
                ->middleware('role:super_admin|it_admin|director')
                ->name('system.settings');

            Route::get('/reports', [SuperAdminConfigController::class, 'reports'])
                ->middleware('role:super_admin|it_admin|director')
                ->name('reports.index');

            // Settings (profile, password, theme)
            Route::get('/settings', [AdminSystemController::class, 'settings'])
                ->name('settings.index');
            Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
            Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
            Route::post('/settings/theme', [SettingsController::class, 'updateTheme'])->name('settings.theme.update');



            // Users + access
            Route::get('/users', [UserAccessController::class, 'index'])
                ->name('users.index');
            // Split users into separate lists (pages)
            Route::get('/users/staff', [UserAccessController::class, 'staffIndex'])
                ->name('users.staff');
            Route::get('/users/public', [UserAccessController::class, 'publicIndex'])
                ->name('users.public');

            Route::get('/users/create', [UserAccessController::class, 'create'])
                ->name('users.create');
            Route::post('/users', [UserAccessController::class, 'store'])
                ->name('users.store');

            Route::get('/users/{user}/access', [UserAccessController::class, 'editAccess'])
                ->name('users.access.edit');
            Route::post('/users/{user}/access', [UserAccessController::class, 'updateAccess'])
                ->name('users.access.update');
            Route::post('/users/{user}/reset', [UserAccessController::class, 'resetAccount'])
                ->name('users.reset');
            Route::delete('/users/{user}', [UserAccessController::class, 'destroy'])
                ->name('users.destroy');

            // Roles + permissions (Super Admin & Director only)
            Route::get('/roles', [RolePermissionController::class, 'rolesIndex'])
                ->middleware('role:super_admin|director')
                ->name('roles.index');
            Route::post('/roles', [RolePermissionController::class, 'rolesStore'])
                ->middleware('role:super_admin|director')
                ->name('roles.store');

            Route::get('/roles/{role}/edit', [RolePermissionController::class, 'rolesEdit'])
                ->middleware('role:super_admin|director')
                ->name('roles.edit');
            Route::post('/roles/{role}', [RolePermissionController::class, 'rolesUpdate'])
                ->middleware('role:super_admin|director')
                ->name('roles.update');

            Route::get('/permissions', [RolePermissionController::class, 'permissionsIndex'])
                ->middleware('role:super_admin|director')
                ->name('permissions.index');
            Route::get('/permissions/matrix', [RolePermissionController::class, 'permissionMatrix'])
                ->middleware('role:super_admin|director')
                ->name('permissions.matrix');
            Route::post('/permissions', [RolePermissionController::class, 'permissionsStore'])
                ->middleware('role:super_admin|director')
                ->name('permissions.store');

            // User approvals (created/managed by IT Admin & Director/Super Admin)
            Route::get('/user-approvals', [UserApprovalController::class, 'index'])
                ->name('approvals.index');
            Route::post('/user-approvals/{user}/approve', [UserApprovalController::class, 'approve'])
                ->name('approvals.approve');
            Route::post('/user-approvals/{user}/reject', [UserApprovalController::class, 'reject'])
                ->name('approvals.reject');
        });



    /*
    |--------------------------------------------------------------------------
    | WEBSITE / PUBLIC JSON ENDPOINTS
    |--------------------------------------------------------------------------
    */
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/news', [\App\Http\Controllers\Website\PublicContentController::class, 'news'])->name('news');
        Route::get('/notices', [\App\Http\Controllers\Website\PublicContentController::class, 'notices'])->name('notices');
        Route::get('/events', [\App\Http\Controllers\Website\PublicContentController::class, 'events'])->name('events');
    });

    Route::post('/website/complaints', [\App\Http\Controllers\Website\ComplaintsController::class, 'store'])->name('website.complaints.store');

    // Account Setup (Post-Reset)
    Route::get('/account/setup/{token}', [\App\Http\Controllers\Auth\AccountSetupController::class, 'show'])->name('account.setup');
    Route::post('/account/setup/{token}', [\App\Http\Controllers\Auth\AccountSetupController::class, 'update'])->name('account.setup.update');
/*
    |--------------------------------------------------------------------------
    | STAFF - JSON DETAILS (for dashboard view modals)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['staff.portal','role:accreditation_officer|accounts_payments|registrar|production|super_admin|director'])
        ->get('/staff/applications/{application}/details', [ApplicationDetailsController::class, 'show'])
        ->name('staff.applications.details');

    Route::middleware(['staff.portal'])->get('/staff/search', [ApplicationDetailsController::class, 'globalSearch'])->name('staff.search');

    // Secure staff-only document viewing (applicant uploads)
    Route::middleware(['staff.portal','role:accreditation_officer|registrar|accounts_payments|production|auditor|super_admin|it_admin|director'])
        ->get('/staff/documents/{document}', [\App\Http\Controllers\Staff\DocumentViewController::class, 'show'])
        ->name('staff.documents.show');

    /*
    |--------------------------------------------------------------------------
    | STAFF - ACCREDITATION OFFICER
    |--------------------------------------------------------------------------
    */
    Route::middleware(['staff.portal','role:accreditation_officer','block.director.operational'])
        ->prefix('staff/accreditation-officer')
        ->name('staff.officer.')
        ->group(function () {
            Route::get('/', [AccreditationOfficerController::class, 'dashboard'])->name('dashboard');
            Route::get('/applications/{application}', [AccreditationOfficerController::class, 'show'])->name('applications.show');
            Route::post('/applications/{application}/approve', [AccreditationOfficerController::class, 'approve'])->name('applications.approve');
            Route::post('/applications/{application}/request-correction', [AccreditationOfficerController::class, 'requestCorrection'])->name('applications.requestCorrection');
            Route::post('/applications/{application}/message', [AccreditationOfficerController::class, 'sendMessage'])->name('applications.message');
            Route::post('/applications/{application}/unlock', [AccreditationOfficerController::class, 'unlock'])->name('applications.unlock');
            Route::post('/applications/{application}/forward-to-registrar', [AccreditationOfficerController::class, 'forwardToRegistrar'])->name('applications.forward-to-registrar');

            Route::get('/physical-intake', [AccreditationOfficerController::class, 'physicalIntake'])->name('physical-intake');
            Route::post('/physical-intake', [AccreditationOfficerController::class, 'processPhysicalIntake'])->name('physical-intake.process');
            Route::get('/lookup-application/{number}', [AccreditationOfficerController::class, 'lookupApplication'])->name('lookup-application');

            Route::get('/production-queue', [AccreditationOfficerController::class, 'productionQueue'])->name('production-queue');

            // Applications (list views with comprehensive filtering)
            Route::get('/applications', [AccreditationOfficerController::class, 'allApplications'])->name('applications.index');
            Route::get('/applications-recent', [AccreditationOfficerController::class, 'recentApplications'])->name('applications.new');
            Route::get('/applications-pending', [AccreditationOfficerController::class, 'pendingReview'])->name('applications.pending');
            Route::get('/applications-approved', [AccreditationOfficerController::class, 'approvedApplications'])->name('applications.approved');
            Route::get('/applications-rejected', [AccreditationOfficerController::class, 'rejectedApplications'])->name('applications.rejected');
            Route::get('/applications-returned', [AccreditationOfficerController::class, 'returnedApplications'])->name('applications.returned');
            
            // Export functionality
            Route::get('/export/csv', [AccreditationOfficerController::class, 'exportCsv'])->name('export.csv');
            Route::get('/export/pdf', [AccreditationOfficerController::class, 'exportPdf'])->name('export.pdf');
            
            // Seek guidance from Registrar
            Route::post('/seek-guidance', [AccreditationOfficerController::class, 'seekGuidance'])->name('seek-guidance');

            // Records
            Route::get('/records/accredited-journalists', [AccreditationOfficerController::class, 'accreditedJournalists'])->name('records.accredited-journalists');
            Route::get('/records/registered-mediahouses', [AccreditationOfficerController::class, 'registeredMediaHouses'])->name('records.registered-mediahouses');
            Route::get('/records/history', [AccreditationOfficerController::class, 'recordsHistory'])->name('records.history');
            Route::get('/records/renewals', [AccreditationOfficerController::class, 'recordsRenewals'])->name('records.renewals');

            // Document verification
            Route::get('/documents/uploaded', [AccreditationOfficerController::class, 'documentsUploaded'])->name('documents.uploaded');
            Route::get('/documents/pending', [AccreditationOfficerController::class, 'documentsPending'])->name('documents.pending');
            Route::get('/documents/verified', [AccreditationOfficerController::class, 'documentsVerified'])->name('documents.verified');
            Route::get('/documents/rejected', [AccreditationOfficerController::class, 'documentsRejected'])->name('documents.rejected');

            // Renewals & expiry
            Route::get('/renewals/expiring', [AccreditationOfficerController::class, 'renewalsExpiring'])->name('renewals.expiring');
            Route::get('/renewals/expired', [AccreditationOfficerController::class, 'renewalsExpired'])->name('renewals.expired');
            Route::get('/renewals/requests', [AccreditationOfficerController::class, 'renewalsRequests'])->name('renewals.requests');
            Route::get('/renewals/queue', [AccreditationOfficerController::class, 'renewalsQueue'])->name('renewals.queue');
            Route::post('/renewals/send-reminders', [AccreditationOfficerController::class, 'sendRenewalReminders'])->name('renewals.send-reminders');

            // Records Management - Complete Database System
            // Full Record Viewing
            Route::get('/records/accredited-journalists/{id}/full', [AccreditationOfficerController::class, 'viewFullAccreditedJournalist'])->name('records.accredited-journalists.full');
            Route::get('/records/registered-mediahouses/{id}/full', [AccreditationOfficerController::class, 'viewFullRegisteredMediaHouse'])->name('records.registered-mediahouses.full');
            
            // Record Editing
            Route::get('/records/accredited-journalists/{id}/edit', [AccreditationOfficerController::class, 'editAccreditedJournalist'])->name('records.accredited-journalists.edit');
            Route::get('/records/registered-mediahouses/{id}/edit', [AccreditationOfficerController::class, 'editRegisteredMediaHouse'])->name('records.registered-mediahouses.edit');
            Route::post('/records/accredited-journalists/{id}/update', [AccreditationOfficerController::class, 'updateAccreditedJournalist'])->name('records.accredited-journalists.update');
            Route::post('/records/registered-mediahouses/{id}/update', [AccreditationOfficerController::class, 'updateRegisteredMediaHouse'])->name('records.registered-mediahouses.update');
            
            // Document Management
            Route::get('/records/accredited-journalists/{id}/download-documents', [AccreditationOfficerController::class, 'downloadAccreditedJournalistDocuments'])->name('records.accredited-journalists.download-documents');
            Route::get('/records/registered-mediahouses/{id}/download-documents', [AccreditationOfficerController::class, 'downloadRegisteredMediaHouseDocuments'])->name('records.registered-mediahouses.download-documents');
            
            // Export Functionality
            Route::get('/records/accredited-journalists/export', [AccreditationOfficerController::class, 'exportAccreditedJournalists'])->name('records.accredited-journalists.export');
            Route::get('/records/registered-mediahouses/export', [AccreditationOfficerController::class, 'exportRegisteredMediaHouses'])->name('records.registered-mediahouses.export');
            
            // PDF Export Functionality
            Route::get('/records/accredited-journalists/export-pdf', [AccreditationOfficerController::class, 'exportAccreditedJournalistsPDF'])->name('records.accredited-journalists.export-pdf');
            Route::get('/records/registered-mediahouses/export-pdf', [AccreditationOfficerController::class, 'exportRegisteredMediaHousesPDF'])->name('records.registered-mediahouses.export-pdf');
            Route::get('/records/accredited-journalists/{id}/export-pdf', [AccreditationOfficerController::class, 'exportSingleAccreditedJournalistPDF'])->name('records.accredited-journalists.export-single-pdf');
            Route::get('/records/registered-mediahouses/{id}/export-pdf', [AccreditationOfficerController::class, 'exportSingleRegisteredMediaHousePDF'])->name('records.registered-mediahouses.export-single-pdf');
            
            // Legacy Routes (maintained for compatibility)
            Route::post('/records/send-collection-notification', [AccreditationOfficerController::class, 'sendCollectionNotification'])->name('records.send-collection-notification');
            Route::put("/records/update", [AccreditationOfficerController::class, "updateRecordData"])->name("records.update");
            Route::get("/records/{id}/edit-data", [AccreditationOfficerController::class, "getRecordData"])->name("records.edit-data");

            // Edit Request Management - Registrar Approval System
            Route::get('/edit-requests', [AccreditationOfficerController::class, 'editRequests'])->name('edit-requests.index');
            Route::get('/edit-requests/{id}', [AccreditationOfficerController::class, 'viewEditRequest'])->name('edit-requests.view');
            Route::post('/edit-requests/{id}/approve', [AccreditationOfficerController::class, 'approveEditRequest'])->name('edit-requests.approve');
            Route::post('/edit-requests/{id}/reject', [AccreditationOfficerController::class, 'rejectEditRequest'])->name('edit-requests.reject');

            // Registrar Routes
        Route::prefix('registrar')->name('registrar.')->middleware(['auth', 'role:registrar'])->group(function () {
            Route::get('/', [RegistrarController::class, 'dashboard'])->name('dashboard');
            Route::get('/applications', [RegistrarController::class, 'applications'])->name('applications');
            Route::get('/applications/{id}/mark-reviewed', [RegistrarController::class, 'markAsReviewed'])->name('applications.mark-reviewed');
            Route::post('/applications/{id}/flag-anomaly', [RegistrarController::class, 'flagAnomaly'])->name('applications.flag-anomaly');
            Route::post('/applications/{id}/send-guidance', [RegistrarController::class, 'sendGuidance'])->name('applications.send-guidance');
            Route::post('/applications/{id}/send-message', [RegistrarController::class, 'sendMessage'])->name('applications.send-message');
            Route::get('/reports', [RegistrarController::class, 'reports'])->name('reports');
            Route::get('/reports/export', [RegistrarController::class, 'exportApplications'])->name('reports.export');
            Route::get('/downloads', [RegistrarController::class, 'downloads'])->name('downloads');
            Route::get('/dashboard/export', [RegistrarController::class, 'exportDashboardReport'])->name('dashboard.export');
            
            // Records Management Routes (same as officer but read-only for registrar)
            Route::prefix('records')->name('records.')->group(function () {
                Route::get('/accredited-journalists', [RegistrarController::class, 'accreditedJournalists'])->name('accredited-journalists');
                Route::get('/accredited-journalists/export', [RegistrarController::class, 'exportAccreditedJournalists'])->name('accredited-journalists.export');
                Route::get('/accredited-journalists/export-pdf', [RegistrarController::class, 'exportAccreditedJournalistsPDF'])->name('accredited-journalists.export-pdf');
                Route::get('/accredited-journalists/{id}', [RegistrarController::class, 'viewAccreditedJournalist'])->name('accredited-journalists.view');
                Route::get('/accredited-journalists/{id}/download-documents', [RegistrarController::class, 'downloadAccreditedJournalistDocuments'])->name('accredited-journalists.download-documents');
                
                Route::get('/registered-mediahouses', [RegistrarController::class, 'registeredMediaHouses'])->name('registered-mediahouses');
                Route::get('/registered-mediahouses/export', [RegistrarController::class, 'exportRegisteredMediaHouses'])->name('registered-mediahouses.export');
                Route::get('/registered-mediahouses/export-pdf', [RegistrarController::class, 'exportRegisteredMediaHousesPDF'])->name('registered-mediahouses.export-pdf');
                Route::get('/registered-mediahouses/{id}', [RegistrarController::class, 'viewRegisteredMediaHouse'])->name('registered-mediahouses.view');
                Route::get('/registered-mediahouses/{id}/download-documents', [RegistrarController::class, 'downloadRegisteredMediaHouseDocuments'])->name('registered-mediahouses.download-documents');
            });
        });

        // Receipt Management Routes
        Route::prefix('receipts')->name('receipts.')->middleware(['auth', 'role:accounts_officer,registrar,admin'])->group(function () {
            Route::get('/', [ReceiptController::class, 'index'])->name('index');
            Route::get('/create/{applicationId}', [ReceiptController::class, 'create'])->name('create');
            Route::post('/', [ReceiptController::class, 'store'])->name('store');
            Route::get('/{id}', [ReceiptController::class, 'show'])->name('show');
            Route::get('/{id}/view', [ReceiptController::class, 'view'])->name('view');
            Route::get('/{id}/download', [ReceiptController::class, 'download'])->name('download');
            Route::post('/{id}/verify', [ReceiptController::class, 'verifyReceipt'])->name('verify');
            Route::post('/{id}/cancel', [ReceiptController::class, 'cancelReceipt'])->name('cancel');
            Route::get('/export', [ReceiptController::class, 'export'])->name('export');
        });

        // Public Payment Verification Route
        Route::get('/verify-payment/{reference}', [ReceiptController::class, 'verifyPayment'])->name('payment.verification');

        // PayNow Webhook Route
        Route::post('/webhooks/paynow', [ReceiptController::class, 'paynowWebhook'])->name('webhooks.paynow');

        // Production Dashboard Routes
            Route::get('/production', [ProductionController::class, 'dashboard'])->name('production.dashboard');
            Route::get('/production/queue', [ProductionController::class, 'queue'])->name('production.queue');
            Route::get('/production/card-generation', [ProductionController::class, 'cardGeneration'])->name('production.card-generation');
            Route::get('/production/print-queue', [ProductionController::class, 'printQueue'])->name('production.print-queue');
            Route::get('/production/issuance', [ProductionController::class, 'issuance'])->name('production.issuance');
            
            // Production Actions
            Route::post('/production/{application}/move-to-card-generation', [ProductionController::class, 'moveToCardGeneration'])->name('production.move-to-card-generation');
            Route::post('/production/{application}/generate-card', [ProductionController::class, 'generateCard'])->name('production.generate-card');
            Route::post('/production/{application}/move-to-print-queue', [ProductionController::class, 'moveToPrintQueue'])->name('production.move-to-print-queue');
            Route::post('/production/{application}/mark-printed', [ProductionController::class, 'markPrinted'])->name('production.mark-printed');
            Route::post('/production/{application}/mark-issued', [ProductionController::class, 'markIssued'])->name('production.mark-issued');
            
            // Production Analytics
            Route::get('/production/analytics', [ProductionController::class, 'analytics'])->name('production.analytics');
            Route::get('/production/performance', [ProductionController::class, 'performance'])->name('production.performance');
            Route::get('/production/export', [ProductionController::class, 'export'])->name('production.export');

            // Compliance
            Route::get('/compliance/monitoring', [AccreditationOfficerController::class, 'complianceMonitoring'])->name('compliance.monitoring');
            Route::get('/compliance/violations', [AccreditationOfficerController::class, 'complianceViolations'])->name('compliance.violations');
            Route::get('/compliance/cases', [AccreditationOfficerController::class, 'complianceCases'])->name('compliance.cases');
            Route::get('/compliance/unaccredited', [AccreditationOfficerController::class, 'complianceUnaccredited'])->name('compliance.unaccredited');

            // Reports
            Route::get('/reports/stats', [AccreditationOfficerController::class, 'reportsStats'])->name('reports.stats');
            Route::get('/reports/monthly', [AccreditationOfficerController::class, 'reportsMonthly'])->name('reports.monthly');
            Route::get('/reports/trends', [AccreditationOfficerController::class, 'reportsTrends'])->name('reports.trends');
            Route::get('/reports/compliance', [AccreditationOfficerController::class, 'reportsCompliance'])->name('reports.compliance');

            // Notices & communication
            Route::get('/comm/notices', [AccreditationOfficerController::class, 'commNotices'])->name('comm.notices');
            Route::get('/comm/announcements', [AccreditationOfficerController::class, 'commAnnouncements'])->name('comm.announcements');
            Route::get('/comm/memos', [AccreditationOfficerController::class, 'commMemos'])->name('comm.memos');
            Route::get('/comm/messaging', [AccreditationOfficerController::class, 'commMessaging'])->name('comm.messaging');

            // Advanced tools
            Route::get('/advanced/duplicates', [AccreditationOfficerController::class, 'advancedDuplicates'])->name('advanced.duplicates');
            Route::get('/advanced/forgery', [AccreditationOfficerController::class, 'advancedForgery'])->name('advanced.forgery');
            Route::get('/advanced/qr', [AccreditationOfficerController::class, 'advancedQr'])->name('advanced.qr');
            Route::get('/advanced/audit', [AccreditationOfficerController::class, 'advancedAudit'])->name('advanced.audit');

            // Profile & tools
            Route::get('/tools/profile', [AccreditationOfficerController::class, 'toolsProfile'])->name('tools.profile');
            Route::get('/tools/tasks', [AccreditationOfficerController::class, 'toolsTasks'])->name('tools.tasks');
            Route::get('/tools/drafts', [AccreditationOfficerController::class, 'toolsDrafts'])->name('tools.drafts');
            Route::get('/tools/sops', [AccreditationOfficerController::class, 'toolsSops'])->name('tools.sops');
        });

    /*
    |--------------------------------------------------------------------------
    | STAFF - REGISTRAR
    |--------------------------------------------------------------------------
    */
    Route::middleware(['staff.portal','role:registrar','block.director.operational'])
        ->prefix('staff/registrar')
        ->name('staff.registrar.')
        ->group(function () {
            Route::get('/', [RegistrarController::class, 'dashboard'])->name('dashboard');
            Route::get('/incoming-queue', [RegistrarController::class, 'incomingQueue'])->name('incoming-queue');
            Route::get('/reports', [RegistrarController::class, 'reports'])->name('reports');
            // Route::get('/audit-trail', [RegistrarController::class, 'auditTrailSearch'])->name('audit-trail');
            Route::post('/applications/{application}/reassign-category', [RegistrarController::class, 'reassignCategory'])->name('applications.reassign-category');
            
            Route::post('/applications/{application}/toggle-reviewed', [RegistrarController::class, 'toggleReviewed'])->name('applications.toggle-reviewed');
            Route::post('/applications/{application}/flag-anomaly', [RegistrarController::class, 'flagApplication'])->name('applications.flag-anomaly');
            Route::post('/applications/{application}/message-officer', [RegistrarController::class, 'sendMessageToOfficer'])->name('applications.message-officer');

            // Accreditation / Registration lists
            Route::get('/{type}/applications/{bucket}', [RegistrarController::class, 'applicationsList'])
                ->whereIn('type', ['accreditation','registration'])
                ->whereIn('bucket', ['all','new','under-review','approved','rejected','corrections'])
                ->name('apps.list');

            // Renewals (AP5)
            Route::get('/renewals/{bucket}', [RegistrarController::class, 'renewalsList'])
                ->whereIn('bucket', ['due-soon','submitted','renewed-expired'])
                ->name('renewals.list');

            Route::get('/applications/{application}', [RegistrarController::class, 'show'])->name('applications.show');
            // Route::post('/applications/{application}/approve', [RegistrarController::class, 'approve'])->name('applications.approve');
            // Route::post('/applications/{application}/reject', [RegistrarController::class, 'reject'])->name('applications.reject');
            // Route::post('/applications/{application}/return', [RegistrarController::class, 'returnToAccounts'])->name('applications.return');
            Route::post('/renewals/send-reminders', [RegistrarController::class, 'sendRenewalReminders'])->name('renewals.send-reminders');

            // Route::post('/applications/{application}/fix-request', [RegistrarController::class, 'raiseFixRequest'])->name('applications.fix-request');
            // Route::post('/applications/{application}/push-to-accounts', [RegistrarController::class, 'pushToAccounts'])->name('applications.push-to-accounts');

            Route::get('/accounts-oversight', [RegistrarController::class, 'accountsOversight'])->name('accounts-oversight');

            Route::get('/reminders', [RegistrarController::class, 'remindersIndex'])->name('reminders.index');
            Route::post('/reminders', [RegistrarController::class, 'createReminder'])->name('reminders.store');

            // Notices & Events (Read-only access)
            Route::get('/notices-events', [RegistrarController::class, 'noticesEvents'])->name('notices-events');
            
            // News / Press Statements (Read-only access)
            Route::get('/news', [RegistrarController::class, 'news'])->name('news');
        });

    /*
    |--------------------------------------------------------------------------
    | STAFF - ACCOUNTS/PAYMENTS
    |--------------------------------------------------------------------------
    */
    Route::middleware(['staff.portal','role:accounts_payments','block.director.operational'])
        ->prefix('staff/accounts')
        ->name('staff.accounts.')
        ->group(function () {
            Route::get('/', [AccountsPaymentsController::class, 'dashboard'])->name('dashboard');

            // Routes expected by sidebar
            Route::get('/paynow/transactions', [AccountsPaymentsController::class, 'paynowTransactions'])->name('paynow.transactions');
            Route::get('/reconciliation', [AccountsPaymentsController::class, 'reconciliation'])->name('reconciliation');
            Route::get('/proofs/pending', [AccountsPaymentsController::class, 'proofsPending'])->name('proofs.pending');

            // --- ACCOUNTS DASHBOARD - New Core Modules ---
            Route::get('/payments', [AccountsPaymentsController::class, 'index'])->name('payments.index');
            Route::get('/payments/retry/{payment}', [AccountsPaymentsController::class, 'retryPaymentStatus'])->name('payments.retry');
            Route::get('/payments/offline/create', [AccountsPaymentsController::class, 'createOffline'])->name('payments.offline.create');
            Route::post('/payments/offline/store', [AccountsPaymentsController::class, 'storeOffline'])->name('payments.offline.store');
            Route::post('/payments/{payment}/reverse', [AccountsPaymentsController::class, 'reverse'])->name('payments.reverse');
            Route::post('/payments/{payment}/refund', [AccountsPaymentsController::class, 'initiateRefund'])->name('payments.refund');
            Route::post('/refunds/{refund}/approve', [AccountsPaymentsController::class, 'approveRefund'])->name('refunds.approve');
            Route::get('/ledger', [AccountsPaymentsController::class, 'ledger'])->name('ledger');
            Route::post('/reconciliation/mark', [AccountsPaymentsController::class, 'markReconciled'])->name('reconciliation.mark');
            Route::get('/reports/financial', [AccountsPaymentsController::class, 'reportFinancial'])->name('reports.financial');
            // ----------------------------------------------

            // 1) Payments dashboard (PayNow feed)
            Route::get('/paynow-transactions', [AccountsPaymentsController::class, 'paynowTransactions'])->name('paynow.transactions');

            // 2) Payment proofs
            Route::get('/payment-proofs/pending', [AccountsPaymentsController::class, 'proofsPending'])->name('proofs.pending');
            Route::get('/payment-proofs/approved', [AccountsPaymentsController::class, 'proofsApproved'])->name('proofs.approved');
            Route::post('/payment-proofs/bulk-download', [AccountsPaymentsController::class, 'bulkDownloadProofs'])->name('proofs.bulk-download');

            // NEW: Batch Payments
            Route::get('/batches/pending', [AccountsPaymentsController::class, 'batchesPending'])->name('batches.pending');
            Route::post('/batches/{batch}/approve', [AccountsPaymentsController::class, 'approveBatch'])->name('batches.approve');
            Route::post('/batches/{batch}/reject', [AccountsPaymentsController::class, 'rejectBatch'])->name('batches.reject');
            Route::post('/applications/{application}/proof/approve', [AccountsPaymentsController::class, 'approveProof'])->name('proofs.approve');
            Route::post('/applications/{application}/proof/reject', [AccountsPaymentsController::class, 'rejectProof'])->name('proofs.reject');

            // Payment rejection
            Route::post('/applications/{application}/payment/reject', [AccountsPaymentsController::class, 'rejectPayment'])->name('applications.payment.reject');

            // Cash payments
            Route::get('/cash-payment/create', [AccountsPaymentsController::class, 'createCashPayment'])->name('cash-payment.create');
            Route::post('/cash-payment', [AccountsPaymentsController::class, 'storeCashPayment'])->name('cash-payment.store');
            Route::post('/cash-payment/{payment}/void', [AccountsPaymentsController::class, 'voidCashPayment'])->name('cash-payment.void');

            // Waiver verification (from Registrar)
            Route::post('/applications/{application}/waiver-verification/approve', [AccountsPaymentsController::class, 'approveWaiverVerification'])->name('waiver-verification.approve');
            Route::post('/applications/{application}/waiver-verification/reject', [AccountsPaymentsController::class, 'rejectWaiverVerification'])->name('waiver-verification.reject');

            // Receipting
            Route::get('/applications/{application}/receipt', [AccountsPaymentsController::class, 'generateReceipt'])->name('applications.receipt');

            // 3) Waivers
            Route::get('/waivers/requests', [AccountsPaymentsController::class, 'waiversRequests'])->name('waivers.requests');
            Route::get('/waivers/approved', [AccountsPaymentsController::class, 'waiversApproved'])->name('waivers.approved');
            Route::get('/waivers/rejected', [AccountsPaymentsController::class, 'waiversRejected'])->name('waivers.rejected');
            Route::post('/applications/{application}/waiver/approve', [AccountsPaymentsController::class, 'approveWaiver'])->name('waivers.approve');
            Route::post('/applications/{application}/waiver/reject', [AccountsPaymentsController::class, 'rejectWaiver'])->name('waivers.reject');

            // 4) Reconciliation
            Route::get('/reconciliation', [AccountsPaymentsController::class, 'reconciliation'])->name('reconciliation');

            // 5) Application status tracking
            Route::get('/applications/paid', [AccountsPaymentsController::class, 'applicationsPaid'])->name('apps.paid');
            Route::get('/applications/pending', [AccountsPaymentsController::class, 'applicationsPending'])->name('apps.pending');
            Route::get('/applications/waived', [AccountsPaymentsController::class, 'applicationsWaived'])->name('apps.waived');

            // 6) Reports
            Route::get('/reports/revenue', [AccountsPaymentsController::class, 'reportRevenue'])->name('reports.revenue');
            Route::get('/reports/export-ledger', [AccountsPaymentsController::class, 'exportLedger'])->name('reports.export-ledger');
            Route::get('/reports/exceptions', [AccountsPaymentsController::class, 'reportExceptions'])->name('reports.exceptions');
            Route::get('/reports/audit', [AccountsPaymentsController::class, 'reportAudit'])->name('reports.audit');

            // 6) Notifications & alerts
            Route::get('/alerts', [AccountsPaymentsController::class, 'alerts'])->name('alerts');

            // 7) System tools
            Route::get('/tools/paynow-settings', [AccountsPaymentsController::class, 'paynowSettings'])->name('tools.paynow');
            Route::get('/tools/user-action-logs', [AccountsPaymentsController::class, 'userActionLogs'])->name('tools.logs');

            // Help
            Route::get('/help', [AccountsPaymentsController::class, 'help'])->name('help');

            Route::get('/applications/{application}', [AccountsPaymentsController::class, 'show'])->name('applications.show');
            Route::post('/applications/{application}/paid', [AccountsPaymentsController::class, 'markPaid'])->name('applications.paid');
            Route::post('/applications/{application}/return', [AccountsPaymentsController::class, 'returnToOfficer'])->name('applications.return');
            Route::post('/applications/{application}/unlock', [AccountsPaymentsController::class, 'unlock'])->name('applications.unlock');
        });

    /*
    |--------------------------------------------------------------------------
    | STAFF - PRODUCTION
    |--------------------------------------------------------------------------
    */
    Route::middleware(['staff.portal','role:production|accreditation_officer','block.director.operational'])
        ->prefix('staff/production')
        ->name('staff.production.')
        ->group(function () {
            Route::get('/', [ProductionController::class, 'dashboard'])->name('dashboard');

            // Menu pages
            Route::get('/queue', [ProductionController::class, 'queue'])->name('queue');
            Route::get('/cards', [ProductionController::class, 'cards'])->name('cards');
            Route::get('/certificates', [ProductionController::class, 'certificates'])->name('certificates');
            Route::get('/printing', [ProductionController::class, 'printing'])->name('printing');
            Route::get('/issuance', [ProductionController::class, 'issuance'])->name('issuance');
            Route::get('/registers/issued', [ProductionController::class, 'issuedRegister'])->name('registers.issued');
            Route::get('/reports', [ProductionController::class, 'reports'])->name('reports');

            Route::get('/applications/{application}', [ProductionController::class, 'show'])->name('applications.show');

            // Preview/edit before generating & printing
            Route::get('/applications/{application}/card/preview', [ProductionController::class, 'cardPreview'])->name('applications.card.preview');
            Route::post('/applications/{application}/card/print', [ProductionController::class, 'cardPrint'])->name('applications.card.print');
            Route::post('/applications/{application}/card/print-back', [ProductionController::class, 'cardPrintBack'])->name('applications.card.print_back');

            Route::get('/applications/{application}/certificate/preview', [ProductionController::class, 'certificatePreview'])->name('applications.certificate.preview');
            Route::post('/applications/{application}/certificate/print', [ProductionController::class, 'certificatePrint'])->name('applications.certificate.print');

            Route::post('/applications/{application}/generate-card', [ProductionController::class, 'generateCard'])->name('applications.generate_card');
            Route::post('/applications/{application}/generate-certificate', [ProductionController::class, 'generateCertificate'])->name('applications.generate_certificate');
            Route::post('/applications/{application}/print', [ProductionController::class, 'printSingle'])->name('applications.print_single');
            Route::post('/batch/print', [ProductionController::class, 'printBatch'])->name('batch.print');
            Route::post('/applications/{application}/issue', [ProductionController::class, 'markIssued'])->name('applications.issue');
            Route::post('/applications/{application}/unlock', [ProductionController::class, 'unlock'])->name('applications.unlock');

            Route::get('/designer', [ProductionController::class, 'designer'])->name('designer');
            Route::get('/templates', [ProductionController::class, 'templates'])->name('templates');
            Route::post('/templates', [ProductionController::class, 'storeTemplate'])->name('templates.store');
            Route::put('/templates/{template}', [ProductionController::class, 'updateTemplate'])->name('templates.update');
            Route::post('/templates/{template}/activate', [ProductionController::class, 'activateTemplate'])->name('templates.activate');
        });

    /*
    |--------------------------------------------------------------------------
    | STAFF - IT ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['staff.portal','role:it_admin|super_admin'])
        ->prefix('staff/it')
        ->name('staff.it.')
        ->group(function () {
            Route::get('/', [ItDashboardController::class, 'index'])->name('dashboard');
            Route::get('/users/create', [ItAdminController::class, 'createUser'])->name('users.create');
            Route::post('/users', [ItAdminController::class, 'storeUser'])->name('users.store');

            // Regions
            Route::post('/regions', [ItAdminController::class, 'storeRegion'])->name('regions.store');
            Route::post('/regions/{region}/toggle', [ItAdminController::class, 'toggleRegion'])->name('regions.toggle');

            // Content Management - MOVED TO PR ROLE

            // Applicant resets
            Route::get('/applicants', [ItAdminController::class, 'listApplicants'])->name('applicants.list');
            Route::post('/applicants/{user}/reset', [ItAdminController::class, 'resetApplicant'])->name('applicants.reset');

            // Unified Dashboard Routes
            Route::get('/monitoring', [ItDashboardController::class, 'monitoring'])->name('monitoring');
            Route::get('/drafts', [ItDashboardController::class, 'drafts'])->name('drafts');
            Route::get('/files', [ItDashboardController::class, 'files'])->name('files');
            Route::get('/errors', [ItDashboardController::class, 'errors'])->name('errors');
            Route::get('/users-mgmt', [ItDashboardController::class, 'users'])->name('users-mgmt');
            Route::get('/workflow-mgmt', [ItDashboardController::class, 'workflow'])->name('workflow-mgmt');
            Route::get('/accreditation-mgmt', [ItDashboardController::class, 'accreditation'])->name('accreditation-mgmt');
            Route::get('/notifications-mgmt', [ItDashboardController::class, 'notifications'])->name('notifications-mgmt');
            Route::get('/payments-mgmt', [ItDashboardController::class, 'payments'])->name('payments-mgmt');
            Route::get('/security-mgmt', [ItDashboardController::class, 'security'])->name('security-mgmt');
            Route::get('/backup-mgmt', [ItDashboardController::class, 'backup'])->name('backup-mgmt');
            Route::get('/audit-mgmt', [ItDashboardController::class, 'audit'])->name('audit-mgmt');
            Route::get('/system-mgmt', [ItDashboardController::class, 'system'])->name('system-mgmt');
            Route::get('/performance-mgmt', [ItDashboardController::class, 'performance'])->name('performance-mgmt');
            Route::get('/reports-mgmt', [ItDashboardController::class, 'reports'])->name('reports-mgmt');
            Route::get('/mediahouses-mgmt', [ItDashboardController::class, 'mediahouses'])->name('mediahouses-mgmt');

            // Read-only application detail + timeline
            Route::get('/application-overview/{application}', [ItDashboardController::class, 'showApplication'])->name('application.overview');
            Route::get('/application-overview/{application}/download-batch', [ItDashboardController::class, 'downloadBatch'])->name('application.download_batch');

            // Actions
            Route::post('/application/{application}/unlock', [ItDashboardController::class, 'unlockApplication'])->name('application.unlock');
            Route::post('/application/{application}/reset', [ItDashboardController::class, 'resetApplication'])->name('application.reset');
            Route::post('/user/{user}/suspend', [ItDashboardController::class, 'suspendUser'])->name('user.suspend');
            Route::post('/user/{user}/reset-password', [ItDashboardController::class, 'forcePasswordReset'])->name('user.reset_password');
            Route::post('/config/save', [ItDashboardController::class, 'saveConfig'])->name('config.save');
            Route::post('/fees/sync', [ItDashboardController::class, 'syncFees'])->name('fees.sync');
            Route::post('/fees/save', [ItDashboardController::class, 'saveFee'])->name('fees.save');
            Route::post('/notifications/template/save', [ItDashboardController::class, 'saveNotificationTemplate'])->name('notifications.template.save');
            Route::post('/payments/process-queue', [ItDashboardController::class, 'processPaymentQueue'])->name('payments.process_queue');
            Route::post('/system/backup', [ItDashboardController::class, 'triggerBackup'])->name('system.backup');
            Route::post('/system/clear-cache', [ItDashboardController::class, 'clearCache'])->name('system.clear_cache');
            Route::post('/system/cleanup', [ItDashboardController::class, 'runCleanup'])->name('system.cleanup');
            Route::post('/security/session/{id}/logout', [ItDashboardController::class, 'logoutSession'])->name('security.session.logout');
            Route::post('/security/ip/block', [ItDashboardController::class, 'blockIp'])->name('security.ip.block');
            Route::post('/security/toggle-rate-limiting', [ItDashboardController::class, 'toggleRateLimiting'])->name('security.toggle_rate_limiting');
            Route::post('/security/ssl-audit', [ItDashboardController::class, 'sslAudit'])->name('security.ssl_audit');
            Route::get('/reports/generate/{type}', [ItDashboardController::class, 'generateReport'])->name('reports.generate');

            // Legacy Redirects
            Route::get('/command-center', fn() => redirect()->route('staff.it.dashboard'));
        });

    /*
    |--------------------------------------------------------------------------
    | STAFF - AUDITOR (READ ONLY)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['staff.portal','role:auditor|super_admin'])
        ->prefix('staff/auditor')
        ->name('staff.auditor.')
        ->group(function () {
            Route::get('/', [AuditorController::class, 'dashboard'])->name('dashboard');

            // Analytics (charts)
            Route::get('/analytics', [AuditorController::class, 'analytics'])->name('analytics');

            // User logins + full user activity trails
            Route::get('/logins', [AuditorController::class, 'logins'])->name('logins');
            Route::get('/logins.csv', [AuditorController::class, 'loginsCsv'])->name('logins.csv');

            // 1) Application & accreditation audits
            Route::get('/applications', [AuditorController::class, 'applications'])->name('applications');
            Route::get('/applications.csv', [AuditorController::class, 'applicationsCsv'])->name('applications.csv');

            // 2) Financial & payment auditing (read-only)
            Route::get('/payments/paynow', [AuditorController::class, 'paynow'])->name('paynow');
            Route::get('/payments/paynow.csv', [AuditorController::class, 'paynowCsv'])->name('paynow.csv');
            Route::get('/payments/proofs', [AuditorController::class, 'proofs'])->name('proofs');
            Route::get('/payments/proofs.csv', [AuditorController::class, 'proofsCsv'])->name('proofs.csv');
            Route::get('/payments/waivers', [AuditorController::class, 'waivers'])->name('waivers');
            Route::get('/payments/waivers.csv', [AuditorController::class, 'waiversCsv'])->name('waivers.csv');

            // 4) Activity logs
            Route::get('/logs', [AuditorController::class, 'logs'])->name('logs');
            Route::get('/logs.csv', [AuditorController::class, 'logsCsv'])->name('logs.csv');


            // 5) Reporting
            Route::get('/reports', [AuditorController::class, 'reports'])->name('reports');
            Route::get('/reports.csv', [AuditorController::class, 'reportsCsv'])->name('reports.csv');

            // 6) Security oversight
            Route::get('/security', [AuditorController::class, 'security'])->name('security');
            Route::get('/security.csv', [AuditorController::class, 'securityCsv'])->name('security.csv');

            // Activity feed export
            Route::get('/activity.csv', [AuditorController::class, 'activityCsv'])->name('activity.csv');

            // Flag anomalies (Auditor-only)
            Route::post('/flag', [AuditorController::class, 'flag'])->name('flag');
        });

    /*
    |--------------------------------------------------------------------------
    | STAFF - DIRECTOR (Executive Dashboard)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['staff.portal','role:director'])
        ->prefix('staff/director')
        ->name('staff.director.')
        ->group(function () {
            Route::get('/', [DirectorController::class, 'dashboard'])->name('dashboard');
            Route::get('/reports/accreditation', [DirectorController::class, 'accreditationPerformance'])->name('reports.accreditation');
            Route::get('/reports/financial', [DirectorController::class, 'financialOverview'])->name('reports.financial');
            Route::get('/reports/compliance', [DirectorController::class, 'complianceRisk'])->name('reports.compliance');
            Route::get('/reports/mediahouses', [DirectorController::class, 'mediaHouseOversight'])->name('reports.mediahouses');
            Route::get('/reports/staff', [DirectorController::class, 'staffPerformance'])->name('reports.staff');
            Route::get('/reports/issuance', [DirectorController::class, 'issuanceOversight'])->name('reports.issuance');
            Route::get('/reports/geographic', [DirectorController::class, 'geographicDistribution'])->name('reports.geographic');
            Route::get('/reports/downloads', [DirectorController::class, 'reportsDownloads'])->name('reports.downloads');
            
            // Report Generation Routes
            Route::post('/generate/monthly-accreditation', [DirectorController::class, 'generateMonthlyAccreditationReport'])->name('generate.monthly-accreditation');
            Route::post('/generate/revenue-financial', [DirectorController::class, 'generateRevenueFinancialReport'])->name('generate.revenue-financial');
            Route::post('/generate/compliance-audit', [DirectorController::class, 'generateComplianceAuditReport'])->name('generate.compliance-audit');
            Route::post('/generate/mediahouse-status', [DirectorController::class, 'generateMediaHouseStatusReport'])->name('generate.mediahouse-status');
            Route::post('/generate/operational-performance', [DirectorController::class, 'generateOperationalPerformanceReport'])->name('generate.operational-performance');
        });

    /*
    |--------------------------------------------------------------------------
    | USER APPROVALS (Director / Super Admin)
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | CHATBOT
    |--------------------------------------------------------------------------
    */
    Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');
});

// Public Verification
Route::get('/verify/{token}', [\App\Http\Controllers\PublicVerificationController::class, 'verify'])->name('public.verify');
