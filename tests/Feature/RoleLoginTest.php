<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_five_roles_can_log_in_and_open_dashboard(): void
    {
        $company = Company::create([
            'name' => 'Sembark Demo Company',
        ]);

        $roles = [
            User::ROLE_SUPER_ADMIN,
            User::ROLE_ADMIN,
            User::ROLE_MEMBER,
            User::ROLE_SALES,
            User::ROLE_MANAGER,
        ];

        foreach ($roles as $index => $role) {
            $email = strtolower($role) . '@example.test';
            $password = 'TestPassword123!';

            $user = User::factory()->create([
                'name' => $role . ' Test User',
                'email' => $email,
                'password' => Hash::make($password),
                'company_id' => $role === User::ROLE_SUPER_ADMIN
                    ? null
                    : $company->id,
                'role' => $role,
            ]);

            $this->post('/login', [
                'email' => $email,
                'password' => $password,
            ])->assertRedirect();

            $this->assertAuthenticatedAs($user);

            $this->get('/dashboard')->assertOk();

            $this->post('/logout')->assertRedirect();
            $this->assertGuest();
        }
    }

    public function test_sales_and_manager_cannot_open_url_creation_page(): void
    {
        $company = Company::create([
            'name' => 'Sembark Demo Company',
        ]);

        foreach ([User::ROLE_SALES, User::ROLE_MANAGER] as $role) {
            $user = User::factory()->create([
                'company_id' => $company->id,
                'role' => $role,
            ]);

            $this->actingAs($user)
                ->get(route('short-urls.create'))
                ->assertForbidden();

            $this->actingAs($user)
                ->post(route('short-urls.store'), [
                    'original_url' => 'https://example.com',
                ])
                ->assertForbidden();

            auth()->logout();
        }
    }

    public function test_manager_can_view_company_urls_but_not_other_company_urls(): void
    {
        $companyA = Company::create(['name' => 'Alpha Ltd']);
        $companyB = Company::create(['name' => 'Beta Ltd']);

        $manager = User::factory()->create([
            'company_id' => $companyA->id,
            'role' => User::ROLE_MANAGER,
        ]);

        $adminA = User::factory()->create([
            'company_id' => $companyA->id,
            'role' => User::ROLE_ADMIN,
        ]);

        $adminB = User::factory()->create([
            'company_id' => $companyB->id,
            'role' => User::ROLE_ADMIN,
        ]);

        \App\Models\ShortUrl::create([
            'company_id' => $companyA->id,
            'user_id' => $adminA->id,
            'original_url' => 'https://example.com/alpha',
            'code' => 'Alpha123',
        ]);

        \App\Models\ShortUrl::create([
            'company_id' => $companyB->id,
            'user_id' => $adminB->id,
            'original_url' => 'https://example.com/beta',
            'code' => 'Beta1234',
        ]);

        $this->actingAs($manager)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Alpha123')
            ->assertDontSee('Beta1234');
    }
}
