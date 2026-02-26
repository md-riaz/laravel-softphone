<?php

namespace Tests\Feature\InternalApi;

use App\Models\Company;
use App\Models\Extension;
use App\Models\PbxConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExtensionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_own_extensions(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $pbx = PbxConnection::factory()->create(['company_id' => $company->id]);
        Extension::factory()->count(2)->create([
            'pbx_connection_id' => $pbx->id,
            'user_id' => $user->id,
        ]);

        // Create extension belonging to another user (should not appear)
        Extension::factory()->create(['pbx_connection_id' => $pbx->id]);

        $response = $this->actingAs($user)->getJson(route('internal.extensions'));

        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_user_can_activate_extension(): void
    {
        $user = User::factory()->create();
        $extension = Extension::factory()->create([
            'user_id' => $user->id,
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->postJson(route('internal.extensions.activate', $extension));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Extension activated.']);
        $this->assertTrue($extension->fresh()->is_active);
    }

    public function test_user_can_deactivate_extension(): void
    {
        $user = User::factory()->create();
        $extension = Extension::factory()->create([
            'user_id' => $user->id,
            'is_active' => true,
            'is_registered' => true,
        ]);

        $response = $this->actingAs($user)->postJson(route('internal.extensions.deactivate', $extension));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Extension deactivated.']);
        $this->assertFalse($extension->fresh()->is_active);
        $this->assertFalse($extension->fresh()->is_registered);
    }

    public function test_max_6_active_extensions_enforced(): void
    {
        $user = User::factory()->create();
        $pbx = PbxConnection::factory()->create();

        // Create 6 already-active extensions for this user
        Extension::factory()->count(6)->create([
            'pbx_connection_id' => $pbx->id,
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        // Create a 7th inactive extension
        $extension = Extension::factory()->create([
            'pbx_connection_id' => $pbx->id,
            'user_id' => $user->id,
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)->postJson(route('internal.extensions.activate', $extension));

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Maximum of 6 active extensions per user.']);
        $this->assertFalse($extension->fresh()->is_active);
    }

    public function test_user_cannot_activate_another_users_extension(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $extension = Extension::factory()->create([
            'user_id' => $owner->id,
            'is_active' => false,
        ]);

        $response = $this->actingAs($other)->postJson(route('internal.extensions.activate', $extension));

        $response->assertStatus(403);
        $this->assertFalse($extension->fresh()->is_active);
    }

    public function test_user_cannot_deactivate_another_users_extension(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $extension = Extension::factory()->create([
            'user_id' => $owner->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($other)->postJson(route('internal.extensions.deactivate', $extension));

        $response->assertStatus(403);
        $this->assertTrue($extension->fresh()->is_active);
    }

    public function test_user_can_get_sip_credentials_for_own_extension(): void
    {
        $user = User::factory()->create();
        $extension = Extension::factory()->create([
            'user_id' => $user->id,
            'password' => 'secret123',
        ]);

        $response = $this->actingAs($user)->getJson(route('internal.extensions.sip-credentials', $extension));

        $response->assertStatus(200);
        $response->assertJson(['password' => 'secret123']);
    }

    public function test_user_cannot_get_sip_credentials_for_another_users_extension(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $extension = Extension::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other)->getJson(route('internal.extensions.sip-credentials', $extension));

        $response->assertStatus(403);
    }
}
