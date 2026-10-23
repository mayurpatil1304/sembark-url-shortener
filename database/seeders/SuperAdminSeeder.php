<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@sembark.local');
        $password = env('SUPER_ADMIN_PASSWORD');

        if (empty($password)) {
            throw new \RuntimeException(
                'Set SUPER_ADMIN_PASSWORD in .env before seeding.'
            );
        }

        $existing = DB::table('users')
            ->where('email', $email)
            ->exists();

        if ($existing) {
            $user = DB::table('users')
                ->where('email', $email)
                ->first();

            if ($user->role !== 'SuperAdmin') {
                throw new \RuntimeException(
                    'The configured SuperAdmin email belongs to another role.'
                );
            }

            // Do not reset an existing administrator's password.
            return;
        }

        $now = now();

        // Password hashing is done in PHP; insertion uses raw SQL.
        DB::insert(
            'INSERT INTO users
                (name, email, email_verified_at, password, company_id,
                 role, remember_token, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [
                'Super Admin',
                $email,
                $now,
                Hash::make($password),
                null,
                'SuperAdmin',
                null,
                $now,
                $now,
            ]
        );
    }
}
