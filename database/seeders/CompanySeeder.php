<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo company
        $company = Company::create([
            'name' => 'Demo Company',
            'email' => 'demo@example.com',
            'phone' => '+1234567890',
            'description' => 'This is a demo company for testing purposes',
            'is_active' => true,
        ]);

        // Set fixed API key for testing
        $company->api_key = 'demo_api_key_for_testing_purposes_only';
        $company->save();

        // Create demo company manager
        User::create([
            'name' => 'Demo Manager',
            'email' => 'manager@demo.com',
            'password' => bcrypt('password'),
            'company_id' => $company->id,
            'is_admin' => false,
        ]);

        $this->command->info('Demo company created with API key: ' . $company->api_key);
        $this->command->info('Demo company manager created: manager@demo.com / password');
    }
}
