@extends('layouts.dashboard')

@section('content')

<h1 class="text-2xl font-semibold mb-6">Assessment Dashboard</h1>

<div class="flex gap-3 mb-6">
    <input
        type="text"
        placeholder="Search assessment..."
        class="flex-1 border rounded px-4 py-2"
    >
    <button class="bg-blue-600 text-white px-6 rounded">
        Search
    </button>
</div>

<div class="space-y-4">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

        <!-- CHRA CARD -->
        <a href="{{ route('chra.index') }}"
        class="group bg-white rounded-xl border p-6 shadow-sm hover:shadow-md hover:border-blue-500 transition cursor-pointer">

            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                CHRA
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Chemical Health Risk Assessment
            </p>

            <div class="mt-4 text-sm text-blue-600 font-medium opacity-0 group-hover:opacity-100 transition">
                Open →
            </div>
        </a>

        <!-- HIRARC CARD (DISABLED) -->
        <div
            class="group bg-white rounded-xl border p-6 shadow-sm hover:shadow-md hover:border-blue-500 transition cursor-pointer">

            <h3 class="text-lg font-semibold text-gray-700">
                HIRARC
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Hazard Identification, Risk Assessment & Control
            </p>

            <div class="mt-4 text-sm text-gray-400">
                Coming Soon
            </div>
        </div>

        <!-- NRA CARD (DISABLED) -->
        <div
            class="group bg-white rounded-xl border p-6 shadow-sm hover:shadow-md hover:border-blue-500 transition cursor-pointer">

            <h3 class="text-lg font-semibold text-gray-700">
                NRA
            </h3>

            <p class="text-sm text-gray-500 mt-1">
                Noise Risk Assessment
            </p>

            <div class="mt-4 text-sm text-gray-400">
                Coming Soon
            </div>
        </div>

    </div>


</div>

@endsection
