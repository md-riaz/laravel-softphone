<?php

namespace Tests\Feature\InternalApi;

use App\Models\Call;
use App\Models\Company;
use App\Models\Disposition;
use App\Models\Extension;
use App\Models\PbxConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CallTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_call(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $pbx = PbxConnection::factory()->create(['company_id' => $company->id]);
        $extension = Extension::factory()->create([
            'pbx_connection_id' => $pbx->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->postJson(route('internal.calls.store'), [
            'uuid' => Str::uuid()->toString(),
            'extension_id' => $extension->id,
            'user_id' => $user->id,
            'direction' => 'outbound',
            'caller_number' => '+15551234567',
            'callee_number' => '+15559876543',
            'started_at' => now()->toDateTimeString(),
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['id', 'uuid', 'direction']);
        $this->assertDatabaseHas('calls', ['uuid' => $response->json('uuid')]);
    }

    public function test_user_can_mark_call_answered(): void
    {
        $user = User::factory()->create();
        $extension = Extension::factory()->create(['user_id' => $user->id]);
        $call = Call::factory()->create([
            'extension_id' => $extension->id,
            'user_id' => $user->id,
            'status' => 'ringing',
        ]);

        $answeredAt = now()->toDateTimeString();

        $response = $this->actingAs($user)->postJson(route('internal.calls.answered', $call), [
            'answered_at' => $answeredAt,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'answered']);
    }

    public function test_user_can_mark_call_ended(): void
    {
        $user = User::factory()->create();
        $extension = Extension::factory()->create(['user_id' => $user->id]);
        $startedAt = now()->subMinutes(5);
        $answeredAt = now()->subMinutes(4);
        $call = Call::factory()->create([
            'extension_id' => $extension->id,
            'user_id' => $user->id,
            'status' => 'answered',
            'started_at' => $startedAt,
            'answered_at' => $answeredAt,
        ]);

        $endedAt = now()->toDateTimeString();

        $response = $this->actingAs($user)->postJson(route('internal.calls.ended', $call), [
            'ended_at' => $endedAt,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ended']);
        $this->assertNotNull($call->fresh()->duration);
        $this->assertNotNull($call->fresh()->talk_time);
    }

    public function test_user_can_submit_wrapup(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        $extension = Extension::factory()->create(['user_id' => $user->id]);
        $call = Call::factory()->create([
            'extension_id' => $extension->id,
            'user_id' => $user->id,
            'status' => 'ended',
        ]);
        $disposition = Disposition::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user)->postJson(route('internal.calls.wrapup', $call), [
            'notes' => 'Customer was satisfied.',
            'disposition_ids' => [$disposition->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('call_notes', [
            'call_id' => $call->id,
            'content' => 'Customer was satisfied.',
        ]);
        $this->assertDatabaseHas('call_dispositions', [
            'call_id' => $call->id,
            'disposition_id' => $disposition->id,
        ]);
    }

    public function test_unauthenticated_cannot_create_call(): void
    {
        $response = $this->postJson(route('internal.calls.store'), [
            'uuid' => Str::uuid()->toString(),
            'direction' => 'outbound',
            'caller_number' => '+15551234567',
            'callee_number' => '+15559876543',
            'started_at' => now()->toDateTimeString(),
        ]);

        $response->assertStatus(401);
    }
}
