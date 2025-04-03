<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем глобального администратора
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@leadflow.test',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // Назначаем роль администратора
        $admin->assignRole('admin');

        $this->command->info('Admin created: admin@leadflow.test / password');

        // Получаем демо-компанию
        $demoCompany = Company::where('api_key', 'demo_api_key_for_testing_purposes_only')->first();

        // Создаем менеджера для демо-компании
        if ($demoCompany) {
            $manager = User::create([
                'name' => 'Demo Manager',
                'email' => 'manager@demo.com',
                'password' => Hash::make('password'),
                'company_id' => $demoCompany->id,
                'is_admin' => false,
            ]);

            // Назначаем роль менеджера
            $manager->assignRole('manager');

            $this->command->info('Demo company manager created: manager@demo.com / password');

            // Создаем обычных пользователей для демо-компании
            User::factory(2)->forCompany($demoCompany)->create()->each(function($user) {
                // Назначаем роль сотрудника
                $user->assignRole('employee');
            });
            $this->command->info('Created 2 regular users for Demo Company');
        }

        // Создаем дополнительные тестовые компании с пользователями
        Company::factory(3)->create()->each(function ($company) {
            // Для каждой компании создаем менеджера
            $companyManager = User::factory()->forCompany($company)->create([
                'name' => 'Manager ' . $company->name,
                'email' => 'manager_' . $company->id . '@leadflow.test',
                'password' => Hash::make('password'),
            ]);

            // Назначаем роль менеджера
            $companyManager->assignRole('manager');

            $this->command->info("Manager created for {$company->name}: manager_{$company->id}@leadflow.test / password");

            // И несколько обычных пользователей
            User::factory(2)->forCompany($company)->create()->each(function($user) {
                // Назначаем роль сотрудника
                $user->assignRole('employee');
            });
            $this->command->info("Created 2 regular users for {$company->name}");
        });
    }
}
