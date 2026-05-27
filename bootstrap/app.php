<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('staff/*') || $request->is('admin/*')) {
                return route('staff.login');
            }
            return route('login');
        });
        
        $middleware->alias([
            // ✅ Correct Spatie namespaces
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,

            // Optional: keep your old middleware under a different alias
            'legacy_role' => \App\Http\Middleware\RoleMiddleware::class,

            // Portal separation (avoid staff session leaking into applicant portal and vice-versa)
            'applicant.portal' => \App\Http\Middleware\EnsureApplicantPortal::class,
            'staff.portal'     => \App\Http\Middleware\EnsureStaffPortal::class,

            // Module toggles
            'module.enabled'   => \App\Http\Middleware\EnsureModuleEnabled::class,

            // Director has oversight rights, but must not run operational workflows
            'block.director.operational' => \App\Http\Middleware\BlockDirectorOperationalRoles::class,
            'director.view_only' => \App\Http\Middleware\DirectorViewOnly::class,

            // Workflow enforcement (ZMC v2)
            'workflow.enforce' => \App\Http\Middleware\EnforceWorkflowTransitions::class,
            'role.access' => \App\Http\Middleware\EnforceRoleBasedAccess::class,
        ]);

        $middleware->appendToGroup('web', [
            \App\Http\Middleware\TokenAuth::class,
            \App\Http\Middleware\AddRequestId::class,
            \App\Http\Middleware\ZmcGatekeeper::class,
        ]);

        $middleware->priority([
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\TokenAuth::class,
            \Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests::class,
            \Illuminate\Routing\Middleware\ThrottleRequests::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Central error logging to database (system_logs)
        $exceptions->reportable(function (Throwable $e) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasTable('system_logs')) {
                    \App\Models\SystemLog::create([
                        'level' => 'error',
                        'category' => 'API',
                        'message' => $e->getMessage(),
                        'stack_trace' => (string) $e,
                        'user_id' => auth()->id(),
                        'ip' => request()?->ip(),
                        'request_id' => request()?->headers->get('X-Request-Id'),
                    ]);
                }
            } catch (Throwable $ignore) {
                // Never block exception reporting
            }
        });
    })
    ->create();
