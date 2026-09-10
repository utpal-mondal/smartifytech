<!-- Sidebar -->
<div class="w-64 bg-white shadow-md relative">
    <div class="p-4 text-center border-b">
        <!-- Logo -->
        <img 
            src="{{ asset('images/logo.png') }}" alt="Smartify Tech Logo" class="mx-auto mb-3 h-8 w-auto"/>

        <!-- Admin Panel Title -->
        <h2 class="text-2xl font-bold text-gray-800">Admin Panel</h2>
    </div>

    <nav class="mt-4">
        @if(auth()->user()->role === 'superadmin')

            <a href="{{ route('admin.index') }}"
               class="block py-2 px-4
               {{ request()->is('admin/dashboard') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Product Price 
            </a>

            <a href="{{ route('admin.change.password') }}"
               class="block py-2 px-4
               {{ request()->is('admin/change-password') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Change Password
            </a>

            <a href="{{ route('admin.banner') }}"
               class="block py-2 px-4
               {{ request()->is('admin/banner') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Banner List
            </a>

            <a href="{{ route('admin.chat-history') }}"
               class="block py-2 px-4
               {{ request()->is('admin/chat-history*') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Chat History
            </a>

            <a href="{{ route('admin.enquiries') }}"
               class="block py-2 px-4
               {{ request()->is('admin/enquiries*') ? 'bg-blue-600 text-white' : 'text-gray-700' }}">
                Enquiry
            </a>

        @endif
    </nav>



    <div class="absolute bottom-0 w-64 p-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                Logout
            </button>
        </form>
    </div>
</div>
