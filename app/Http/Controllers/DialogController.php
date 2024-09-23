<?php

namespace App\Http\Controllers;

use App\Models\Dialog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DialogController extends Controller
{
    public function index()
    {
        return Dialog::query()->with(['user1', 'user2'])->get();
//        return response()->json(Dialog::all());
    }

    public function show(Dialog $dialog): JsonResponse
    {
        return response()->json($dialog);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateRequest($request);

        $validated['user_1'] = Auth::User()->id;
        $validated['user_2'] = $request->user_id;

        $dialog = Dialog::query()->create($validated);

        return response()->json($dialog, 201);
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'chat_id' => ['required', 'exists:chats,id'],
            'user_id' => ['required', 'exists:users,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
