<?php

namespace App\Jobs;

use App\Models\Call;
use App\Models\CallAnalytic;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class AggregateCallAnalytics implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $date
    ) {}

    public function handle(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $extensionIds = $company->pbxConnections()
                ->with('extensions')
                ->get()
                ->pluck('extensions')
                ->flatten()
                ->pluck('id');

            $calls = Call::whereIn('extension_id', $extensionIds)
                ->whereDate('started_at', $this->date);

            $totalCalls = $calls->count();
            $inboundCalls = (clone $calls)->where('direction', 'inbound')->count();
            $outboundCalls = (clone $calls)->where('direction', 'outbound')->count();
            $answeredCalls = (clone $calls)->whereIn('status', ['answered', 'ended'])->count();
            $missedCalls = (clone $calls)->where('status', 'missed')->count();
            $totalDuration = (clone $calls)->sum('duration') ?? 0;
            $totalTalkTime = (clone $calls)->sum('talk_time') ?? 0;
            $avgDuration = $totalCalls > 0 ? round($totalDuration / $totalCalls, 2) : 0;
            $avgTalkTime = $answeredCalls > 0 ? round($totalTalkTime / $answeredCalls, 2) : 0;

            CallAnalytic::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'date' => $this->date,
                ],
                [
                    'total_calls' => $totalCalls,
                    'inbound_calls' => $inboundCalls,
                    'outbound_calls' => $outboundCalls,
                    'answered_calls' => $answeredCalls,
                    'missed_calls' => $missedCalls,
                    'total_duration' => $totalDuration,
                    'total_talk_time' => $totalTalkTime,
                    'avg_duration' => $avgDuration,
                    'avg_talk_time' => $avgTalkTime,
                ]
            );
        }
    }
}
