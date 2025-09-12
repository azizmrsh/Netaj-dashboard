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
        // إنشاء الأدوار الأساسية
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        
        // الحصول على جميع الصلاحيات
        $allPermissions = Permission::all();
        
        // تعيين جميع الصلاحيات لـ Super Admin
        $superAdmin->syncPermissions($allPermissions);
        
        // تعيين صلاحيات محددة للمدير
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
        
        // تعيين صلاحيات محددة للمحرر
        $editorPermissions = Permission::whereIn('name', [
            'view_any_user',
            'view_user',
            'create_user',
            'update_user',
            'view_any_role',
            'view_role',
        ])->get();
        $editor->syncPermissions($editorPermissions);
        
        // تعيين صلاحيات القراءة فقط للمشاهد
        $viewerPermissions = Permission::whereIn('name', [
            'view_any_user',
            'view_user',
            'view_any_role',
            'view_role',
        ])->get();
        $viewer->syncPermissions($viewerPermissions);
        
        // تعيين دور Super Admin للمستخدم الأول (admin@example.com)
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('super_admin');
        }
        
        $this->command->info('تم إنشاء الأدوار والصلاحيات بنجاح!');
        $this->command->info('الأدوار المُنشأة:');
        $this->command->info('- Super Admin: جميع الصلاحيات');
        $this->command->info('- Admin: إدارة المستخدمين والأدوار (بدون حذف الأدوار)');
        $this->command->info('- Editor: عرض وإنشاء وتعديل المستخدمين، عرض الأدوار');
        $this->command->info('- Viewer: عرض المستخدمين والأدوار فقط');
    }
}