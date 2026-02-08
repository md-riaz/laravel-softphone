<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExtensionRequest;
use App\Models\Extension;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExtensionController extends Controller
{
    public function index()
    {
        $extensions = Extension::with('pbxConnection', 'user')->paginate(15);
        return view('admin.extensions.index', compact('extensions'));
    }

    public function create()
    {
        return view('admin.extensions.create');
    }

    public function store(StoreExtensionRequest $request)
    {
        Extension::create($request->validated());
        return redirect()->route('admin.extensions.index')->with('success', 'Extension created.');
    }

    public function show(Extension $extension)
    {
        $extension->load('pbxConnection', 'user', 'calls');
        return view('admin.extensions.show', compact('extension'));
    }

    public function edit(Extension $extension)
    {
        return view('admin.extensions.edit', compact('extension'));
    }

    public function update(StoreExtensionRequest $request, Extension $extension)
    {
        $extension->update($request->validated());
        return redirect()->route('admin.extensions.index')->with('success', 'Extension updated.');
    }

    public function destroy(Extension $extension)
    {
        $extension->delete();
        return redirect()->route('admin.extensions.index')->with('success', 'Extension deleted.');
    }

    public function myExtensions(Request $request): JsonResponse
    {
        $extensions = $request->user()->extensions()->with('pbxConnection')->get();
        return response()->json($extensions);
    }

    public function activate(Extension $extension): JsonResponse
    {
        $user = $extension->user;

        if ($user) {
            $activeCount = Extension::where('user_id', $user->id)
                ->where('is_active', true)
                ->where('id', '!=', $extension->id)
                ->count();

            if ($activeCount >= 6) {
                return response()->json([
                    'message' => 'Maximum of 6 active extensions per user.',
                ], 422);
            }
        }

        $extension->update(['is_active' => true]);

        return response()->json(['message' => 'Extension activated.', 'extension' => $extension]);
    }

    public function deactivate(Extension $extension): JsonResponse
    {
        $extension->update(['is_active' => false, 'is_registered' => false]);

        return response()->json(['message' => 'Extension deactivated.', 'extension' => $extension]);
    }
}
