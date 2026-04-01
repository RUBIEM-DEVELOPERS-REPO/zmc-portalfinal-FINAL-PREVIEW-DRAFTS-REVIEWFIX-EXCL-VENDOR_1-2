<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // IMPORTANT: guard_name must match your auth guard (usually 'web')
        $role = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $user = User::updateOrCreate(
            ['email' => 'mupikenipatience@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('ZMC@Admin2026!'),
            ]
        );

        if (! $user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
        }
    }
}
