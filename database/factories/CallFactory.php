<?php

namespace Database\Factories;

use App\Models\Call;
use App\Models\Extension;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CallFactory extends Factory
{
    protected $model = Call::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid()->toString(),
            'extension_id' => Extension::factory(),
            'user_id' => User::factory(),
            'direction' => fake()->randomElement(['inbound', 'outbound']),
            'caller_number' => fake()->e164PhoneNumber(),
            'callee_number' => fake()->e164PhoneNumber(),
            'status' => 'ringing',
            'started_at' => now(),
            'answered_at' => null,
            'ended_at' => null,
            'duration' => null,
            'talk_time' => null,
        ];
    }
}
