<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Disposition;
use Illuminate\Database\Eloquent\Factories\Factory;

class DispositionFactory extends Factory
{
    protected $model = Disposition::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->word(),
            'color' => fake()->hexColor(),
            'is_active' => true,
        ];
    }
}
