<?php

namespace Database\Factories;

use App\Models\Extension;
use App\Models\PbxConnection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExtensionFactory extends Factory
{
    protected $model = Extension::class;

    public function definition(): array
    {
        return [
            'pbx_connection_id' => PbxConnection::factory(),
            'user_id' => User::factory(),
            'extension_number' => (string) fake()->unique()->numberBetween(1000, 9999),
            'password' => fake()->password(8, 12),
            'display_name' => fake()->name(),
            'is_active' => false,
            'is_registered' => false,
        ];
    }
}
