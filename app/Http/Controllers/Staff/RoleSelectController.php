<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleSelectController extends Controller
{
    public function index(Request $request)
    {
        $request->session()->forget('staff_selected_role');

        $roles = [
            [
                'key' => 'accreditation_officer',
                'title' => 'Accreditation Officer',
                'desc' => 'Review applications, verify documents, communicate with applicants, approve for payment or return for correction.',
                'icon' => 'ri-shield-user-line',
            ],
            [
                'key' => 'accounts_payments',
                'title' => 'Accounts / Payments',
                'desc' => 'Check waivers and payment proofs, receive payments via PayNow, approve to production or return for correction.',
                'icon' => 'ri-bank-card-line',
            ],
            [
                'key' => 'registrar',
                'title' => 'Registrar',
                'desc' => 'Supervisory oversight of approved applications. Review and mark applications as reviewed.',
                'icon' => 'ri-file-list-3-line',
            ],
            [
                'key' => 'production',
                'title' => 'Production Officer',
                'desc' => 'Generate press cards for media practitioners and certificates for media houses. Handle printing and issuance.',
                'icon' => 'ri-printer-line',
            ],
            [
                'key' => 'it_admin',
                'title' => 'IT Administrator',
                'desc' => 'User management, account creation, role and permission assignment. System administration and security oversight.',
                'icon' => 'ri-settings-3-line',
            ],
            [
                'key' => 'auditor',
                'title' => 'Auditor',
                'desc' => 'Read-only access to audit trails, financial oversight, compliance reporting across the entire workflow.',
                'icon' => 'ri-eye-line',
            ],
            [
                'key' => 'director',
                'title' => 'Director MDG',
                'desc' => 'Director Media Development and Governance. Strategic oversight of accreditation, registration, and compliance.',
                'icon' => 'ri-vip-crown-line',
            ],
            [
                'key' => 'pr_officer',
                'title' => 'Public Relations',
                'desc' => 'Manage notices, events, news, press statements, and downloadable content for the public portal.',
                'icon' => 'ri-megaphone-line',
            ],
            [
                'key' => 'public_info_compliance',
                'title' => 'Public Info & Compliance',
                'desc' => 'Handle public complaints and appeals. Monitor information compliance and public engagement.',
                'icon' => 'ri-chat-check-line',
            ],
            [
                'key' => 'research_training',
                'title' => 'Research & Training',
                'desc' => 'Research, training, and standards development. Monitor media standards and training programmes.',
                'icon' => 'ri-book-open-line',
            ],
            [
                'key' => 'chief_accountant',
                'title' => 'Chief Accountant',
                'desc' => 'Supervisory oversight of accounts and payments. Financial reporting and revenue oversight.',
                'icon' => 'ri-money-dollar-circle-line',
            ],
            [
                'key' => 'super_admin',
                'title' => 'Super Admin',
                'desc' => 'Complete system access including all roles, settings, and administrative functions. Highest privilege level.',
                'icon' => 'ri-admin-line',
            ],
        ];

        return view('staff.select-role', compact('roles'));
    }

    public function choose(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', 'string'],
        ]);

        $request->session()->put('staff_selected_role', $data['role']);

        return redirect()->route('staff.login', ['role' => $data['role']]);
    }
}
