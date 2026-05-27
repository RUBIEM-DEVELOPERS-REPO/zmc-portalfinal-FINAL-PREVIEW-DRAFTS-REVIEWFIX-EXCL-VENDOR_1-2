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
            'pr_officer',
            'public_info_compliance',
            'research_training',
            'chief_accountant',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        $permissions = [
            // Application Management
            'view_all_applications',
            'view_assigned_applications',
            'approve_application',
            'reject_application',
            'request_correction',
            'return_application',
            'forward_application',
            'assign_application',
            'lock_application',
            'unlock_application',
            
            // Payment & Financial
            'confirm_payment',
            'reject_payment',
            'verify_payment_proof',
            'process_refund',
            'approve_waiver',
            'reject_waiver',
            'view_financial_oversight',
            'view_payment_reports',
            'export_ledger',
            
            // Production & Issuance
            'generate_cards',
            'generate_certificates',
            'print_documents',
            'issue_credentials',
            'mark_production_ready',
            
            // User & Role Management
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
            'remove_roles',
            'approve_user_accounts',
            'suspend_users',
            'reset_user_passwords',
            'force_password_reset',
            
            // Content Management
            'view_content',
            'manage_content',
            'view_news',
            'manage_news',
            'manage_notices_events',
            'manage_downloads',
            
            // Audit & Oversight
            'view_audit_trail',
            'view_system_logs',
            'view_analytics',
            'view_reports',
            'download_reports',
            'flag_anomalies',
            'generate_audit_reports',
            
            // IT Administration
            'manage_system_settings',
            'manage_regions',
            'trigger_backup',
            'clear_cache',
            'run_cleanup',
            'manage_security_settings',
            'block_ip_addresses',
            'view_active_sessions',
            'terminate_sessions',
            
            // Complaints & Appeals
            'receive_complaints_appeals',
            'manage_complaints_appeals',
            'forward_complaints',
            
            // Draft Management
            'view_drafts',
            'review_drafts',
            'delete_expired_drafts',
            
            // File Management
            'view_files',
            'download_files',
            'upload_documents',
            'delete_documents',
            
            // Workflow Management
            'configure_workflow',
            'manage_fees',
            'manage_templates',
            'configure_regions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::findByName('super_admin')->syncPermissions(Permission::all());

        Role::findByName('auditor')->syncPermissions([
            'view_audit_trail',
            'view_all_applications',
            'view_analytics',
            'view_financial_oversight',
        ]);

        Role::findByName('director')->syncPermissions([
            'view_all_applications',
            'view_analytics',
            'view_audit_trail',
            'approve_user_accounts',
            'view_content',
            'view_news',
            'receive_complaints_appeals',
            'download_reports',
            'view_financial_oversight',
        ]);

        Role::findByName('it_admin')->syncPermissions([
            'manage_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
            'remove_roles',
            'approve_user_accounts',
            'suspend_users',
            'reset_user_passwords',
            'force_password_reset',
            'view_audit_trail',
            'view_analytics',
            'manage_system_settings',
            'manage_regions',
            'trigger_backup',
            'clear_cache',
            'run_cleanup',
            'manage_security_settings',
            'block_ip_addresses',
            'view_active_sessions',
            'terminate_sessions',
            'view_drafts',
            'review_drafts',
            'view_files',
            'view_all_applications',
            'view_system_logs',
        ]);

        Role::findByName('complaints_officer')->syncPermissions([
            'receive_complaints_appeals',
            'manage_complaints_appeals',
        ]);

        Role::findByName('pr_officer')->syncPermissions([
            'manage_content',
            'manage_news',
            'manage_notices_events',
            'manage_downloads',
            'view_content',
            'view_news',
        ]);

        Role::findByName('public_info_compliance')->syncPermissions([
            'receive_complaints_appeals',
            'manage_complaints_appeals',
            'view_content',
            'view_news',
        ]);

        Role::findByName('research_training')->syncPermissions([
            'view_content',
            'view_news',
            'view_all_applications',
            'view_analytics',
        ]);

        Role::findByName('chief_accountant')->syncPermissions([
            'view_all_applications',
            'view_analytics',
            'view_audit_trail',
            'confirm_payment',
            'download_reports',
            'view_financial_oversight',
        ]);
    }
}
