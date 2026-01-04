@extends('layouts.dashboard')

@section('content')

<div class="space-y-5">

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-2 rounded text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif



    <!-- HEADER -->
    <div class="flex items-center justify-between gap-4">
        <h1 class="text-xl font-semibold text-gray-800">
            CHRA Reports
        </h1>

        <div class="flex items-center gap-4">
            <a href="{{ route('chra.delete.history', ['view' => 'deleted']) }}"
            class="px-3 py-1.5 text-xs rounded border
                    {{ request('view') === 'deleted'
                            ? 'bg-red-600 text-white border-red-600'
                            : 'bg-white text-gray-700 hover:bg-red-50' }}">
                Deleted Registry
            </a>

            <a href="{{ route('chra.create') }}"
            class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded hover:bg-blue-700">
                + New CHRA
            </a>
        </div>
    </div>


    <!-- FILTER & SORT -->
    <div class="bg-white px-4 py-3 rounded shadow-sm">
        <form method="GET"
              action="{{ route('chra.index') }}"
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

            <a href="{{ route('chra.index') }}"
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

            <tbody class="divide-y">
                @forelse($chras as $chra)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-1.5">#{{ $chra->id }}</td>
                        <td class="px-3 py-1.5">{{ $chra->company_name }}</td>

                        <td class="px-3 py-1.5">
                            <span class="px-2 py-0.5 rounded text-[11px]
                                {{ $chra->status === 'approved' ? 'bg-green-100 text-green-700' :
                                   ($chra->status === 'rejected' ? 'bg-red-100 text-red-700' :
                                   ($chra->status === 'pending' ? 'bg-blue-100 text-blue-700' :
                                   'bg-yellow-100 text-yellow-700')) }}">
                                {{ ucfirst($chra->status) }}
                            </span>
                        </td>

                        <td class="px-3 py-1.5">
                            {{ $chra->created_at->format('d M Y') }}
                        </td>

                        <td class="px-3 py-1.5 text-right space-x-2">
                            @if($chra->isUploaded())
                                <a href="{{ route('chra.show.uploaded', $chra) }}"
                                class="text-blue-600 hover:underline">
                                    View PDF
                                </a>
                            @else
                                <a href="{{ route('chra.show', $chra) }}"
                                class="text-blue-600 hover:underline">
                                    View
                                </a>
                            @endif

                            @if(in_array($chra->status, ['draft', 'rejected']))

                                @php
                                    $latestDelete = $chra->deleteRequests()
                                        ->where('status', 'pending')
                                        ->latest()
                                        ->first();
                                @endphp


                                @if(!$latestDelete)
                                    <button
                                        onclick="openDeleteModal({{ $chra->id }})"
                                        class="text-red-600 hover:underline text-xs">
                                        Request Delete
                                    </button>

                                @elseif($latestDelete->status === 'pending')
                                    <span class="text-gray-400 text-xs italic">
                                        Delete Request Pending
                                    </span>

                                @elseif($latestDelete->status === 'rejected')
                                    <button
                                        onclick="openDeleteModal({{ $chra->id }})"
                                        class="text-red-600 hover:underline text-xs"
                                        title="Previous request was rejected: {{ $latestDelete->admin_remark }}">
                                        Request Delete Again
                                    </button>
                                @endif

                            @endif

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5"
                            class="px-3 py-6 text-center text-gray-500 text-xs">
                            No CHRA reports found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- DELETE MODAL -->
<div id="deleteModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">

    <div class="bg-white rounded p-6 w-96">
        <h3 class="font-semibold mb-3 text-sm">
            Request CHRA Deletion
        </h3>

        <form method="POST" id="deleteForm">
            @csrf

            <textarea name="reason"
                      rows="3"
                      required
                      class="border w-full px-3 py-2 rounded text-sm"
                      placeholder="State the reason for deletion"></textarea>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button"
                        onclick="closeDeleteModal()"
                        class="border px-3 py-1 rounded text-sm">
                    Cancel
                </button>

                <button type="submit"
                        class="bg-red-600 text-white px-4 py-1 rounded text-sm">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>



<script>
function openDeleteModal(chraId) {
    const form = document.getElementById('deleteForm');
    form.action = `/chra/${chraId}/request-delete`;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').classList.remove('flex');
}
</script>


@endsection
