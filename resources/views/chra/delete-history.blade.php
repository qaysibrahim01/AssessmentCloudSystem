@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-6 text-sm">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold">
            Deleted CHRA History
        </h1>

        <a href="{{ route('chra.index') }}"
           class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded">
            ← Back to CHRA
        </a>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-2 text-left">CHRA ID</th>
                    <th class="px-4 py-2 text-left">Company</th>
                    <th class="px-4 py-2 text-left">Reason</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Admin Remark</th>
                    <th class="px-4 py-2 text-left">Reviewed At</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($deleteRequests as $req)
                    <tr>
                        <td class="px-4 py-2">
                            #{{ $req->chra->id ?? '-' }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $req->chra->company_name ?? '[Deleted]' }}
                        </td>

                        <td class="px-4 py-2">
                            {{ $req->reason }}
                        </td>

                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs
                                @if($req->status === 'approved') bg-green-100 text-green-700
                                @elseif($req->status === 'rejected') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700
                                @endif">
                                {{ ucfirst($req->status) }}
                            </span>
                        </td>

                        <td class="px-4 py-2">
                            {{ $req->admin_remark ?? '-' }}
                        </td>

                        <td class="px-4 py-2">
                            {{ optional($req->reviewed_at)->format('d M Y H:i') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            class="px-4 py-6 text-center text-gray-500">
                            No delete history found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
