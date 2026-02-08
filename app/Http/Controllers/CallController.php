<?php

namespace App\Http\Controllers;

use App\Http\Requests\CallAnsweredRequest;
use App\Http\Requests\CallEndedRequest;
use App\Http\Requests\CallWrapupRequest;
use App\Http\Requests\StoreCallRequest;
use App\Models\Call;
use App\Models\CallDisposition;
use App\Models\CallNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Call::with('extension', 'user', 'dispositions');

        if ($request->filled('date_from')) {
            $query->where('started_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->where('started_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('direction')) {
            $query->where('direction', $request->input('direction'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('extension_id')) {
            $query->where('extension_id', $request->input('extension_id'));
        }

        $calls = $query->orderByDesc('started_at')->paginate(25);

        return response()->json($calls);
    }

    public function store(StoreCallRequest $request): JsonResponse
    {
        $call = Call::create($request->validated());

        return response()->json($call, 201);
    }

    public function answered(CallAnsweredRequest $request, Call $call): JsonResponse
    {
        $call->update([
            'status' => 'answered',
            'answered_at' => $request->validated('answered_at'),
        ]);

        return response()->json($call);
    }

    public function ended(CallEndedRequest $request, Call $call): JsonResponse
    {
        $endedAt = $request->validated('ended_at');

        $duration = $call->started_at
            ? (int) $call->started_at->diffInSeconds($endedAt)
            : null;

        $talkTime = $call->answered_at
            ? (int) $call->answered_at->diffInSeconds($endedAt)
            : null;

        $status = $call->answered_at ? 'ended' : 'missed';

        $call->update([
            'status' => $status,
            'ended_at' => $endedAt,
            'duration' => $duration,
            'talk_time' => $talkTime,
        ]);

        return response()->json($call);
    }

    public function wrapup(CallWrapupRequest $request, Call $call): JsonResponse
    {
        $validated = $request->validated();

        if (!empty($validated['notes'])) {
            CallNote::create([
                'call_id' => $call->id,
                'user_id' => $request->user()->id,
                'content' => $validated['notes'],
            ]);
        }

        if (!empty($validated['disposition_ids'])) {
            foreach ($validated['disposition_ids'] as $dispositionId) {
                CallDisposition::create([
                    'call_id' => $call->id,
                    'disposition_id' => $dispositionId,
                    'user_id' => $request->user()->id,
                ]);
            }
        }

        return response()->json($call->load('notes', 'dispositions'));
    }

    public function history(Request $request)
    {
        $calls = Call::where('user_id', $request->user()->id)
            ->with('extension', 'dispositions', 'notes')
            ->orderByDesc('started_at')
            ->paginate(25);

        return view('agent.call-history', compact('calls'));
    }
}
