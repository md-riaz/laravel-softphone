<?php

namespace Database\Factories;

use App\Models\Call;
use App\Models\CallNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CallNoteFactory extends Factory
{
    protected $model = CallNote::class;

    public function definition(): array
    {
        return [
            'call_id' => Call::factory(),
            'user_id' => User::factory(),
            'content' => fake()->paragraph(),
        ];
    }
}
