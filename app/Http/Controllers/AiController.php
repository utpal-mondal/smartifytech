<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatConversation;
use App\Services\AiServices;

class AiController extends Controller
{
    /**
     * Return an AI generated customer service reply for a chat message
     * and store both sides of the conversation.
     *
     * @param Request $request
     * @param AiServices $ai
     * @return \Illuminate\Http\JsonResponse
     */
    public function chat(Request $request, AiServices $ai)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $conversation = ChatConversation::firstOrCreate(
            ['session_id' => $request->session()->getId()],
            [
                'name' => $validated['name'] ?? null,
                'email' => $validated['email'] ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => substr((string) $request->userAgent(), 0, 255),
            ]
        );

        $conversation->messages()->create([
            'role' => 'user',
            'message' => $validated['message'],
        ]);

        $reply = $ai->reply($validated['message']);

        $conversation->messages()->create([
            'role' => 'agent',
            'message' => $reply,
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json(['message' => $reply]);
    }
}