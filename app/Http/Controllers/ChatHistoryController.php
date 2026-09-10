<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatConversation;

class ChatHistoryController extends Controller
{
    /**
     * Display a listing of chat enquiries.
     */
    public function index()
    {
        $conversations = ChatConversation::withCount('messages')
            ->orderBy('last_message_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.chat-history.index', compact('conversations'));
    }

    /**
     * Display the specified chat enquiry.
     */
    public function show($id)
    {
        $conversation = ChatConversation::with('messages')
            ->findOrFail($id);

        return view('admin.chat-history.show', compact('conversation'));
    }
}