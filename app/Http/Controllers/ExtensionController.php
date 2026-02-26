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
        $pbxConnections = \App\Models\PbxConnection::where('is_active', true)->with('company')->get();
        $users = \App\Models\User::where('role', 'agent')->get();
        return view('admin.extensions.create', compact('pbxConnections', 'users'));
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
        $pbxConnections = \App\Models\PbxConnection::where('is_active', true)->with('company')->get();
        $users = \App\Models\User::where('role', 'agent')->get();
        return view('admin.extensions.edit', compact('extension', 'pbxConnections', 'users'));
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

    public function activate(Request $request, Extension $extension): JsonResponse
    {
        if ($extension->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $activeCount = Extension::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->where('id', '!=', $extension->id)
            ->count();

        if ($activeCount >= 6) {
            return response()->json([
                'message' => 'Maximum of 6 active extensions per user.',
            ], 422);
        }

        $extension->update(['is_active' => true]);

        return response()->json(['message' => 'Extension activated.', 'extension' => $extension]);
    }

    public function deactivate(Request $request, Extension $extension): JsonResponse
    {
        if ($extension->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $extension->update(['is_active' => false, 'is_registered' => false]);

        return response()->json(['message' => 'Extension deactivated.', 'extension' => $extension]);
    }

    public function sipCredentials(Request $request, Extension $extension): JsonResponse
    {
        if ($extension->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return response()->json(['password' => $extension->password]);
    }
}
