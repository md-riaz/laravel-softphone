<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_companies(): void
    {
        $admin = User::factory()->admin()->create();
        Company::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.companies.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.companies.index');
    }

    public function test_admin_can_create_company(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.companies.store'), [
            'name' => 'Test Company',
            'domain' => 'test.com',
        ]);

        $response->assertRedirect(route('admin.companies.index'));
        $this->assertDatabaseHas('companies', ['name' => 'Test Company']);
    }

    public function test_admin_can_update_company(): void
    {
        $admin = User::factory()->admin()->create();
        $company = Company::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->put(route('admin.companies.update', $company), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('admin.companies.index'));
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'New Name']);
    }

    public function test_admin_can_delete_company(): void
    {
        $admin = User::factory()->admin()->create();
        $company = Company::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.companies.destroy', $company));

        $response->assertRedirect(route('admin.companies.index'));
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
    }

    public function test_agent_cannot_access_admin_companies(): void
    {
        $agent = User::factory()->agent()->create();

        $response = $this->actingAs($agent)->get(route('admin.companies.index'));

        $response->assertStatus(403);
    }
}
