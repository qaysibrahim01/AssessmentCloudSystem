@extends('layouts.dashboard')

@section('content')

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Users</h1>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex gap-3 border-b pb-2">
        <a href="{{ route('admin.users.index', ['tab' => 'users', 'search' => request('search')]) }}"
           class="px-3 py-2 text-sm rounded {{ $tab === 'users' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-50' }}">
            Users
        </a>
        <a href="{{ route('admin.users.index', ['tab' => 'requests', 'search' => request('search')]) }}"
           class="px-3 py-2 text-sm rounded {{ $tab === 'requests' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-600 hover:bg-gray-50' }}">
            Registration Requests ({{ $pending->total() }})
        </a>
    </div>

    <div class="bg-white border rounded p-4 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="md:col-span-3">
                <label class="text-xs text-gray-500">Search (name, email, company, role)</label>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       class="border rounded px-3 py-2 w-full"
                       placeholder="e.g. Jane, example@company.com, committee">
            </div>
            <div>
                <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">
                    Apply
                </button>
            </div>
        </form>
    </div>

    @if(auth()->user()->email === 'admin@example.com')
        <div class="bg-white border rounded p-4 shadow-sm space-y-3">
            <h2 class="text-lg font-semibold">Create Admin (master admin only)</h2>
            <form method="POST" action="{{ route('admin.users.create-admin') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                @csrf
                <input type="text" name="name" placeholder="Admin name" class="border rounded px-3 py-2" required>
                <input type="email" name="email" placeholder="Admin email" class="border rounded px-3 py-2" required>
                <input type="password" name="password" placeholder="Password (min 8 chars)" class="border rounded px-3 py-2" required>
                <div class="flex items-center">
                    <button class="bg-green-600 text-white px-4 py-2 rounded w-full">Create Admin</button>
                </div>
            </form>
        </div>
    @endif

    @if($tab === 'requests')
        <div class="bg-white border rounded shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-3 py-2 text-left">Name</th>
                        <th class="px-3 py-2 text-left">Email</th>
                        <th class="px-3 py-2 text-left">Role</th>
                        <th class="px-3 py-2 text-left">Company</th>
                        <th class="px-3 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($pending as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $user->name }}</td>
                            <td class="px-3 py-2">{{ $user->email }}</td>
                            <td class="px-3 py-2 capitalize">{{ $user->role }}</td>
                            <td class="px-3 py-2">{{ $user->company_name ?? '-' }}</td>
                            <td class="px-3 py-2 text-right space-x-2">
                                <form method="POST"
                                      action="{{ route('admin.users.approve', $user) }}"
                                      class="inline">
                                    @csrf
                                    <button class="px-3 py-1 text-xs bg-green-600 text-white rounded">
                                        Approve
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('admin.users.reject', $user) }}"
                                      class="inline">
                                    @csrf
                                    <button class="px-3 py-1 text-xs bg-red-600 text-white rounded">
                                        Reject
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-500 text-sm">
                                No pending registrations.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-3">
                {{ $pending->links() }}
            </div>
        </div>
    @else
        <div class="bg-white border rounded shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-3 py-2 text-left">Name</th>
                        <th class="px-3 py-2 text-left">Email</th>
                        <th class="px-3 py-2 text-left">Role</th>
                        <th class="px-3 py-2 text-left">Company</th>
                        <th class="px-3 py-2 text-left">Approved At</th>
                        <th class="px-3 py-2 text-left">Approval Email Sent</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2">{{ $user->name }}</td>
                            <td class="px-3 py-2">{{ $user->email }}</td>
                            <td class="px-3 py-2 capitalize">{{ $user->role }}</td>
                            <td class="px-3 py-2">{{ $user->company_name ?? '-' }}</td>
                            <td class="px-3 py-2">
                                {{ optional($user->approved_at)->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-3 py-2">
                                {{ optional($user->approval_email_sent_at)->format('d M Y') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-3 py-4 text-center text-gray-500 text-sm">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-3">
                {{ $users->links() }}
            </div>
        </div>
    @endif
</div>

@endsection
