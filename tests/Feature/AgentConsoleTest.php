<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgentConsoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_agent_can_access_console(): void
    {
        $company = Company::factory()->create();
        $agent = User::factory()->agent()->create(['company_id' => $company->id]);

        $response = $this->actingAs($agent)->get(route('agent.console'));

        $response->assertStatus(200);
        $response->assertViewIs('agent.console');
    }

    public function test_agent_can_access_call_history(): void
    {
        $agent = User::factory()->agent()->create();

        $response = $this->actingAs($agent)->get(route('agent.call-history'));

        $response->assertStatus(200);
        $response->assertViewIs('agent.call-history');
    }

    public function test_admin_can_access_console(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->create(['company_id' => $company->id]);

        $response = $this->actingAs($admin)->get(route('agent.console'));

        $response->assertStatus(200);
        $response->assertViewIs('agent.console');
    }

    public function test_unauthenticated_cannot_access_console(): void
    {
        $response = $this->get(route('agent.console'));

        $response->assertRedirect(route('login'));
    }
}
