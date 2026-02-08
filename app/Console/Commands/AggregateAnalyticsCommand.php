<?php

namespace App\Console\Commands;

use App\Jobs\AggregateCallAnalytics;
use Illuminate\Console\Command;

class AggregateAnalyticsCommand extends Command
{
    protected $signature = 'analytics:aggregate {date? : The date to aggregate (Y-m-d). Defaults to yesterday.}';

    protected $description = 'Aggregate call analytics for a given date';

    public function handle(): int
    {
        $date = $this->argument('date') ?? now()->subDay()->toDateString();

        $this->info("Aggregating analytics for {$date}...");

        AggregateCallAnalytics::dispatch($date);

        $this->info('Job dispatched successfully.');

        return self::SUCCESS;
    }
}
