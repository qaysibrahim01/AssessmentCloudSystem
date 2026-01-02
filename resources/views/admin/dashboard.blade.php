@extends('layouts.dashboard')

@section('content')

<h1 class="text-2xl font-semibold mb-6">
    Admin Dashboard
</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    {{-- CHRA MODULE --}}
    <a href="{{ route('admin.chra.index') }}"
       class="group bg-white rounded-lg border p-6 hover:border-gray-400 transition">

        <h3 class="text-lg font-semibold text-gray-800">
            CHRA
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Chemical Health Risk Assessment
        </p>

        <div class="mt-4 text-xs text-gray-400 group-hover:text-gray-600">
            Open →
        </div>
    </a>

    {{-- HIRARC (placeholder) --}}
    <div class="bg-gray-50 rounded-lg border p-6 opacity-60 cursor-not-allowed">
        <h3 class="text-lg font-semibold text-gray-500">
            HIRARC
        </h3>
        <p class="text-sm text-gray-400 mt-1">
            Coming soon
        </p>
    </div>

    {{-- NRA (placeholder) --}}
    <div class="bg-gray-50 rounded-lg border p-6 opacity-60 cursor-not-allowed">
        <h3 class="text-lg font-semibold text-gray-500">
            NRA
        </h3>
        <p class="text-sm text-gray-400 mt-1">
            Coming soon
        </p>
    </div>

</div>

@endsection
