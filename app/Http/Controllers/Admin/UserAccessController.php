<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserAccessController extends Controller
{
    /**
     * /admin/users
     *
     * Main User & Account Management page showing both Staff and Public users.
     */
    public function index(Request $request)
    {
        $q = $request->get('q');

        // Staff users query
        $staffQuery = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->where('account_type', 'staff')
            ->latest();

        // Public users query
        $publicQuery = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->where('account_type', 'public')
            ->latest();

        $staffUsers = $staffQuery->paginate(10, ['*'], 'staff_page')
            ->withQueryString();
        $publicUsers = $publicQuery->paginate(10, ['*'], 'public_page')
            ->withQueryString();

        $counts = [
            'staff'  => User::where('account_type', 'staff')->count(),
            'public' => User::where('account_type', 'public')->count(),
        ];

        return view('admin.users.index', compact('staffUsers', 'publicUsers', 'counts', 'q'));
    }

    /**
     * Staff users list.
     */
    public function staffIndex(Request $request)
    {
        return $this->renderList($request, 'staff');
    }

    /**
     * Public users list.
     */
    public function publicIndex(Request $request)
    {
        return $this->renderList($request, 'public');
    }

    /**
     * Shared list renderer.
     */
    private function renderList(Request $request, string $type)
    {
        $q = $request->get('q');

        $base = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->where('account_type', $type)
            ->latest();

        $users = (clone $base)
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'staff'  => User::where('account_type', 'staff')->count(),
            'public' => User::where('account_type', 'public')->count(),
        ];

        $title = $type === 'staff' ? 'Staff Users' : 'Public Users';

        return view('admin.users.list', compact('users', 'counts', 'q', 'type', 'title'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'designation' => ['nullable', 'string', 'max:255'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
        ]);

        $activationToken = Str::random(64);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)),
            'designation' => $data['designation'] ?? null,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'account_status' => 'pending',
            'account_type' => 'staff',
            'activation_token' => $activationToken,
        ]);

        $user->syncRoles($data['roles'] ?? []);

        $roleNames = implode(', ', $data['roles'] ?? []);
        $activationUrl = route('staff.activate', $activationToken);

        try {
            Mail::raw(
                "Hello {$user->name},\n\n"
                . "Your ZMC Staff account has been created with the role(s): {$roleNames}.\n\n"
                . "Please activate your account by clicking the link below and setting your password:\n\n"
                . "{$activationUrl}\n\n"
                . "This link is valid for one-time use.\n\n"
                . "Regards,\nZimbabwe Media Commission",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('ZMC Staff Account - Activate Your Account');
                }
            );
        } catch (\Throwable $e) {
            \Log::warning('Activation email failed for ' . $user->email . ': ' . $e->getMessage());
            return redirect()->route('admin.users.staff')
                ->with('success', "User created but activation email could not be sent. You can resend it from User Management.")
                ->with('error', 'Email delivery failed: ' . $e->getMessage());
        }

        \App\Support\AuditTrail::log('account_created_by_superadmin', $user, ['roles' => $data['roles'] ?? []]);

        return redirect()->route('admin.users.staff')->with('success', "Staff account created. Activation link sent to {$user->email}.");
    }

    public function editAccess(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        $userRoleNames = $user->roles->pluck('name')->toArray();
        $userPermNames = $user->permissions->pluck('name')->toArray();

        return view('admin.users.access', compact(
            'user',
            'roles',
            'permissions',
            'userRoleNames',
            'userPermNames'
        ));
    }

    public function updateAccess(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'designation' => ['nullable', 'string', 'max:255'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->designation = $data['designation'] ?? null;
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        $user->syncRoles($data['roles'] ?? []);
        $user->syncPermissions($data['permissions'] ?? []);

        \App\Support\AuditTrail::log('user_access_updated', $user, [
            'roles' => $data['roles'] ?? [],
            'permissions' => $data['permissions'] ?? []
        ]);

        return back()->with('success', 'User account and access updated.');
    }

    public function resetAccount(User $user)
    {
        $token = \Illuminate\Support\Str::random(64);

        $user->forceFill([
            'setup_token' => $token,
            'account_status' => 'pending_setup',
            'password' => Hash::make(\Illuminate\Support\Str::random(32)), // Deactivate current password
        ])->save();

        \App\Support\AuditTrail::log('account_reset_initiated', $user, ['token' => $token]);

        return back()->with('success', 'Account reset initiated. Share the setup link with the user.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->hasRole('super_admin') && User::role('super_admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last super admin account.');
        }

        $userName = $user->name;
        $userEmail = $user->email;

        \App\Support\AuditTrail::log('user_deleted', $user, [
            'deleted_name' => $userName,
            'deleted_email' => $userEmail,
        ]);

        $user->roles()->detach();
        $user->permissions()->detach();
        $user->delete();

        return back()->with('success', "User \"{$userName}\" ({$userEmail}) has been permanently deleted.");
    }
}
