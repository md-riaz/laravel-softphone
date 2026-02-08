<?php

namespace App\Console\Commands;

use App\Models\Call;
use App\Models\Company;
use Illuminate\Console\Command;

class ExportCallsCommand extends Command
{
    protected $signature = 'calls:export
        {--company= : The company ID}
        {--from= : Start date (Y-m-d)}
        {--to= : End date (Y-m-d)}
        {--output=calls_export.csv : Output file path}';

    protected $description = 'Export calls to CSV for a given date range and company';

    public function handle(): int
    {
        $companyId = $this->option('company');
        $from = $this->option('from') ?? now()->subMonth()->toDateString();
        $to = $this->option('to') ?? now()->toDateString();
        $output = $this->option('output');

        if (!$companyId) {
            $this->error('Please provide a company ID with --company.');
            return self::FAILURE;
        }

        $company = Company::find($companyId);
        if (!$company) {
            $this->error("Company with ID {$companyId} not found.");
            return self::FAILURE;
        }

        $extensionIds = $company->pbxConnections()
            ->with('extensions')
            ->get()
            ->pluck('extensions')
            ->flatten()
            ->pluck('id');

        $calls = Call::whereIn('extension_id', $extensionIds)
            ->whereBetween('started_at', [$from, $to . ' 23:59:59'])
            ->with('extension', 'user')
            ->orderBy('started_at')
            ->get();

        $fp = fopen($output, 'w');

        fputcsv($fp, [
            'UUID', 'Direction', 'Caller', 'Callee', 'Status',
            'Extension', 'User', 'Started At', 'Answered At',
            'Ended At', 'Duration (s)', 'Talk Time (s)',
        ]);

        foreach ($calls as $call) {
            fputcsv($fp, [
                $call->uuid,
                $call->direction,
                $call->caller_number,
                $call->callee_number,
                $call->status,
                $call->extension->extension_number ?? '',
                $call->user->name ?? '',
                $call->started_at?->toDateTimeString(),
                $call->answered_at?->toDateTimeString(),
                $call->ended_at?->toDateTimeString(),
                $call->duration,
                $call->talk_time,
            ]);
        }

        fclose($fp);

        $this->info("Exported {$calls->count()} calls to {$output}");

        return self::SUCCESS;
    }
}
