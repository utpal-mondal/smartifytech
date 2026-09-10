@extends('layouts.auth')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-gray-100">

    @include('layouts.navbar')

    <div class="flex-1 flex justify-center items-start py-10">
        <div class="max-w-4xl w-full space-y-8 px-4">

            <div class="flex items-center justify-between bg-white rounded-xl shadow p-6">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-800">Enquiry Details</h2>
                    <p class="text-sm text-gray-500 mt-1">Conversation with {{ $conversation->name ?? 'Guest' }}</p>
                </div>
                <a href="{{ route('admin.chat-history') }}"
                   class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-lg transition">
                    Back to list
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Visitor Information</h3>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Name:</span>
                        <p class="font-medium text-gray-800">{{ $conversation->name ?? 'Guest' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Email:</span>
                        <p class="font-medium text-gray-800">{{ $conversation->email ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">IP Address:</span>
                        <p class="font-medium text-gray-800">{{ $conversation->ip_address ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Started:</span>
                        <p class="font-medium text-gray-800">{{ $conversation->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Messages</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    @forelse($conversation->messages as $message)
                        <div class="flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] rounded-2xl px-5 py-3 text-sm
                                        {{ $message->role === 'user' ? 'bg-blue-600 text-white rounded-br-none' : 'bg-gray-100 text-gray-800 rounded-bl-none' }}">
                                <p class="whitespace-pre-wrap">{{ $message->message }}</p>
                                <span class="block text-xs mt-1 {{ $message->role === 'user' ? 'text-blue-200' : 'text-gray-500' }}">
                                    {{ $message->created_at->format('H:i, M d') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-6">No messages in this conversation.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection