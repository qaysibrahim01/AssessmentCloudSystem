<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AssessmentCS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">
<div x-data="{ sidebarOpen: false, userMenu: false }" class="h-screen flex flex-col">

    <!-- TOP HEADER -->
    <header class="h-16 bg-white border-b flex items-center justify-between px-6">

        <!-- LEFT -->
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 text-2xl leading-none">
                &#9776;
            </button>

            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="{{ asset('images/logo.jpg') }}" class="h-10" alt="AssessmentCS">
            </a>
        </div>

        <!-- USER -->
        <div class="relative">
            <button @click="userMenu = !userMenu" class="text-sm">
                {{ auth()->user()->name }}
            </button>

            <div x-show="userMenu"
                 @click.outside="userMenu=false"
                 class="absolute right-0 mt-2 w-40 bg-white border rounded shadow">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="block w-full text-left px-4 py-2 hover:bg-gray-100">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">

        <!-- SIDEBAR -->
        <aside x-show="sidebarOpen"
               class="w-64 bg-white border-r p-4 text-sm">

            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded hover:bg-gray-100 font-medium">
                Dashboard
            </a>

            {{-- ASSESSOR --}}
            @if(auth()->user()->role === 'assessor')
                <a href="{{ route('chra.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    CHRA
                </a>
                <a href="{{ route('hirarc.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    HIRARC
                </a>
                <a href="{{ route('nra.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    NRA
                </a>
            @endif

            {{-- ADMIN --}}
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.chra.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    CHRA Reviews
                </a>
                <a href="{{ route('admin.hirarc.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    HIRARC Reviews
                </a>
                <a href="{{ route('admin.nra.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    NRA Reviews
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    User Approvals
                </a>
            @endif

            {{-- COMMITTEE --}}
            @if(auth()->user()->role === 'committee')
                <a href="{{ route('committee.chra.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    CHRA
                </a>

                <a href="{{ route('committee.hirarc.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    HIRARC
                </a>

                <a href="{{ route('committee.nra.index') }}"
                   class="block px-3 py-2 rounded hover:bg-gray-100">
                    NRA
                </a>
            @endif

        </aside>

        <!-- CONTENT -->
        <main class="flex-1 p-6 overflow-y-auto">
            @yield('content')
        </main>
    </div>
</div>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
