<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Roles ────────────────────────────────────────────────────────────
        $adminRole  = Role::firstOrCreate(['name' => 'admin',         'guard_name' => 'web']);
        $superRole  = Role::firstOrCreate(['name' => 'supervisor',    'guard_name' => 'web']);
        $staffRole  = Role::firstOrCreate(['name' => 'support_staff', 'guard_name' => 'web']);

        // ─── Permissions ─────────────────────────────────────────────────────
        $permissions = [
            'view activities',
            'create activities',
            'update activities',
            'delete activities',
            'update activity status',
            'view reports',
            'export reports',
            'manage users',
            'view audit logs',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign permissions to roles
        $adminRole->syncPermissions($permissions);
        $superRole->syncPermissions([
            'view activities', 'create activities', 'update activities',
            'update activity status', 'view reports', 'export reports', 'view audit logs',
        ]);
        $staffRole->syncPermissions([
            'view activities', 'create activities', 'update activity status',
        ]);

        // ─── Admin user ───────────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@apptrack.pro'],
            [
                'full_name'   => 'System Administrator',
                'employee_id' => 'EMP-001',
                'department'  => 'IT Operations',
                'password'    => Hash::make('Admin@123'),
                'is_active'   => true,
            ]
        );
        $admin->assignRole($adminRole);

        // ─── Sample supervisor ────────────────────────────────────────────────
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@apptrack.pro'],
            [
                'full_name'   => 'Kwame Mensah',
                'employee_id' => 'EMP-002',
                'department'  => 'Applications Support',
                'password'    => Hash::make('Super@123'),
                'is_active'   => true,
            ]
        );
        $supervisor->assignRole($superRole);

        // ─── Sample support staff ─────────────────────────────────────────────
        $staff1 = User::firstOrCreate(
            ['email' => 'ama.boateng@apptrack.pro'],
            [
                'full_name'   => 'Ama Boateng',
                'employee_id' => 'EMP-003',
                'department'  => 'Applications Support',
                'password'    => Hash::make('Staff@123'),
                'is_active'   => true,
            ]
        );
        $staff1->assignRole($staffRole);

        $staff2 = User::firstOrCreate(
            ['email' => 'kofi.asante@apptrack.pro'],
            [
                'full_name'   => 'Kofi Asante',
                'employee_id' => 'EMP-004',
                'department'  => 'Applications Support',
                'password'    => Hash::make('Staff@123'),
                'is_active'   => true,
            ]
        );
        $staff2->assignRole($staffRole);

        $this->command->info('✅  Roles, permissions and seed users created successfully.');
        $this->command->table(
            ['Role',  'Email',                         'Password'],
            [
                ['Admin',         'admin@apptrack.pro',             'Admin@123'],
                ['Supervisor',    'supervisor@apptrack.pro',        'Super@123'],
                ['Support Staff', 'ama.boateng@apptrack.pro',       'Staff@123'],
                ['Support Staff', 'kofi.asante@apptrack.pro',       'Staff@123'],
            ]
        );
    }
}
