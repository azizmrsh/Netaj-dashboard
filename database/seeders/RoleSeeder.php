<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create basic roles
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        
        // Get all permissions
        $allPermissions = Permission::all();
        
        // Assign all permissions to Super Admin
        $superAdmin->syncPermissions($allPermissions);
        
        // Assign specific permissions to Admin
        $adminPermissions = Permission::whereIn('name', [
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'delete_user',
            'view_any_role',
            'view_role',
            'create_role',
            'update_role',
        ])->get();
        $admin->syncPermissions($adminPermissions);
        
        // Assign specific permissions to Editor
        $editorPermissions = Permission::whereIn('name', [
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'view_any_role',
            'view_role',
        ])->get();
        $editor->syncPermissions($editorPermissions);
        
        // Assign read-only permissions to Viewer
        $viewerPermissions = Permission::whereIn('name', [
            'view_any_user',
            'view_user',
            'view_any_role',
            'view_role',
        ])->get();
        $viewer->syncPermissions($viewerPermissions);
        
        // Assign Super Admin role to first user (admin@example.com)
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('super_admin');
        }
        
        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Created roles:');
        $this->command->info('- Super Admin: All permissions');
        $this->command->info('- Admin: User and role management (without role deletion)');
        $this->command->info('- Editor: View, create and edit users, view roles');
        $this->command->info('- Viewer: View users and roles only');
    }
}