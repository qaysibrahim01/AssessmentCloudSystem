@extends('layouts.dashboard')

@section('content')

<div class="space-y-5">

    <!-- HEADER -->
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-xl font-semibold text-gray-800">
            HIRARC Assessments
        </h1>

        <a href="{{ route('hirarc.create') }}"
           class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded hover:bg-blue-700">
            + New HIRARC
        </a>
    </div>

    <!-- FILTER & SORT -->
    <div class="bg-white px-4 py-3 rounded shadow-sm">
        <form method="GET"
              action="{{ route('hirarc.index') }}"
              class="grid grid-cols-1 md:grid-cols-5 gap-3 text-xs items-end">

            <!-- STATUS -->
            <div>
                <label class="block text-gray-500 mb-1">Status</label>
                <select name="status"
                        class="border rounded px-2 py-1.5 w-full text-xs">
                    <option value="">All</option>
                    @foreach(['draft','pending','approved','rejected'] as $status)
                        <option value="{{ $status }}"
                            {{ request('status') === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- SORT BY -->
            <div>
                <label class="block text-gray-500 mb-1">Sort By</label>
                <select name="sort_by"
                        class="border rounded px-2 py-1.5 w-full text-xs">
                    <option value="created_at">Created Date</option>
                    <option value="company_name">Company Name</option>
                    <option value="status">Status</option>
                </select>
            </div>

            <!-- ORDER -->
            <div>
                <label class="block text-gray-500 mb-1">Order</label>
                <select name="sort_order"
                        class="border rounded px-2 py-1.5 w-full text-xs">
                    <option value="desc">Descending</option>
                    <option value="asc">Ascending</option>
                </select>
            </div>

            <button class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs">
                Apply
            </button>

            <a href="{{ route('hirarc.index') }}"
               class="border px-3 py-1.5 rounded text-xs text-center hover:bg-gray-100">
                Reset
            </a>
        </form>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-3 py-2 text-left">ID</th>
                    <th class="px-3 py-2 text-left">Company</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-left">Created</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hirarcs as $hirarc)
                    <tr class="border-t">
                        <td class="px-3 py-2">#{{ $hirarc->id }}</td>
                        <td class="px-3 py-2">{{ $hirarc->company_name }}</td>
                        <td class="px-3 py-2 capitalize">{{ $hirarc->status }}</td>
                        <td class="px-3 py-2">
                            {{ optional($hirarc->created_at)->format('d M Y') }}
                        </td>
                        <td class="px-3 py-2 text-right">
                            @if($hirarc->isUploaded())
                                <a href="{{ route('hirarc.show.uploaded', $hirarc) }}"
                                   class="text-blue-600 hover:underline">
                                    View Uploaded
                                </a>
                            @elseif($hirarc->canEdit())
                                <a href="{{ route('hirarc.edit', $hirarc) }}"
                                   class="text-blue-600 hover:underline">
                                    Edit / Continue
                                </a>
                            @else
                                <a href="{{ route('hirarc.show', $hirarc) }}"
                                   class="text-blue-600 hover:underline">
                                    View
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-10 text-center text-gray-500">
                            No HIRARC assessments yet. Start by creating one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
