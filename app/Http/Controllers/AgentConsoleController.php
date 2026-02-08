<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgentConsoleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $extensions = $user->extensions()->with('pbxConnection')->get();
        $dispositions = \App\Models\Disposition::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->get();

        return view('agent.console', compact('extensions', 'dispositions'));
    }
}
