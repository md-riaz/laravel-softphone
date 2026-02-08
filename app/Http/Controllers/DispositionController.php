<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDispositionRequest;
use App\Models\Disposition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DispositionController extends Controller
{
    public function index()
    {
        $dispositions = Disposition::with('company')->paginate(15);
        return view('admin.dispositions.index', compact('dispositions'));
    }

    public function create()
    {
        return view('admin.dispositions.create');
    }

    public function store(StoreDispositionRequest $request)
    {
        Disposition::create($request->validated());
        return redirect()->route('admin.dispositions.index')->with('success', 'Disposition created.');
    }

    public function show(Disposition $disposition)
    {
        return view('admin.dispositions.show', compact('disposition'));
    }

    public function edit(Disposition $disposition)
    {
        return view('admin.dispositions.edit', compact('disposition'));
    }

    public function update(StoreDispositionRequest $request, Disposition $disposition)
    {
        $disposition->update($request->validated());
        return redirect()->route('admin.dispositions.index')->with('success', 'Disposition updated.');
    }

    public function destroy(Disposition $disposition)
    {
        $disposition->delete();
        return redirect()->route('admin.dispositions.index')->with('success', 'Disposition deleted.');
    }

    public function list(Request $request): JsonResponse
    {
        $user = $request->user();
        $dispositions = Disposition::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->get();

        return response()->json($dispositions);
    }
}
