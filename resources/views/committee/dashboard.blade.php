@extends('layouts.dashboard')

@section('content')

<div class="space-y-6">

    <h1 class="text-2xl font-semibold text-gray-800">
        Committee Dashboard
    </h1>

    <p class="text-sm text-gray-600">
        You can view approved assessments for your company only.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <!-- CHRA CARD -->
        <a href="{{ route('committee.chra.index') }}"
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

        <!-- HIRARC (VIEW ONLY PLACEHOLDER) -->
        <div class="group bg-white rounded-xl border p-6 shadow-sm hover:shadow-md hover:border-blue-500 transition cursor-not-allowed opacity-70">
            <h3 class="text-lg font-semibold text-gray-700">
                HIRARC
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Hazard Identification, Risk Assessment & Control
            </p>
            <div class="mt-4 text-sm text-gray-400">
                View-only access coming soon
            </div>
        </div>

        <!-- NRA (VIEW ONLY PLACEHOLDER) -->
        <div class="group bg-white rounded-xl border p-6 shadow-sm hover:shadow-md hover:border-blue-500 transition cursor-not-allowed opacity-70">
            <h3 class="text-lg font-semibold text-gray-700">
                NRA
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Noise Risk Assessment
            </p>
            <div class="mt-4 text-sm text-gray-400">
                View-only access coming soon
            </div>
        </div>
    </div>

</div>

@endsection
