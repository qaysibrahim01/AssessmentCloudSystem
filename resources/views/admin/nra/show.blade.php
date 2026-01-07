@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-8 text-sm">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">NRA Review</h1>
            <p class="text-gray-500">
                {{ $nra->company_name }} • Status: <span class="capitalize">{{ $nra->status }}</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.nra.index') }}" class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">Back</a>
            @if(in_array($nra->status, ['pending','approved']))
            <a href="{{ route('nra.pdf', $nra) }}" class="bg-green-600 text-white px-3 py-1.5 rounded text-xs">Download PDF</a>
            @endif
        </div>
    </div>

    <div class="bg-white border rounded-lg p-8 space-y-6">
        @include('nra.report')
    </div>

    @if($nra->status === 'pending')
    <div class="flex gap-3">
        <form method="POST" action="{{ route('admin.nra.approve', $nra) }}">
            @csrf
            <button class="bg-green-600 text-white px-4 py-2 rounded text-sm">Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.nra.reject', $nra) }}">
            @csrf
            <input name="reason" placeholder="Rejection reason" class="border px-3 py-2 rounded text-sm" required>
            <button class="bg-red-600 text-white px-4 py-2 rounded text-sm">Reject</button>
        </form>
    </div>
    @endif
</div>

@endsection
