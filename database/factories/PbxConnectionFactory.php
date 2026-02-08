<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\PbxConnection;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbxConnectionFactory extends Factory
{
    protected $model = PbxConnection::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->words(2, true) . ' PBX',
            'host' => fake()->ipv4(),
            'port' => 5060,
            'wss_url' => 'wss://' . fake()->domainName() . ':8089/ws',
            'stun_server' => 'stun:stun.l.google.com:19302',
            'turn_server' => null,
            'turn_username' => null,
            'turn_password' => null,
            'is_active' => true,
        ];
    }
}
