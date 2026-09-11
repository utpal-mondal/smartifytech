@extends('layouts.auth')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-gray-100">

    @include('layouts.navbar')

    <div class="flex-1 flex justify-center items-start py-10">
        <div class="max-w-7xl w-full space-y-8 px-4">

            <div class="flex items-center justify-between bg-white rounded-xl shadow p-6">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-800">Chat History</h2>
                    <p class="text-sm text-gray-500 mt-1">View all chat conversations from the website chatbox.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Chat Conversations</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">Name</th>
                                <th class="px-6 py-4 text-left">Email</th>
                                <th class="px-6 py-4 text-left">Messages</th>
                                <th class="px-6 py-4 text-left">Last Message</th>
                                <th class="px-6 py-4 text-left">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($conversations as $conversation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $conversation->id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $conversation->name ?? 'Guest' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $conversation->email ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $conversation->messages_count }}</td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $conversation->last_message_at ? $conversation->last_message_at->format('Y-m-d H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.chat-history.show', $conversation->id) }}"
                                           class="text-blue-600 hover:text-blue-800 font-medium">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        No enquiries found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($conversations->hasPages())
                    <div class="px-6 py-4 border-t">
                        {{ $conversations->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection