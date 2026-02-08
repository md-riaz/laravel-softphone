<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\CallAnalytic;
use App\Models\Company;
use App\Models\Extension;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard($request);
        }

        return $this->agentDashboard($request);
    }

    public function adminDashboard(Request $request)
    {
        $stats = [
            'total_companies' => Company::count(),
            'total_users' => User::count(),
            'total_extensions' => Extension::count(),
            'active_extensions' => Extension::where('is_active', true)->count(),
            'total_calls_today' => Call::whereDate('started_at', now()->toDateString())->count(),
            'recent_analytics' => CallAnalytic::with('company')
                ->orderByDesc('date')
                ->limit(10)
                ->get(),
        ];

        return view('dashboard.admin', compact('stats'));
    }

    public function agentDashboard(Request $request)
    {
        $user = $request->user();

        $stats = [
            'my_extensions' => $user->extensions()->with('pbxConnection')->get(),
            'active_extensions' => $user->extensions()->where('is_active', true)->count(),
            'total_calls_today' => Call::where('user_id', $user->id)
                ->whereDate('started_at', now()->toDateString())
                ->count(),
            'recent_calls' => Call::where('user_id', $user->id)
                ->with('extension', 'dispositions')
                ->orderByDesc('started_at')
                ->limit(10)
                ->get(),
        ];

        return view('dashboard.agent', compact('stats'));
    }
}
