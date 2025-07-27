<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إعادة تعيين الكاش الخاص بالأدوار والصلاحيات
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // إنشاء كل الصلاحيات اللازمة للنظام بشكل كامل
        $permissions = [
            'view-admin-panel', // <-- الصلاحية المخصصة الجديدة للدخول للوحة التحكم

            // Products
            'view-products', 'create-products', 'edit-products', 'delete-products',
            
            // Categories
            'view-categories', 'create-categories', 'edit-categories', 'delete-categories',

            // Orders
            'view-orders', 'create-orders', 'edit-orders', 'delete-orders',
            'view-trashed-orders', 'restore-orders', 'force-delete-orders',

            // Users & Roles
            'view-users', 'create-users', 'edit-users', 'delete-users', 'ban-users',
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles',

            // Customers
            'view-customers', 'create-customers', 'edit-customers', 'delete-customers', 'ban-customers',

            // Suppliers & Purchases
            'view-suppliers', 'create-suppliers', 'edit-suppliers', 'delete-suppliers',
            'view-purchases', 'create-purchases', 'edit-purchases', 'delete-purchases',

            // Financial & Inventory
            'view-expenses', 'create-expenses', 'edit-expenses', 'delete-expenses',
            'view-inventory',
            'view-reports',
            
            // Discount Codes
            'view-discount-codes', 'create-discount-codes', 'edit-discount-codes', 'delete-discount-codes',
        ];

        // إنشاء الصلاحيات إذا لم تكن موجودة
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // --- دور المدير العام (Super-Admin) ---
        $superAdminRole = Role::firstOrCreate(['name' => 'Super-Admin']);
        // إعطاء كل الصلاحيات للمدير العام
        $superAdminRole->givePermissionTo(Permission::all());

        // --- دور مدير الطلبات (Order-Manager) ---
        $orderManagerRole = Role::firstOrCreate(['name' => 'Order-Manager']);
        $orderManagerPermissions = [
            'view-admin-panel', // <-- إعطاء صلاحية الدخول للوحة التحكم
            'view-orders', 'create-orders', 'edit-orders', 
            'delete-orders', 'view-trashed-orders', 'restore-orders',
            'view-customers', // مدير الطلبات يحتاج لرؤية العملاء
        ];
        $orderManagerRole->syncPermissions($orderManagerPermissions);
        
        // --- دور كاتب المحتوى (Content-Creator) ---
        $contentCreatorRole = Role::firstOrCreate(['name' => 'Content-Creator']);
        $contentCreatorPermissions = [
            'view-admin-panel', // <-- إعطاء صلاحية الدخول للوحة التحكم
            'view-products', 'create-products', 'edit-products',
            'view-categories', 'create-categories', 'edit-categories',
        ];
        $contentCreatorRole->syncPermissions($contentCreatorPermissions);
    }
}
