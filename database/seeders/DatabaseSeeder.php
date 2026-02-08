<?php

namespace Database\Seeders;

use App\Models\Call;
use App\Models\CallAnalytic;
use App\Models\CallDisposition;
use App\Models\CallNote;
use App\Models\Company;
use App\Models\Disposition;
use App\Models\Extension;
use App\Models\PbxConnection;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create company
        $company = Company::create([
            'name' => 'Demo Company',
            'domain' => 'demo.example.com',
        ]);

        // 2. Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'company_id' => $company->id,
        ]);

        // 3. Create agent user
        $agent = User::create([
            'name' => 'Agent User',
            'email' => 'agent@example.com',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'company_id' => $company->id,
        ]);

        // 4. Create PBX connection
        $pbx = PbxConnection::create([
            'company_id' => $company->id,
            'name' => 'Main PBX',
            'host' => 'pbx.example.com',
            'port' => 5060,
            'wss_url' => 'wss://pbx.example.com:7443',
        ]);

        // 5. Create 3 extensions, assign 2 to the agent
        $ext1 = Extension::create([
            'pbx_connection_id' => $pbx->id,
            'user_id' => $agent->id,
            'extension_number' => '1001',
            'password' => 'ext1001pass',
            'display_name' => 'Agent Ext 1',
            'is_active' => true,
        ]);

        $ext2 = Extension::create([
            'pbx_connection_id' => $pbx->id,
            'user_id' => $agent->id,
            'extension_number' => '1002',
            'password' => 'ext1002pass',
            'display_name' => 'Agent Ext 2',
            'is_active' => true,
        ]);

        $ext3 = Extension::create([
            'pbx_connection_id' => $pbx->id,
            'user_id' => null,
            'extension_number' => '1003',
            'password' => 'ext1003pass',
            'display_name' => 'Unassigned Ext',
            'is_active' => false,
        ]);

        // 6. Create dispositions
        $dispositionData = [
            ['name' => 'Sale', 'color' => '#28a745'],
            ['name' => 'No Answer', 'color' => '#dc3545'],
            ['name' => 'Callback', 'color' => '#ffc107'],
            ['name' => 'Wrong Number', 'color' => '#6c757d'],
            ['name' => 'Not Interested', 'color' => '#17a2b8'],
        ];

        $dispositions = [];
        foreach ($dispositionData as $d) {
            $dispositions[] = Disposition::create([
                'company_id' => $company->id,
                'name' => $d['name'],
                'color' => $d['color'],
            ]);
        }

        // 7. Create sample calls with notes and dispositions
        $statuses = ['ended', 'missed', 'ended', 'ended', 'missed'];
        foreach ($statuses as $i => $status) {
            $startedAt = now()->subDays(rand(0, 6))->subHours(rand(1, 8));
            $answeredAt = $status === 'ended' ? $startedAt->copy()->addSeconds(rand(5, 15)) : null;
            $endedAt = $status !== 'ringing' ? ($answeredAt ? $answeredAt->copy()->addSeconds(rand(30, 300)) : $startedAt->copy()->addSeconds(rand(15, 30))) : null;

            $call = Call::create([
                'uuid' => Str::uuid()->toString(),
                'extension_id' => $i % 2 === 0 ? $ext1->id : $ext2->id,
                'user_id' => $agent->id,
                'direction' => $i % 2 === 0 ? 'outbound' : 'inbound',
                'caller_number' => '555010' . $i,
                'callee_number' => '555020' . $i,
                'status' => $status,
                'started_at' => $startedAt,
                'answered_at' => $answeredAt,
                'ended_at' => $endedAt,
                'duration' => $endedAt ? (int) $startedAt->diffInSeconds($endedAt) : null,
                'talk_time' => $answeredAt && $endedAt ? (int) $answeredAt->diffInSeconds($endedAt) : null,
            ]);

            // Add a note
            CallNote::create([
                'call_id' => $call->id,
                'user_id' => $agent->id,
                'content' => 'Sample note for call #' . ($i + 1),
            ]);

            // Add a disposition
            CallDisposition::create([
                'call_id' => $call->id,
                'disposition_id' => $dispositions[$i]->id,
                'user_id' => $agent->id,
            ]);
        }

        // 8. Create call_analytics records
        for ($d = 6; $d >= 0; $d--) {
            CallAnalytic::create([
                'company_id' => $company->id,
                'date' => now()->subDays($d)->toDateString(),
                'total_calls' => rand(20, 80),
                'inbound_calls' => rand(10, 40),
                'outbound_calls' => rand(10, 40),
                'answered_calls' => rand(15, 60),
                'missed_calls' => rand(2, 15),
                'total_duration' => rand(3600, 14400),
                'total_talk_time' => rand(1800, 10800),
                'avg_duration' => rand(60, 300) / 1.0,
                'avg_talk_time' => rand(30, 200) / 1.0,
            ]);
        }
    }
}
