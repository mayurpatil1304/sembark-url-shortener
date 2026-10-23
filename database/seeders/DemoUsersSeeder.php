<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminPassword = env('SUPER_ADMIN_PASSWORD');
        $demoPassword = env('DEMO_USERS_PASSWORD');

        if (blank($superAdminPassword) || blank($demoPassword)) {
            throw new RuntimeException(
                'Set SUPER_ADMIN_PASSWORD and DEMO_USERS_PASSWORD in your .env file before seeding.'
            );
        }

        // Create the demo company if it does not already exist.
        $company = Company::firstOrCreate([
            'name' => 'Sembark Demo Company',
        ]);

        $accounts = [
            [
                'name' => 'Sembark SuperAdmin',
                'email' => env('SUPER_ADMIN_EMAIL', 'superadmin@sembark.local'),
                'role' => User::ROLE_SUPER_ADMIN,
                'company_id' => null,
                'password' => $superAdminPassword,
            ],
            [
                'name' => 'Demo Admin',
                'email' => 'admin@sembark.local',
                'role' => User::ROLE_ADMIN,
                'company_id' => $company->id,
                'password' => $demoPassword,
            ],
            [
                'name' => 'Demo Member',
                'email' => 'member@sembark.local',
                'role' => User::ROLE_MEMBER,
                'company_id' => $company->id,
                'password' => $demoPassword,
            ],
            [
                'name' => 'Demo Sales',
                'email' => 'sales@sembark.local',
                'role' => User::ROLE_SALES,
                'company_id' => $company->id,
                'password' => $demoPassword,
            ],
            [
                'name' => 'Demo Manager',
                'email' => 'manager@sembark.local',
                'role' => User::ROLE_MANAGER,
                'company_id' => $company->id,
                'password' => $demoPassword,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'company_id' => $account['company_id'],
                    'password' => Hash::make($account['password']),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command?->info(
            'Demo login accounts for all five roles have been created or updated.'
        );
    }
}
