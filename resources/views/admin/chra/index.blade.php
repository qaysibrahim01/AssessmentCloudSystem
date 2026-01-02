@extends('layouts.dashboard')

@section('content')

{{-- ================= TABS ================= --}}
<div class="flex gap-3 mb-6 text-sm">
    <a href="{{ route('admin.chra.index') }}"
       class="px-4 py-2 rounded border
              {{ request('view') === null
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'bg-white text-gray-700 hover:bg-gray-100' }}">
        CHRA List
    </a>

    <a href="{{ route('admin.chra.index', ['view' => 'delete']) }}"
       class="px-4 py-2 rounded border
              {{ request('view') === 'delete'
                    ? 'bg-yellow-500 text-white border-yellow-500'
                    : 'bg-white text-gray-700 hover:bg-yellow-50' }}">
        Delete Requests
        @if($pendingDeleteCount)
            <span class="ml-1 text-xs px-1.5 py-0.5 rounded-full bg-red-600 text-white">
                {{ $pendingDeleteCount }}
            </span>
        @endif
    </a>

    <a href="{{ route('admin.chra.index', ['view' => 'deleted']) }}"
       class="px-4 py-2 rounded border
              {{ request('view') === 'deleted'
                    ? 'bg-red-600 text-white border-red-600'
                    : 'bg-white text-gray-700 hover:bg-red-50' }}">
        Deleted Registry
    </a>
</div>

<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold text-gray-800">
        Admin – CHRA Reviews
    </h1>

    <a href="{{ route('admin.chra.uploaded.create') }}"
       class="bg-green-600 text-white px-4 py-2 rounded text-sm">
        + Upload CHRA PDF
    </a>
</div>


{{-- ================= FILTER & SEARCH ================= --}}
@if(!request('view'))
<div class="bg-white px-4 py-3 rounded shadow-sm mb-4">
    <form method="GET"
          class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">

        {{-- STATUS --}}
        <div>
            <label class="block text-gray-500 mb-1">Status</label>
            <select name="status" class="border rounded px-2 py-1.5 w-full">
                <option value="">All</option>
                @foreach(['draft','pending','approved','rejected'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- REPORT TYPE --}}
        <div>
            <label class="block text-gray-500 mb-1">Report Type</label>
            <select name="type" class="border rounded px-2 py-1.5 w-full">
                <option value="">All</option>
                <option value="system" @selected(request('type') === 'system')>
                    System Generated
                </option>
                <option value="uploaded" @selected(request('type') === 'uploaded')>
                    Uploaded Report
                </option>
            </select>
        </div>

        {{-- SEARCH --}}
        <div>
            <label class="block text-gray-500 mb-1">Search</label>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="ID / Company / Assessor"
                   class="border rounded px-2 py-1.5 w-full">
        </div>

        <div class="flex items-end">
            <button class="bg-blue-600 text-white px-4 py-1.5 rounded w-full">
                Apply
            </button>
        </div>
    </form>
</div>
@endif

{{-- ================= LIST ================= --}}
@if(!request('view'))
<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-xs">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Company</th>
                <th class="px-3 py-2 text-left">Assessor</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-center">Official PDF</th>
                <th class="px-3 py-2 text-right">Review</th>
            </tr>
        </thead>

        <tbody class="divide-y">
        @forelse($chras as $chra)
            <tr>
                <td class="px-3 py-1.5">#{{ $chra->id }}</td>
                <td class="px-3 py-1.5">{{ $chra->company_name }}</td>
                <td class="px-3 py-1.5">{{ $chra->assessor_name }}</td>
                <td class="px-3 py-1.5">{{ ucfirst($chra->status) }}</td>

                {{-- REPORT TYPE --}}
                <td class="px-3 py-1.5 text-center">
                    @if($chra->isUploaded())
                        <span class="text-blue-600 font-semibold text-xs">
                            Uploaded Report
                        </span>
                    @else
                        <span class="text-green-600 font-semibold text-xs">
                            System Generated
                        </span>
                    @endif
                </td>

                <td class="px-3 py-1.5 text-right">
                    <a href="{{ route('admin.chra.show', $chra) }}"
                    class="text-blue-600 hover:underline">
                        Review
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6"
                    class="px-3 py-6 text-center text-gray-500">
                    No CHRA records found
                </td>
            </tr>
        @endforelse
        </tbody>

    </table>
</div>
@endif

{{-- ================= UPLOAD MODAL ================= --}}
<div id="uploadModal"
     class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded p-6 w-96">
        <h3 class="font-semibold mb-3 text-sm">
            Upload Official CHRA PDF
        </h3>

        <form method="POST" id="uploadForm" enctype="multipart/form-data">
            @csrf

            <input type="file"
                   name="pdf"
                   accept="application/pdf"
                   required
                   class="border w-full px-3 py-2 rounded text-sm">

            <div class="flex justify-end gap-2 mt-4">
                <button type="button"
                        onclick="closeUploadModal()"
                        class="border px-3 py-1 rounded text-sm">
                    Cancel
                </button>

                <button class="bg-green-600 text-white px-4 py-1 rounded text-sm">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openUploadModal(chraId) {
    document.getElementById('uploadForm').action =
        `/admin/chra/${chraId}/upload-pdf`;
    document.getElementById('uploadModal').classList.remove('hidden');
    document.getElementById('uploadModal').classList.add('flex');
}

function closeUploadModal() {
    document.getElementById('uploadModal').classList.add('hidden');
}
</script>

@endsection
