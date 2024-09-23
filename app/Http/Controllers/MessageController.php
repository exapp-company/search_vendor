<?php

namespace App\Http\Controllers;

use App\Models\Dialog;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{

    public function index(Dialog $dialog): JsonResponse
    {
        $messages = $dialog->messages()
            ->with('sender', 'receiver')
            ->get();
        return response()->json($messages);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validateRequest($request);
        $validated['type'] = $request->hasFile('file') ? 'file' : 'text';

        if ($request->hasFile('content')) {
            $file = $request->file('file');
            $validated['content'] = $file->storeAs('uploads', $file->getClientOriginalName(), 'public');
        }

        $dialog = Dialog::query()->where('chat_id', $validated['chat_id'])->firstOrFail();
        $message = $dialog->messages()->create(array_merge($validated, ['is_sent' => true]));
        return response()->json($message, 201);
    }


    public function update(Request $request, Message $message): JsonResponse
    {
        $validated = $this->validateRequest($request);
        $message->update($validated);
        return response()->json($message);
    }

    public function read(Message $message)
    {
        $message->is_read = true;
        return response()->noContent();
    }

    public function destroy(Message $message)
    {
        $message->delete();
        return response()->noContent();
    }

    protected function validateRequest(Request $request): array
    {
        return $request->validate([
            'sender_id' => ['required', 'exists:users,id'],
            'receiver_id' => ['required', 'exists:users,id'],
            'chat_id' => ['required', 'exists:chats,id'],
            'content' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->hasFile($attribute)) {
                        if (!$request->file($attribute)->isValid()) {
                            return $fail('The file is not valid.');
                        }
                    } elseif (!is_string($value)) {
                        return $fail('The content must be either a valid string or a file.');
                    }
                },
            ]
        ]);
    }

}
