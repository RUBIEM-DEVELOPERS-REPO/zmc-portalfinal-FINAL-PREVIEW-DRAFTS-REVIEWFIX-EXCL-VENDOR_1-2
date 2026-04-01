<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        abort_unless($user, 403);

        // Fetch recent login activity from AuditLog
        $loginActivity = \App\Models\AuditLog::where('actor_user_id', $user->id)
            ->where('action', 'login_applicant')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('settings.index', [
            'user' => $user,
            'loginActivity' => $loginActivity,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'profile' => ['required', 'array'],
            'profile.secondary_phone' => ['nullable', 'string', 'max:20'],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        // If email is changing, ensure it's unique
        if ($payload['email'] !== $user->email) {
            $request->validate([
                'email' => ['unique:users,email'],
            ]);
        }

        $user->update([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone_number' => $payload['phone_number'],
        ]);

        $profile = $user->profile_data ?? [];
        $profile = array_merge($profile, $payload['profile']);

        // Handle Profile Picture
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles/' . $user->id, 'public');
            $profile['profile_picture'] = $path;
        }

        $user->update(['profile_data' => $profile]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateSecurity(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $data = $request->validate([
            'two_factor_enabled' => ['required', 'boolean'],
        ]);

        $profile = $user->profile_data ?? [];
        $profile['two_factor_enabled'] = (bool)$data['two_factor_enabled'];
        $user->update(['profile_data' => $profile]);

        return back()->with('success', 'Security settings updated.');
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $data = $request->validate([
            'notifications' => ['required', 'array'],
        ]);

        $profile = $user->profile_data ?? [];
        // Support nested notifications object in profile_data
        $profile['notifications'] = array_merge($profile['notifications'] ?? [], $data['notifications']);
        $user->update(['profile_data' => $profile]);

        return back()->with('success', 'Notification preferences updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateTheme(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $data = $request->validate([
            'theme' => ['required', 'in:light,dark'],
        ]);

        $user->update(['theme' => $data['theme']]);

        return back()->with('success', 'Theme updated.');
    }

    public function updateThemeAjax(Request $request)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $data = $request->validate([
            'theme' => ['required', 'in:light,dark'],
        ]);

        $user->update(['theme' => $data['theme']]);

        return response()->json(['success' => true, 'theme' => $data['theme']]);
    }
}
