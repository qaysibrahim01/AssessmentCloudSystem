@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-6 text-sm">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">NRA List</h1>
        <a href="{{ route('admin.nra.uploaded.create') }}"
           class="bg-green-600 text-white px-3 py-1.5 rounded text-xs">
            + Upload NRA PDF
        </a>
    </div>

    <div class="bg-white border rounded shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left border">ID</th>
                    <th class="px-3 py-2 text-left border">Company</th>
                    <th class="px-3 py-2 text-left border">Status</th>
                    <th class="px-3 py-2 text-left border">Type</th>
                    <th class="px-3 py-2 text-left border">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($nras as $n)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 border">#{{ $n->id }}</td>
                        <td class="px-3 py-2 border">{{ $n->company_name }}</td>
                        <td class="px-3 py-2 border capitalize">{{ $n->status }}</td>
                        <td class="px-3 py-2 border">
                            {{ $n->isUploaded() ? 'Uploaded' : 'System' }}
                        </td>
                        <td class="px-3 py-2 border">
                            <a href="{{ route('admin.nra.show', $n) }}" class="text-blue-600 text-xs">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-3 py-3 text-center text-gray-500">No NRA records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
