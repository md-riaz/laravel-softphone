<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePbxConnectionRequest;
use App\Models\PbxConnection;
use Illuminate\Http\Request;

class PbxConnectionController extends Controller
{
    public function index()
    {
        $connections = PbxConnection::with('company')->withCount('extensions')->paginate(15);
        return view('admin.pbx-connections.index', compact('connections'));
    }

    public function create()
    {
        $companies = \App\Models\Company::where('is_active', true)->get();
        return view('admin.pbx-connections.create', compact('companies'));
    }

    public function store(StorePbxConnectionRequest $request)
    {
        PbxConnection::create($request->validated());
        return redirect()->route('admin.pbx-connections.index')->with('success', 'PBX connection created.');
    }

    public function show(PbxConnection $pbxConnection)
    {
        $pbxConnection->load('extensions', 'company');
        return view('admin.pbx-connections.show', compact('pbxConnection'));
    }

    public function edit(PbxConnection $pbxConnection)
    {
        $companies = \App\Models\Company::where('is_active', true)->get();
        return view('admin.pbx-connections.edit', compact('pbxConnection', 'companies'));
    }

    public function update(StorePbxConnectionRequest $request, PbxConnection $pbxConnection)
    {
        $pbxConnection->update($request->validated());
        return redirect()->route('admin.pbx-connections.index')->with('success', 'PBX connection updated.');
    }

    public function destroy(PbxConnection $pbxConnection)
    {
        $pbxConnection->delete();
        return redirect()->route('admin.pbx-connections.index')->with('success', 'PBX connection deleted.');
    }
}
