<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AgentConsoleController extends Controller
{
    public function index(Request $request)
    {
        return view('agent.console');
    }
}
