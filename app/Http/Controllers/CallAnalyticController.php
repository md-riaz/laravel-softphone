<?php

namespace App\Http\Controllers;

use App\Models\CallAnalytic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallAnalyticController extends Controller
{
    public function index(Request $request)
    {
        $query = CallAnalytic::with('company');

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->input('date_to'));
        }
        if ($request->filled('company_id')) {
            $query->where('company_id', $request->input('company_id'));
        }

        $analytics = $query->orderByDesc('date')->paginate(30);

        return view('admin.analytics.index', compact('analytics'));
    }

    public function reports(Request $request)
    {
        $query = CallAnalytic::with('company');

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->input('date_to'));
        }

        $analytics = $query->orderByDesc('date')->paginate(30);

        return view('admin.reports.index', compact('analytics'));
    }

    public function summary(Request $request): JsonResponse
    {
        $companyId = $request->user()->company_id;

        $today = CallAnalytic::where('company_id', $companyId)
            ->where('date', now()->toDateString())
            ->first();

        $thisWeek = CallAnalytic::where('company_id', $companyId)
            ->whereBetween('date', [now()->startOfWeek()->toDateString(), now()->toDateString()])
            ->selectRaw('
                SUM(total_calls) as total_calls,
                SUM(inbound_calls) as inbound_calls,
                SUM(outbound_calls) as outbound_calls,
                SUM(answered_calls) as answered_calls,
                SUM(missed_calls) as missed_calls,
                SUM(total_duration) as total_duration,
                SUM(total_talk_time) as total_talk_time,
                AVG(avg_duration) as avg_duration,
                AVG(avg_talk_time) as avg_talk_time
            ')
            ->first();

        $thisMonth = CallAnalytic::where('company_id', $companyId)
            ->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->toDateString()])
            ->selectRaw('
                SUM(total_calls) as total_calls,
                SUM(inbound_calls) as inbound_calls,
                SUM(outbound_calls) as outbound_calls,
                SUM(answered_calls) as answered_calls,
                SUM(missed_calls) as missed_calls,
                SUM(total_duration) as total_duration,
                SUM(total_talk_time) as total_talk_time,
                AVG(avg_duration) as avg_duration,
                AVG(avg_talk_time) as avg_talk_time
            ')
            ->first();

        return response()->json([
            'today' => $today,
            'this_week' => $thisWeek,
            'this_month' => $thisMonth,
        ]);
    }
}
