<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\Extension;
use App\Models\PbxConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExtensionTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_extensions(): void
    {
        $admin = User::factory()->admin()->create();
        $pbx = PbxConnection::factory()->create();
        Extension::factory()->count(3)->create(['pbx_connection_id' => $pbx->id]);

        $response = $this->actingAs($admin)->get(route('admin.extensions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.extensions.index');
    }

    public function test_admin_can_create_extension(): void
    {
        $admin = User::factory()->admin()->create();
        $pbx = PbxConnection::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.extensions.store'), [
            'pbx_connection_id' => $pbx->id,
            'extension_number' => '1001',
            'password' => 'secret123',
            'display_name' => 'Test Ext',
        ]);

        $response->assertRedirect(route('admin.extensions.index'));
        $this->assertDatabaseHas('extensions', ['extension_number' => '1001']);
    }

    public function test_admin_can_delete_extension(): void
    {
        $admin = User::factory()->admin()->create();
        $extension = Extension::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.extensions.destroy', $extension));

        $response->assertRedirect(route('admin.extensions.index'));
        $this->assertDatabaseMissing('extensions', ['id' => $extension->id]);
    }

    public function test_agent_cannot_access_admin_extensions(): void
    {
        $agent = User::factory()->agent()->create();

        $response = $this->actingAs($agent)->get(route('admin.extensions.index'));

        $response->assertStatus(403);
    }
}
