<?php

namespace Database\Factories;

use App\Models\Call;
use App\Models\CallDisposition;
use App\Models\Disposition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CallDispositionFactory extends Factory
{
    protected $model = CallDisposition::class;

    public function definition(): array
    {
        return [
            'call_id' => Call::factory(),
            'disposition_id' => Disposition::factory(),
            'user_id' => User::factory(),
        ];
    }
}
