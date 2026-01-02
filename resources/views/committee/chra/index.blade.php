@extends('layouts.dashboard')

@section('content')

<div class="space-y-5">

    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-gray-800">
            Approved CHRA Reports
        </h1>
    </div>

    <!-- FILTER & SORT -->
    <div class="bg-white px-4 py-3 rounded shadow-sm">
        <form method="GET"
              action="{{ route('committee.chra.index') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs items-end">

            <!-- SORT BY -->
            <div>
                <label class="block text-gray-500 mb-1">Sort By</label>
                <select name="sort_by"
                        class="border rounded px-2 py-1.5 w-full text-xs">
                    <option value="approved_at"
                        {{ request('sort_by') === 'approved_at' ? 'selected' : '' }}>
                        Approved Date
                    </option>
                    <option value="company_name"
                        {{ request('sort_by') === 'company_name' ? 'selected' : '' }}>
                        Company Name
                    </option>
                </select>
            </div>

            <!-- ORDER -->
            <div>
                <label class="block text-gray-500 mb-1">Order</label>
                <select name="sort_order"
                        class="border rounded px-2 py-1.5 w-full text-xs">
                    <option value="desc"
                        {{ request('sort_order') === 'desc' ? 'selected' : '' }}>
                        Descending
                    </option>
                    <option value="asc"
                        {{ request('sort_order') === 'asc' ? 'selected' : '' }}>
                        Ascending
                    </option>
                </select>
            </div>

            <button class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs">
                Apply
            </button>

            <a href="{{ route('committee.chra.index') }}"
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
                    <th class="px-3 py-2 text-left">Approved At</th>
                    <th class="px-3 py-2 text-right">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($chras as $chra)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-1.5">#{{ $chra->id }}</td>
                        <td class="px-3 py-1.5">{{ $chra->company_name }}</td>
                        <td class="px-3 py-1.5">
                            {{ optional($chra->approved_at)->format('d M Y') }}
                        </td>
                        <td class="px-3 py-1.5 text-right">
                            <a href="{{ route('committee.chra.show', $chra) }}"
                               class="text-blue-600 hover:underline">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4"
                            class="px-3 py-6 text-center text-gray-500 text-xs">
                            No approved CHRA available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
