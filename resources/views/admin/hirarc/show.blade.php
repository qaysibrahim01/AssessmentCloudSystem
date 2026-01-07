@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-8 text-sm">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">HIRARC Review</h1>
            <p class="text-gray-500">
                {{ $hirarc->company_name }} • Status: <span class="capitalize">{{ $hirarc->status }}</span>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.hirarc.index') }}" class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">Back</a>
            @if(in_array($hirarc->status, ['pending','approved']))
            <a href="{{ route('hirarc.pdf', $hirarc) }}" class="bg-green-600 text-white px-3 py-1.5 rounded text-xs">Download PDF</a>
            @endif
        </div>
    </div>

    <div class="bg-white border rounded-lg p-8 space-y-6">
        @include('hirarc.report')
    </div>

    @if($hirarc->status === 'pending')
    <div class="flex gap-3">
        <form method="POST" action="{{ route('admin.hirarc.approve', $hirarc) }}">
            @csrf
            <button class="bg-green-600 text-white px-4 py-2 rounded text-sm">Approve</button>
        </form>
        <form method="POST" action="{{ route('admin.hirarc.reject', $hirarc) }}">
            @csrf
            <input name="reason" placeholder="Rejection reason" class="border px-3 py-2 rounded text-sm" required>
            <button class="bg-red-600 text-white px-4 py-2 rounded text-sm">Reject</button>
        </form>
    </div>
    @endif
</div>

@endsection
