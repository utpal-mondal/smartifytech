@extends('layouts.auth')

@section('content')
<div class="flex min-h-screen bg-gradient-to-br from-gray-100 via-gray-50 to-gray-100">

    @include('layouts.navbar')

    <div class="flex-1 flex justify-center items-start py-10">
        <div class="max-w-7xl w-full space-y-8 px-4">

            <div class="flex items-center justify-between bg-white rounded-xl shadow p-6">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-800">Enquiries</h2>
                    <p class="text-sm text-gray-500 mt-1">Customers who requested a callback and left a phone number.</p>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-xl font-bold text-gray-800">Callback Requests</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4 text-left">#</th>
                                <th class="px-6 py-4 text-left">Name</th>
                                <th class="px-6 py-4 text-left">Email</th>
                                <th class="px-6 py-4 text-left">Phone</th>
                                <th class="px-6 py-4 text-left">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($conversations as $conversation)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">{{ $conversation->id }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-800">{{ $conversation->name ?? 'Guest' }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $conversation->email ?? '-' }}</td>
                                    <td class="px-6 py-4 text-gray-800 font-medium">{{ $conversation->phone }}</td>
                                    <td class="px-6 py-4 text-gray-500">
                                        {{ $conversation->created_at->format('Y-m-d H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
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