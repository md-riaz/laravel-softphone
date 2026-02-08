<?php

namespace Database\Factories;

use App\Models\CallAnalytic;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CallAnalyticFactory extends Factory
{
    protected $model = CallAnalytic::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'date' => fake()->date(),
            'total_calls' => fake()->numberBetween(0, 500),
            'inbound_calls' => fake()->numberBetween(0, 250),
            'outbound_calls' => fake()->numberBetween(0, 250),
            'answered_calls' => fake()->numberBetween(0, 400),
            'missed_calls' => fake()->numberBetween(0, 100),
            'total_duration' => fake()->numberBetween(0, 50000),
            'total_talk_time' => fake()->numberBetween(0, 40000),
            'avg_duration' => fake()->randomFloat(2, 0, 300),
            'avg_talk_time' => fake()->randomFloat(2, 0, 250),
        ];
    }
}
