<?php

use Illuminate\Support\Facades\Route;

// System health (used by IT Admin Dashboard uptime indicator)
Route::get('/health', \App\Http\Controllers\HealthController::class)->name('system.health');
use Illuminate\Http\Request;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortalController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;

use App\Http\Controllers\AccreditationPortalController;
use App\Http\Controllers\MediaHousePortalController;
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
Route::post('/choose-portal', function (Request $request) {
    $data = $request->validate([
        'portal' => ['required', 'in:journalist,mass_media'],
    ]);

    $request->session()->put('public_selected_portal', $data['portal']);

    // Force public auth (login/signup)
    return redirect()->route('login');
})->name('public.choose_portal');

/*
|--------------------------------------------------------------------------
| STAFF ENTRY (STRICT)
|--------------------------------------------------------------------------
| /staff is staff landing page (role tiles)
| staff login is separate from public.
*/
Route::get('/staff', [RoleSelectController::class, 'index'])->name('staff.entry');
Route::get('/staff/select-role', [RoleSelectController::class, 'index'])->name('staff.select_role');
Route::post('/staff/select-role', [RoleSelectController::class, 'choose'])->name('staff.choose_role');

Route::get('/staff/login', [StaffAuthController::class, 'show'])->name('staff.login');
Route::post('/staff/login', [StaffAuthController::class, 'login'])->middleware('guest')->name('staff.login.store');
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
    Route::post('/settings/security', [\App\Http\Controllers\SettingsController::class, 'updateSecurity'])->name('settings.security');
    Route::post('/settings/notifications', [\App\Http\Controllers\SettingsController::class, 'updateNotifications'])->name('settings.notifications');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | AFTER LOGIN ROUTER
    |--------------------------------------------------------------------------
    | routes user into portal chosen on public landing page
    */
    Route::get('/home', function (Request $request) {

        $selected = $request->session()->get('public_selected_portal');

        if ($selected === 'journalist') {
            $request->session()->forget('public_selected_portal');
            return redirect()->route('accreditation.home');
        }

        if ($selected === 'mass_media') {
            $request->session()->forget('public_selected_portal');
            return redirect()->route('mediahouse.portal');
        }

        // fallback if user didn't choose (or direct login)
        return redirect()->route('portal');

    })->name('home');

    /*
    |--------------------------------------------------------------------------
    | Authenticated portal hub (optional)
    |--------------------------------------------------------------------------
    */
    Route::get('/portal', [PortalController::class, 'index'])->name('portal');
    Route::post('/notifications/mark-read', [PortalController::class, 'markNotificationsRead'])->name('notifications.markRead');
    Route::post('/notifications/{id}/mark-read', [PortalController::class, 'markNotificationRead'])->name('notifications.markOne');

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

            // AP5
            Route::get('/renewals',          [AccreditationPortalController::class, 'renewals'])->name('renewals');
            Route::post('/renewals/save-draft', [AccreditationPortalController::class, 'saveDraftAp5'])->name('renewals.saveDraft');
            Route::post('/renewals/submit',  [AccreditationPortalController::class, 'submitAp5'])->name('submitAp5');

            // Lookup by accreditation number for renewals/replacements
            Route::get('/lookup/{accreditationNumber}', [AccreditationPortalController::class, 'lookupAccreditation'])
                ->name('lookup');

            Route::get('/payments',  [AccreditationPortalController::class, 'payments'])->name('payments');
            Route::get('/notices',   [AccreditationPortalController::class, 'notices'])->name('notices');
            Route::get('/howto',     [AccreditationPortalController::class, 'howto'])->name('howto');
            Route::get('/profile',   [AccreditationPortalController::class, 'profile'])->name('profile');
            Route::post('/profile',  [AccreditationPortalController::class, 'updateProfile'])->name('profile.update');
            Route::get('/settings',  [AccreditationPortalController::class, 'settings'])->name('settings');

            // Downloads
            Route::get('/downloads', [\App\Http\Controllers\Portal\DownloadsController::class, 'index'])
                ->name('downloads');
            Route::get('/downloads/file/{doc}', [\App\Http\Controllers\Portal\DownloadsController::class, 'download'])
                ->name('downloads.file');
            
            // Renewals (Journalist)
            Route::get('/renewals', [\App\Http\Controllers\Portal\RenewalController::class, 'index'])
                ->name('renewals.index');
            Route::get('/renewals/select-type', [\App\Http\Controllers\Portal\RenewalController::class, 'selectType'])
                ->name('renewals.select-type');
            Route::post('/renewals/select-type', [\App\Http\Controllers\Portal\RenewalController::class, 'storeType'])
                ->name('renewals.store-type');
            Route::get('/renewals/{renewal}/lookup', [\App\Http\Controllers\Portal\RenewalController::class, 'lookup'])
                ->name('renewals.lookup');
            Route::post('/renewals/{renewal}/lookup', [\App\Http\Controllers\Portal\RenewalController::class, 'performLookup'])
                ->name('renewals.perform-lookup');
            Route::get('/renewals/{renewal}/confirm', [\App\Http\Controllers\Portal\RenewalController::class, 'confirm'])
                ->name('renewals.confirm');
            Route::post('/renewals/{renewal}/confirm-no-changes', [\App\Http\Controllers\Portal\RenewalController::class, 'confirmNoChanges'])
                ->name('renewals.confirm-no-changes');
            Route::post('/renewals/{renewal}/submit-changes', [\App\Http\Controllers\Portal\RenewalController::class, 'submitChanges'])
                ->name('renewals.submit-changes');
            Route::get('/renewals/{renewal}/payment', [\App\Http\Controllers\Portal\RenewalController::class, 'payment'])
                ->name('renewals.payment');
            Route::post('/renewals/{renewal}/payment/paynow', [\App\Http\Controllers\Portal\RenewalController::class, 'submitPaynow'])
                ->name('renewals.payment.paynow');
            Route::post('/renewals/{renewal}/payment/proof', [\App\Http\Controllers\Portal\RenewalController::class, 'submitProof'])
                ->name('renewals.payment.proof');
            Route::get('/renewals/{renewal}', [\App\Http\Controllers\Portal\RenewalController::class, 'show'])
                ->name('renewals.show');
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

            // Staff Management (Linking Journalists)
            Route::get('/staff-members', [\App\Http\Controllers\MediaHouseStaffController::class, 'index'])->name('staff.index');
            Route::post('/staff-members/link', [\App\Http\Controllers\MediaHouseStaffController::class, 'link'])->name('staff.link');
            Route::delete('/staff-members/{staff}', [\App\Http\Controllers\MediaHouseStaffController::class, 'unlink'])->name('staff.unlink');

            // NEW RENEWAL FLOW (AP5) - Number-Only Lookup
            Route::get('/renewals', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'index'])
                ->name('renewals.index');
            Route::get('/renewals/select-type', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'selectType'])
                ->name('renewals.select-type');
            Route::post('/renewals/select-type', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'storeType'])
                ->name('renewals.store-type');
            Route::get('/renewals/{renewal}/lookup', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'lookup'])
                ->name('renewals.lookup');
            Route::post('/renewals/{renewal}/lookup', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'performLookup'])
                ->name('renewals.perform-lookup');
            Route::get('/renewals/{renewal}/confirm', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'confirm'])
                ->name('renewals.confirm');
            Route::post('/renewals/{renewal}/confirm-no-changes', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'confirmNoChanges'])
                ->name('renewals.confirm-no-changes');
            Route::post('/renewals/{renewal}/submit-changes', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'submitChanges'])
                ->name('renewals.submit-changes');
            Route::get('/renewals/{renewal}/payment', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'payment'])
                ->name('renewals.payment');
            Route::post('/renewals/{renewal}/payment/paynow', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'submitPaynow'])
                ->name('renewals.payment.paynow');
            Route::post('/renewals/{renewal}/payment/proof', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'submitProof'])
                ->name('renewals.payment.proof');
            Route::get('/renewals/{renewal}', [\App\Http\Controllers\Portal\MediaHouseRenewalController::class, 'show'])
                ->name('renewals.show');

            Route::get('/payments',    [MediaHousePortalController::class, 'payments'])->name('payments');
            Route::get('/notices',     [MediaHousePortalController::class, 'notices'])->name('notices');
            Route::get('/howto',       [MediaHousePortalController::class, 'howto'])->name('howto');
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
            
            // Official Letter Download (Two-Stage Payment)
            Route::get('/applications/{application}/official-letter', [MediaHousePortalController::class, 'downloadOfficialLetter'])
                ->name('download-official-letter');
            
            // Two-Stage Payment Submissions
            Route::post('/applications/{application}/payment/application-fee/paynow', [MediaHousePortalController::class, 'submitApplicationFeePaynow'])
                ->name('payment.app-fee.paynow');
            Route::post('/applications/{application}/payment/application-fee/proof', [MediaHousePortalController::class, 'submitApplicationFeeProof'])
                ->name('payment.app-fee.proof');
            Route::post('/applications/{application}/payment/registration-fee/paynow', [MediaHousePortalController::class, 'submitRegistrationFeePaynow'])
                ->name('payment.reg-fee.paynow');
            Route::post('/applications/{application}/payment/registration-fee/proof', [MediaHousePortalController::class, 'submitRegistrationFeeProof'])
                ->name('payment.reg-fee.proof');
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
    Route::post('/payments/{application}/submit-reference', [\App\Http\Controllers\Portal\PaynowController::class, 'submitReference'])
        ->name('paynow.submit_reference');

    /*
    |--------------------------------------------------------------------------
    | MANUAL PAYMENTS (Proof / Waiver uploads)
    |--------------------------------------------------------------------------
    */
    Route::post('/payments/{application}/upload-proof', [\App\Http\Controllers\Portal\ManualPaymentController::class, 'uploadProof'])
        ->name('payments.upload_proof');
    Route::post('/payments/{application}/upload-waiver', [\App\Http\Controllers\Portal\ManualPaymentController::class, 'uploadWaiver'])
        ->name('payments.upload_waiver');

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
	                ->middleware(['role:super_admin|it_admin','module.enabled:notices'])
	                ->name('content.notices.store');
            Route::put('/content/notices/{notice}', [\App\Http\Controllers\Admin\ContentController::class, 'updateNotice'])
	                ->middleware(['role:super_admin|it_admin','module.enabled:notices'])
	                ->name('content.notices.update');
            Route::delete('/content/notices/{notice}', [\App\Http\Controllers\Admin\ContentController::class, 'destroyNotice'])
	                ->middleware(['role:super_admin|it_admin','module.enabled:notices'])
	                ->name('content.notices.destroy');

            Route::post('/content/events', [\App\Http\Controllers\Admin\ContentController::class, 'storeEvent'])
	                ->middleware(['role:super_admin|it_admin','module.enabled:events'])
	                ->name('content.events.store');
            Route::put('/content/events/{event}', [\App\Http\Controllers\Admin\ContentController::class, 'updateEvent'])
	                ->middleware(['role:super_admin|it_admin','module.enabled:events'])
	                ->name('content.events.update');
            Route::delete('/content/events/{event}', [\App\Http\Controllers\Admin\ContentController::class, 'destroyEvent'])
	                ->middleware(['role:super_admin|it_admin','module.enabled:events'])
	                ->name('content.events.destroy');


            // News (for website)
            Route::get('/news', [\App\Http\Controllers\Admin\NewsController::class, 'index'])
                ->middleware('module.enabled:news')
                ->name('news.index');
            Route::post('/news', [\App\Http\Controllers\Admin\NewsController::class, 'store'])
	                ->middleware(['role:super_admin|it_admin','module.enabled:news'])
	                ->name('news.store');
            Route::put('/news/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'update'])
	                ->middleware(['role:super_admin|it_admin','module.enabled:news'])
	                ->name('news.update');
            Route::delete('/news/{news}', [\App\Http\Controllers\Admin\NewsController::class, 'destroy'])
	                ->middleware(['role:super_admin|it_admin','module.enabled:news'])
	                ->name('news.destroy');

            // Complaints & Appeals (from website)
            Route::get('/complaints', [\App\Http\Controllers\Admin\ComplaintsController::class, 'index'])
                ->name('complaints.index');
            Route::put('/complaints/{complaint}', [\App\Http\Controllers\Admin\ComplaintsController::class, 'update'])
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
    Route::middleware(['staff.portal','role:accreditation_officer|accounts_payments|registrar|production'])
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

            // Applications (list views)
            Route::get('/applications', [AccreditationOfficerController::class, 'applicationsIndex'])->name('applications.index');
            Route::get('/applications-new', [AccreditationOfficerController::class, 'applicationsNew'])->name('applications.new');
            Route::get('/applications-pending', [AccreditationOfficerController::class, 'applicationsPending'])->name('applications.pending');
            Route::get('/applications-approved', [AccreditationOfficerController::class, 'applicationsApproved'])->name('applications.approved');
            Route::get('/applications-rejected', [AccreditationOfficerController::class, 'applicationsRejected'])->name('applications.rejected');
            // drafts remain only in applicant portal
            Route::get('/applications-bulk', [AccreditationOfficerController::class, 'applicationsBulk'])->name('applications.bulk');
            Route::get('/applications-export/{list}', [AccreditationOfficerController::class, 'applicationsExportCsv'])
                ->whereIn('list', ['all','new','pending','approved','rejected'])
                ->name('applications.export');

            // Records
            Route::get('/records/journalists', [AccreditationOfficerController::class, 'recordsJournalists'])->name('records.journalists');
            Route::get('/records/mediahouses', [AccreditationOfficerController::class, 'recordsMediaHouses'])->name('records.mediahouses');
            Route::get('/records/history', [AccreditationOfficerController::class, 'recordsHistory'])->name('records.history');
            // suspended/revoked page removed
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

            // Records management
            Route::get('/records/accredited-journalists', [AccreditationOfficerController::class, 'accreditedJournalists'])->name('records.accredited-journalists');
            Route::get('/records/registered-mediahouses', [AccreditationOfficerController::class, 'registeredMediaHouses'])->name('records.registered-mediahouses');
            Route::post('/records/send-collection-notification', [AccreditationOfficerController::class, 'sendCollectionNotification'])->name('records.send-collection-notification');

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
            
            // Fix Requests
            Route::get('/fix-requests', [AccreditationOfficerController::class, 'fixRequests'])->name('fix-requests');
            Route::post('/fix-requests/{fixRequest}/resolve', [AccreditationOfficerController::class, 'resolveFixRequest'])->name('fix-requests.resolve');
            
            // Forward Without Approval (Waiver/Special Cases)
            Route::post('/applications/{application}/forward-no-approval', [AccreditationOfficerController::class, 'forwardWithoutApproval'])->name('applications.forward-no-approval');
            
            // Renewals Production
            Route::get('/renewals-production', [AccreditationOfficerController::class, 'renewalsProductionQueue'])->name('renewals.production');
            Route::get('/renewals-production/{renewal}', [AccreditationOfficerController::class, 'showRenewalProduction'])->name('renewals.production.show');
            Route::post('/renewals-production/{renewal}/generate', [AccreditationOfficerController::class, 'generateRenewalDocument'])->name('renewals.production.generate');
            Route::post('/renewals-production/{renewal}/mark-produced', [AccreditationOfficerController::class, 'markRenewalProduced'])->name('renewals.production.mark-produced');
            Route::post('/renewals-production/{renewal}/print', [AccreditationOfficerController::class, 'printRenewalDocument'])->name('renewals.production.print');
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
            Route::get('/audit-trail', [RegistrarController::class, 'auditTrailSearch'])->name('audit-trail');
            Route::post('/applications/{application}/reassign-category', [RegistrarController::class, 'reassignCategory'])->name('applications.reassign-category');
            Route::post('/applications/{application}/approve-for-payment', [RegistrarController::class, 'approveForPayment'])->name('applications.approve-for-payment');

            // Accreditation / Registration lists
            Route::get('/{type}/applications/{bucket}', [RegistrarController::class, 'applicationsList'])
                ->whereIn('type', ['accreditation','registration'])
                ->whereIn('bucket', ['new','under-review','approved','rejected','corrections'])
                ->name('apps.list');

            // Renewals (AP5)
            Route::get('/renewals/{bucket}', [RegistrarController::class, 'renewalsList'])
                ->whereIn('bucket', ['due-soon','submitted','renewed-expired'])
                ->name('renewals.list');

            Route::get('/applications/{application}', [RegistrarController::class, 'show'])->name('applications.show');
            Route::post('/applications/{application}/approve', [RegistrarController::class, 'approve'])->name('applications.approve');
            Route::post('/applications/{application}/reject', [RegistrarController::class, 'reject'])->name('applications.reject');
            Route::post('/applications/{application}/return', [RegistrarController::class, 'returnToAccounts'])->name('applications.return');
            Route::post('/renewals/send-reminders', [RegistrarController::class, 'sendRenewalReminders'])->name('renewals.send-reminders');
            
            // Fix Requests
            Route::get('/fix-requests', [RegistrarController::class, 'fixRequests'])->name('fix-requests');
            Route::post('/applications/{application}/send-fix-request', [RegistrarController::class, 'sendFixRequest'])->name('applications.send-fix-request');
            
            // Special Cases (Forwarded Without Approval)
            Route::post('/applications/{application}/approve-special-case', [RegistrarController::class, 'approveSpecialCase'])->name('applications.approve-special-case');
            
            // Media House Two-Stage Payment: Official Letter Upload
            Route::post('/applications/{application}/approve-with-letter', [RegistrarController::class, 'approveWithOfficialLetter'])->name('applications.approve-with-letter');
            
            // Payment Oversight (Read-Only)
            Route::get('/payment-oversight', [RegistrarController::class, 'paymentOversight'])->name('payment-oversight');
            Route::get('/payment-oversight/{paymentSubmission}', [RegistrarController::class, 'paymentDetail'])->name('payment-detail');
            
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
            Route::post('/applications/{application}/proof/approve', [AccountsPaymentsController::class, 'approveProof'])->name('proofs.approve');
            Route::post('/applications/{application}/proof/reject', [AccountsPaymentsController::class, 'rejectProof'])->name('proofs.reject');

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
            
            // Payment Submission Verification (Waivers & Special Cases)
            Route::post('/applications/{application}/verify-payment', [AccountsPaymentsController::class, 'verifyPaymentSubmission'])->name('applications.verify-payment');
            
            // Renewals Queue
            Route::get('/renewals', [AccountsPaymentsController::class, 'renewalsQueue'])->name('renewals.queue');
            Route::get('/renewals/{renewal}', [AccountsPaymentsController::class, 'showRenewal'])->name('renewals.show');
            Route::post('/renewals/{renewal}/verify', [AccountsPaymentsController::class, 'verifyRenewalPayment'])->name('renewals.verify');
        });

    /*
    |--------------------------------------------------------------------------
    | STAFF - PRODUCTION
    |--------------------------------------------------------------------------
    */
    Route::middleware(['staff.portal','role:production','block.director.operational'])
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

            // Content Management (Notices, Events, Vacancies, Tenders)
            Route::post('/notices', [ContentController::class, 'storeNotice'])->name('notices.store');
            Route::post('/notices/{notice}', [ContentController::class, 'updateNotice'])->name('notices.update');
            Route::delete('/notices/{notice}', [ContentController::class, 'destroyNotice'])->name('notices.destroy');

            Route::post('/events', [ContentController::class, 'storeEvent'])->name('events.store');
            Route::post('/events/{event}', [ContentController::class, 'updateEvent'])->name('events.update');
            Route::delete('/events/{event}', [ContentController::class, 'destroyEvent'])->name('events.destroy');

            Route::post('/vacancies', [ContentController::class, 'storeVacancy'])->name('vacancies.store');
            Route::post('/vacancies/{vacancy}', [ContentController::class, 'updateVacancy'])->name('vacancies.update');
            Route::delete('/vacancies/{vacancy}', [ContentController::class, 'destroyVacancy'])->name('vacancies.destroy');

            Route::post('/tenders', [ContentController::class, 'storeTender'])->name('tenders.store');
            Route::post('/tenders/{tender}', [ContentController::class, 'updateTender'])->name('tenders.update');
            Route::delete('/tenders/{tender}', [ContentController::class, 'destroyTender'])->name('tenders.destroy');

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
    Route::middleware(['staff.portal','role:auditor|director|super_admin|registrar'])
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
    Route::middleware(['staff.portal','role:director','director.view_only'])
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
    Route::middleware(['staff.portal','role:director|super_admin|it_admin'])
        ->prefix('admin/approvals')
        ->name('admin.approvals.')
        ->group(function () {
            Route::get('/', [UserApprovalController::class, 'index'])->name('index');
            Route::post('/{user}/approve', [UserApprovalController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [UserApprovalController::class, 'reject'])->name('reject');
        });

    /*
    |--------------------------------------------------------------------------
    | CHATBOT
    |--------------------------------------------------------------------------
    */
    Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');
});

// Public Verification
Route::get('/verify/{token}', [\App\Http\Controllers\PublicVerificationController::class, 'verify'])->name('public.verify');
