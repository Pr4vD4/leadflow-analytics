<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Создаем глобального администратора
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@leadflow.test',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        // Вызываем CompanySeeder
        $this->call([
            CompanySeeder::class,
        ]);

        // Создаем дополнительные тестовые компании и пользователей
        Company::factory(3)->create()->each(function ($company) {
            // Для каждой компании создаем менеджера
            User::factory()->forCompany($company)->create([
                'name' => 'Manager ' . $company->name,
                'email' => 'manager_' . $company->id . '@leadflow.test',
                'password' => bcrypt('password'),
            ]);

            // И несколько обычных пользователей
            User::factory(2)->forCompany($company)->create();
        });
    }
}
