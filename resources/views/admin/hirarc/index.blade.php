@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-6 text-sm">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">HIRARC List</h1>
        <a href="{{ route('admin.hirarc.uploaded.create') }}"
           class="bg-green-600 text-white px-3 py-1.5 rounded text-xs">
            + Upload HIRARC PDF
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
                @forelse($hirarcs as $h)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 border">#{{ $h->id }}</td>
                        <td class="px-3 py-2 border">{{ $h->company_name }}</td>
                        <td class="px-3 py-2 border capitalize">{{ $h->status }}</td>
                        <td class="px-3 py-2 border">
                            {{ $h->isUploaded() ? 'Uploaded' : 'System' }}
                        </td>
                        <td class="px-3 py-2 border">
                            <a href="{{ route('admin.hirarc.show', $h) }}" class="text-blue-600 text-xs">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-3 py-3 text-center text-gray-500">No HIRARC records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
