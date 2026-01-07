@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-6 text-sm">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">Approved HIRARC</h1>
    </div>

    <div class="bg-white border rounded shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left border">ID</th>
                    <th class="px-3 py-2 text-left border">Company</th>
                    <th class="px-3 py-2 text-left border">Approved At</th>
                    <th class="px-3 py-2 text-left border">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($hirarcs as $h)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 border">#{{ $h->id }}</td>
                        <td class="px-3 py-2 border">{{ $h->company_name }}</td>
                        <td class="px-3 py-2 border">{{ optional($h->approved_at)->format('d M Y') ?? '-' }}</td>
                        <td class="px-3 py-2 border">
                            @if($h->isUploaded())
                                <a href="{{ route('committee.hirarc.show.uploaded', $h) }}" class="text-blue-600 text-xs">
                                    View Uploaded
                                </a>
                            @else
                                <a href="{{ route('committee.hirarc.show', $h) }}" class="text-blue-600 text-xs">View</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-3 py-3 text-center text-gray-500">No approved HIRARC.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
