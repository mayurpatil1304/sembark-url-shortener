<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;

class DashboardPermissionsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    private function makeCompany(string $name): Company
    {
        return Company::create(['name' => $name]);
    }
    private function makeUser(
        string $role,
        ?int $companyId = null
    ): User {
        return User::factory()->create([
            'company_id' => $companyId,
            'role' => $role,
        ]);
    }

    private function makeShortUrl(
        User $user,
        string $code,
        string $destination
    ): ShortUrl {
        return ShortUrl::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'original_url' => $destination,
            'code' => $code,
        ]);
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_superadmin_can_view_platform_dashboard(): void
    {
        $company = $this->makeCompany('Alpha Ltd');
        $admin = $this->makeUser('Admin', $company->id);

        $this->makeShortUrl(
            $admin,
            'Alpha123',
            'https://example.com/alpha'
        );

        $superAdmin = $this->makeUser('SuperAdmin');

        $this->actingAs($superAdmin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Alpha Ltd')
            ->assertSee('Alpha123');
    }

    public function test_admin_sees_only_own_company_urls(): void
    {
        $companyA = $this->makeCompany('Alpha Ltd');
        $companyB = $this->makeCompany('Beta Ltd');

        $adminA = $this->makeUser('Admin', $companyA->id);
        $adminB = $this->makeUser('Admin', $companyB->id);

        $this->makeShortUrl(
            $adminA,
            'Alpha123',
            'https://example.com/alpha'
        );

        $this->makeShortUrl(
            $adminB,
            'Beta1234',
            'https://example.com/beta'
        );

        $this->actingAs($adminA)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Alpha123')
            ->assertDontSee('Beta1234')
            ->assertSee('Alpha Ltd')
            ->assertDontSee('Beta Ltd');
    }

    public function test_member_sees_only_urls_they_created(): void
    {
        $company = $this->makeCompany('Alpha Ltd');

        $memberA = $this->makeUser('Member', $company->id);
        $memberB = $this->makeUser('Member', $company->id);

        $this->makeShortUrl(
            $memberA,
            'MemberA1',
            'https://example.com/member-a'
        );

        $this->makeShortUrl(
            $memberB,
            'MemberB1',
            'https://example.com/member-b'
        );

        $this->actingAs($memberA)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('MemberA1')
            ->assertDontSee('MemberB1');
    }

    public function test_member_cannot_access_superadmin_url_list(): void
    {
        $company = $this->makeCompany('Alpha Ltd');
        $member = $this->makeUser('Member', $company->id);

        $this->actingAs($member)
            ->get(route('admin.short-urls.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_superadmin_url_list(): void
    {
        $company = $this->makeCompany('Alpha Ltd');
        $admin = $this->makeUser('Admin', $company->id);

        $this->actingAs($admin)
            ->get(route('admin.short-urls.index'))
            ->assertForbidden();
    }

    public function test_public_redirect_increments_click_count(): void
    {
        $company = $this->makeCompany('Alpha Ltd');
        $member = $this->makeUser('Member', $company->id);

        $url = $this->makeShortUrl(
            $member,
            'Pub12345',
            'https://example.com/landing'
        );

        $this->get('/Pub12345')
            ->assertRedirect('https://example.com/landing');

        $this->assertDatabaseHas('short_urls', [
            'id' => $url->id,
            'clicks' => 1,
        ]);
    }
}
