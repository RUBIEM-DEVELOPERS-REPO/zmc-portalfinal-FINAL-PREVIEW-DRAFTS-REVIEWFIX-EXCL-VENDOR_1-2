<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Notifications\StaffAccountSetupNotification;

class UserAccessController extends Controller
{
    /**
     * /admin/users
     *
     * Landing page that routes users to the dedicated lists.
     * The user request was to have Public Users and Staff Users in their own lists.
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.users.staff');
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
        $regions = \App\Models\Region::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'regions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'designation' => ['nullable', 'string', 'max:255'],
            'phone_country_code' => ['required', 'string', 'exists:countries,code'],
            'phone_number' => ['required', 'string', 'min:6', 'max:20'],
            'country_code' => ['nullable', 'string', 'exists:countries,code'],
            'regions' => ['nullable', 'array'],
            'regions.*' => ['integer', 'exists:regions,id'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string'],
        ]);

        $setupToken = \Illuminate\Support\Str::random(64);
        $tempPassword = \Illuminate\Support\Str::random(12);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($tempPassword), // Use temporary password
            'designation' => $data['designation'] ?? null,
            'phone_country_code' => $data['phone_country_code'],
            'phone_number' => $data['phone_number'],
            'country_code' => $data['country_code'] ?? $data['phone_country_code'],
            'setup_token' => $setupToken,
            'account_type' => 'staff',
        ]);

        // Force account_status to pending_setup (override migration default)
        $user->account_status = 'pending_setup';
        $user->save();

        $user->syncRoles($data['roles'] ?? []);

        // Assign regions to the user
        if (!empty($data['regions'])) {
            $user->assignedRegions()->sync($data['regions']);
        }

        // Send Setup Notification with temporary password
        $user->notify(new \App\Notifications\StaffAccountSetupNotification($setupToken, $tempPassword));

        $action = (auth()->user() && auth()->user()->hasRole('super_admin')) ? 'account_created_by_superadmin' : 'account_created_by_it_admin';
        \App\Support\AuditTrail::log($action, $user, ['roles' => $data['roles'] ?? []]);

        return redirect()->route('admin.users.staff')->with('success', 'User created and invite sent.');
    }

    public function destroy(User $user)
    {
        // Prevent super admin from deleting themselves
        if (auth()->user()->id === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        // Only super admin can delete users
        if (!auth()->user()->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'Only Super Admin can delete users.');
        }

        // Prevent deletion of super admin users (except by themselves)
        if ($user->hasRole('super_admin')) {
            return redirect()->back()->with('error', 'Super Admin users cannot be deleted.');
        }

        // Log the deletion
        \App\Support\AuditTrail::log('user_deleted', $user, [
            'deleted_by' => auth()->user()->id,
            'deleted_at' => now()
        ]);

        // Delete the user
        $user->delete();

        return redirect()->route('admin.users.staff')->with('success', 'User deleted successfully.');
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

        $action = auth()->user()->hasRole('super_admin') ? 'user_access_updated_by_superadmin' : 'user_access_updated_by_it_admin';
        \App\Support\AuditTrail::log($action, $user, [
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
}
