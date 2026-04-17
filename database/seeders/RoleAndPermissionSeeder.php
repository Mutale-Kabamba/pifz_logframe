<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view_projects',
            'create_projects',
            'edit_projects',
            'delete_projects',
            'view_logframe',
            'edit_logframe',
            'create_tracking_logs',
            'view_tracking_logs',
            'manage_users',
            'manage_google_credentials',
            'view_dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Admin role
        $adminRole = Role::firstOrCreate(['name' => UserRole::Admin->value]);
        $adminRole->syncPermissions($permissions);

        // Project Officer role
        $officerRole = Role::firstOrCreate(['name' => UserRole::ProjectOfficer->value]);
        $officerRole->syncPermissions([
            'view_projects',
            'edit_projects',
            'view_logframe',
            'create_tracking_logs',
            'view_tracking_logs',
            'view_dashboard',
        ]);

        // IT Admin role
        $itAdminRole = Role::firstOrCreate(['name' => UserRole::ITAdmin->value]);
        $itAdminRole->syncPermissions([
            'view_projects',
            'view_logframe',
            'view_tracking_logs',
            'manage_google_credentials',
            'view_dashboard',
        ]);

        // Create default admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@logframe.test'],
            [
                'name' => 'System Admin',
                'password' => bcrypt('password'),
            ],
        );
        $admin->assignRole(UserRole::Admin->value);
    }
}
