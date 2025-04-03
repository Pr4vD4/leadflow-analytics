<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Сбрасываем кэш ролей и разрешений
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Создаем разрешения
        // Управление пользователями
        Permission::create(['name' => 'manage-users', 'guard_name' => 'web']);
        Permission::create(['name' => 'create-users', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-users', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-users', 'guard_name' => 'web']);

        // Управление компаниями
        Permission::create(['name' => 'manage-companies', 'guard_name' => 'web']);
        Permission::create(['name' => 'create-companies', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-companies', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-companies', 'guard_name' => 'web']);

        // Управление лидами
        Permission::create(['name' => 'manage-leads', 'guard_name' => 'web']);
        Permission::create(['name' => 'create-leads', 'guard_name' => 'web']);
        Permission::create(['name' => 'edit-leads', 'guard_name' => 'web']);
        Permission::create(['name' => 'delete-leads', 'guard_name' => 'web']);

        // Аналитика
        Permission::create(['name' => 'view-analytics', 'guard_name' => 'web']);
        Permission::create(['name' => 'view-company-analytics', 'guard_name' => 'web']);
        Permission::create(['name' => 'view-global-analytics', 'guard_name' => 'web']);

        // Создаем роли
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $employeeRole = Role::create(['name' => 'employee', 'guard_name' => 'web']);

        // Назначаем разрешения для админа (все разрешения)
        $adminRole->givePermissionTo(Permission::all());

        // Назначаем разрешения для менеджера
        $managerRole->givePermissionTo([
            'manage-users', 'create-users', 'edit-users', 'delete-users',
            'manage-leads', 'create-leads', 'edit-leads', 'delete-leads',
            'view-analytics', 'view-company-analytics'
        ]);

        // Назначаем разрешения для сотрудника
        $employeeRole->givePermissionTo([
            'create-leads', 'edit-leads',
            'view-analytics'
        ]);
    }
}
