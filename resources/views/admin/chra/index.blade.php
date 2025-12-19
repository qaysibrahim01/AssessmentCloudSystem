@extends('layouts.dashboard')

@section('content')

<h1 class="text-2xl font-semibold mb-6">
    Admin – CHRA Reviews
</h1>

{{-- ===================== --}}
{{-- STATISTICS --}}
{{-- ===================== --}}
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">

    <div class="bg-white p-4 rounded shadow">
        <div class="text-xs text-gray-500">Total</div>
        <div class="text-2xl font-bold">{{ $totalChra }}</div>
    </div>

    <div class="bg-yellow-50 p-4 rounded shadow">
        <div class="text-xs text-yellow-700">Draft</div>
        <div class="text-2xl font-bold">{{ $draftCount }}</div>
    </div>

    <div class="bg-blue-50 p-4 rounded shadow">
        <div class="text-xs text-blue-700">Pending</div>
        <div class="text-2xl font-bold">{{ $pendingCount }}</div>
    </div>

    <div class="bg-green-50 p-4 rounded shadow">
        <div class="text-xs text-green-700">Approved</div>
        <div class="text-2xl font-bold">{{ $approvedCount }}</div>
    </div>

    <div class="bg-red-50 p-4 rounded shadow">
        <div class="text-xs text-red-700">Rejected</div>
        <div class="text-2xl font-bold">{{ $rejectedCount }}</div>
    </div>

</div>

{{-- ===================== --}}
{{-- RISK SUMMARY --}}
{{-- ===================== --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

    <div class="bg-red-100 p-4 rounded shadow">
        <div class="text-sm font-medium text-red-800">High Risk</div>
        <div class="text-xl font-bold">{{ $highRiskCount }}</div>
    </div>

    <div class="bg-yellow-100 p-4 rounded shadow">
        <div class="text-sm font-medium text-yellow-800">Moderate Risk</div>
        <div class="text-xl font-bold">{{ $moderateRiskCount }}</div>
    </div>

    <div class="bg-green-100 p-4 rounded shadow">
        <div class="text-sm font-medium text-green-800">Low Risk</div>
        <div class="text-xl font-bold">{{ $lowRiskCount }}</div>
    </div>

</div>

{{-- ===================== --}}
{{-- FILTERS --}}
{{-- ===================== --}}
<form method="GET"
      class="bg-white p-4 rounded shadow mb-6 grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">

    <select name="status" class="border rounded px-3 py-2">
        <option value="">All Status</option>
        <option value="draft" @selected($status === 'draft')>Draft</option>
        <option value="pending" @selected($status === 'pending')>Pending</option>
        <option value="approved" @selected($status === 'approved')>Approved</option>
        <option value="rejected" @selected($status === 'rejected')>Rejected</option>
    </select>

    <select name="risk" class="border rounded px-3 py-2">
        <option value="">All Risk Levels</option>
        <option value="high" @selected($risk === 'high')>High</option>
        <option value="moderate" @selected($risk === 'moderate')>Moderate</option>
        <option value="low" @selected($risk === 'low')>Low</option>
    </select>

    <select name="ap" class="border rounded px-3 py-2">
        <option value="">All Action Priority</option>
        <option value="AP-1" @selected($ap === 'AP-1')>AP-1</option>
        <option value="AP-2" @selected($ap === 'AP-2')>AP-2</option>
        <option value="AP-3" @selected($ap === 'AP-3')>AP-3</option>
    </select>

    <button class="bg-blue-600 text-white rounded px-4 py-2">
        Apply Filters
    </button>

</form>

{{-- ===================== --}}
{{-- TABLE --}}
{{-- ===================== --}}
<div class="bg-white rounded shadow overflow-hidden">

    <table class="w-full text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">Company</th>
                <th class="px-3 py-2 text-left">Assessor</th>
                <th class="px-3 py-2 text-left">Status</th>
                <th class="px-3 py-2 text-right">Action</th>
            </tr>
        </thead>

        <tbody class="divide-y">
            @forelse($chras as $chra)
                <tr>
                    <td class="px-3 py-2">#{{ $chra->id }}</td>
                    <td class="px-3 py-2">{{ $chra->company_name }}</td>
                    <td class="px-3 py-2">{{ $chra->assessor_name }}</td>
                    <td class="px-3 py-2 capitalize">{{ $chra->status }}</td>
                    <td class="px-3 py-2 text-right">
                        <a href="{{ route('admin.chra.show', $chra) }}"
                           class="text-blue-600 hover:underline">
                            Review
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5"
                        class="text-center py-6 text-gray-500">
                        No CHRA records found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
