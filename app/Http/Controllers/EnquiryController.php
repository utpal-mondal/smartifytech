<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatConversation;

class EnquiryController extends Controller
{
    public function index()
    {
        $conversations = ChatConversation::whereNotNull('phone')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.enquiries.index', compact('conversations'));
    }
}