<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        // Сначала создаем роли и разрешения
        $this->call([
            RolePermissionSeeder::class,
        ]);

        // Затем создаем компании
        $this->call([
            CompanySeeder::class,
        ]);

        // Затем создаем пользователей
        $this->call([
            UserSeeder::class,
        ]);
    }
}
