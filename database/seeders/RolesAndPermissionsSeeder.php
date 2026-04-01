<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Roles
        $roles = [
            'super_admin',
            'accreditation_officer',
            'registrar',
            'accounts_payments',
            'production',
            'it_admin',
            'auditor',
            'director',
            'complaints_officer',
            'pr',
            'public_info_compliance',
            'research_training_standards',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Permissions
        $permissions = [
            // Oversight
            'view_all_applications',
            'view_analytics',
            'view_audit_trail',

            // Operational application workflow
            'approve_application',
            'reject_application',
            'request_correction',
            'confirm_payment',
            'generate_cards',

            // User & access
            'manage_users',
            'approve_user_accounts',

            // Content
            'view_content',
            'manage_content',

            // News
            'view_news',
            'manage_news',

            // Downloads / reports
            'download_reports',

            // Complaints / appeals
            'receive_complaints_appeals',
            'manage_complaints_appeals',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Super admin gets everything
        Role::findByName('super_admin')->syncPermissions(Permission::all());

        // Defaults (can be adjusted from UI)
        Role::findByName('auditor')->syncPermissions(['view_audit_trail']);

        // Director: oversight rights, but NOT operational workflow
        Role::findByName('director')->syncPermissions([
            'view_all_applications',
            'view_analytics',
            'view_audit_trail',
            'approve_user_accounts',
            'view_content',
            'view_news',
            'receive_complaints_appeals',
            'download_reports',
        ]);

        // IT Admin & Super Admin: grant everything
        Role::findByName('super_admin')->syncPermissions(Permission::all());
        Role::findByName('it_admin')->syncPermissions(Permission::all());

        Role::findByName('complaints_officer')->syncPermissions([
            'receive_complaints_appeals',
            'manage_complaints_appeals',
        ]);
    }
}
