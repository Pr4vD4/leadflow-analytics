<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return \App\Models\Company
     */
    public function run(): Company
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

        $this->command->info('Demo company created with API key: ' . $company->api_key);

        return $company;
    }
}
