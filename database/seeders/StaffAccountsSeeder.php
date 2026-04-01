<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class StaffAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $roles = [
            'admin',
            'commission_secretary',
            'registrar',
            'accreditation_officer',
            'accounts_officer',
            'production_officer',
            'quality_assurance',
            'it_support',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Staff accounts data
        $staffAccounts = [
            // System Administrators
            [
                'username' => 'admin',
                'email' => 'admin@zmc.co.zw',
                'password' => 'ZMC@Admin2024!',
                'full_name' => 'System Administrator',
                'role' => 'admin',
                'phone' => '+263 242 752 860',
                'department' => 'IT',
            ],
            [
                'username' => 'sysadmin',
                'email' => 'sysadmin@zmc.co.zw',
                'password' => 'ZMC@Sys2024!',
                'full_name' => 'Backup System Administrator',
                'role' => 'admin',
                'phone' => '+263 242 752 861',
                'department' => 'IT',
            ],

            // Management
            [
                'username' => 'commissioner',
                'email' => 'commissioner@zmc.co.zw',
                'password' => 'ZMC@Comm2024!',
                'full_name' => 'Commission Secretary',
                'role' => 'commission_secretary',
                'phone' => '+263 242 752 862',
                'department' => 'Management',
            ],
            [
                'username' => 'deputy',
                'email' => 'deputy@zmc.co.zw',
                'password' => 'ZMC@Deputy2024!',
                'full_name' => 'Deputy Commission Secretary',
                'role' => 'commission_secretary',
                'phone' => '+263 242 752 863',
                'department' => 'Management',
            ],

            // Registrar
            [
                'username' => 'registrar',
                'email' => 'registrar@zmc.co.zw',
                'password' => 'ZMC@Reg2024!',
                'full_name' => 'Senior Registrar',
                'role' => 'registrar',
                'phone' => '+263 242 752 864',
                'department' => 'Registration',
            ],
            [
                'username' => 'asst_registrar',
                'email' => 'asst.registrar@zmc.co.zw',
                'password' => 'ZMC@AsstReg2024!',
                'full_name' => 'Assistant Registrar',
                'role' => 'registrar',
                'phone' => '+263 242 752 865',
                'department' => 'Registration',
            ],

            // Accreditation Officers
            [
                'username' => 'accreditation_officer',
                'email' => 'accreditation@zmc.co.zw',
                'password' => 'ZMC@Acc2024!',
                'full_name' => 'Senior Accreditation Officer',
                'role' => 'accreditation_officer',
                'phone' => '+263 242 752 866',
                'department' => 'Accreditation',
            ],
            [
                'username' => 'accred_officer1',
                'email' => 'accred.officer1@zmc.co.zw',
                'password' => 'ZMC@Acc12024!',
                'full_name' => 'Accreditation Officer 1',
                'role' => 'accreditation_officer',
                'phone' => '+263 242 752 867',
                'department' => 'Accreditation',
            ],
            [
                'username' => 'accred_officer2',
                'email' => 'accred.officer2@zmc.co.zw',
                'password' => 'ZMC@Acc22024!',
                'full_name' => 'Accreditation Officer 2',
                'role' => 'accreditation_officer',
                'phone' => '+263 242 752 868',
                'department' => 'Accreditation',
            ],
            [
                'username' => 'accred_officer3',
                'email' => 'accred.officer3@zmc.co.zw',
                'password' => 'ZMC@Acc32024!',
                'full_name' => 'Accreditation Officer 3',
                'role' => 'accreditation_officer',
                'phone' => '+263 242 752 869',
                'department' => 'Accreditation',
            ],

            // Accounts Officers
            [
                'username' => 'accounts_officer',
                'email' => 'accounts@zmc.co.zw',
                'password' => 'ZMC@Accnt2024!',
                'full_name' => 'Senior Accounts Officer',
                'role' => 'accounts_officer',
                'phone' => '+263 242 752 870',
                'department' => 'Accounts',
            ],
            [
                'username' => 'accounts_officer1',
                'email' => 'accounts.officer1@zmc.co.zw',
                'password' => 'ZMC@Accnt12024!',
                'full_name' => 'Accounts Officer 1',
                'role' => 'accounts_officer',
                'phone' => '+263 242 752 871',
                'department' => 'Accounts',
            ],
            [
                'username' => 'accounts_officer2',
                'email' => 'accounts.officer2@zmc.co.zw',
                'password' => 'ZMC@Accnt22024!',
                'full_name' => 'Accounts Officer 2',
                'role' => 'accounts_officer',
                'phone' => '+263 242 752 872',
                'department' => 'Accounts',
            ],

            // Production Officers
            [
                'username' => 'production_officer',
                'email' => 'production@zmc.co.zw',
                'password' => 'ZMC@Prod2024!',
                'full_name' => 'Senior Production Officer',
                'role' => 'production_officer',
                'phone' => '+263 242 752 873',
                'department' => 'Production',
            ],
            [
                'username' => 'production_officer1',
                'email' => 'production.officer1@zmc.co.zw',
                'password' => 'ZMC@Prod12024!',
                'full_name' => 'Production Officer 1',
                'role' => 'production_officer',
                'phone' => '+263 242 752 874',
                'department' => 'Production',
            ],
            [
                'username' => 'production_officer2',
                'email' => 'production.officer2@zmc.co.zw',
                'password' => 'ZMC@Prod22024!',
                'full_name' => 'Production Officer 2',
                'role' => 'production_officer',
                'phone' => '+263 242 752 875',
                'department' => 'Production',
            ],

            // Quality Assurance
            [
                'username' => 'qa_officer',
                'email' => 'qa@zmc.co.zw',
                'password' => 'ZMC@QA2024!',
                'full_name' => 'Senior QA Officer',
                'role' => 'quality_assurance',
                'phone' => '+263 242 752 876',
                'department' => 'Quality Assurance',
            ],
            [
                'username' => 'qa_officer1',
                'email' => 'qa.officer1@zmc.co.zw',
                'password' => 'ZMC@QA12024!',
                'full_name' => 'QA Officer 1',
                'role' => 'quality_assurance',
                'phone' => '+263 242 752 877',
                'department' => 'Quality Assurance',
            ],

            // IT Support
            [
                'username' => 'it_support',
                'email' => 'it.support@zmc.co.zw',
                'password' => 'ZMC@IT2024!',
                'full_name' => 'Senior IT Support',
                'role' => 'it_support',
                'phone' => '+263 242 752 878',
                'department' => 'IT',
            ],
            [
                'username' => 'it_support1',
                'email' => 'it.support1@zmc.co.zw',
                'password' => 'ZMC@IT12024!',
                'full_name' => 'IT Support Officer',
                'role' => 'it_support',
                'phone' => '+263 242 752 879',
                'department' => 'IT',
            ],
        ];

        // Create staff accounts
        foreach ($staffAccounts as $account) {
            // Check if user already exists
            $existingUser = User::where('email', $account['email'])->first();
            
            if (!$existingUser) {
                $user = User::create([
                    'username' => $account['username'],
                    'email' => $account['email'],
                    'password' => Hash::make($account['password']),
                    'name' => $account['full_name'],
                    'phone' => $account['phone'],
                    'department' => $account['department'],
                    'email_verified_at' => now(),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Assign role
                $role = Role::where('name', $account['role'])->first();
                if ($role) {
                    $user->assignRole($role);
                }

                $this->command->info("Created staff account: {$account['username']} ({$account['role']})");
            } else {
                $this->command->info("Staff account already exists: {$account['username']}");
            }
        }

        $this->command->info('Staff accounts seeding completed successfully!');
    }
}
