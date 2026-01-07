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

        <!-- HIRARC CARD -->
        <a href="{{ route('committee.hirarc.index') }}"
           class="group bg-white rounded-xl border p-6 shadow-sm hover:shadow-md hover:border-blue-500 transition cursor-pointer">
            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                HIRARC
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Hazard Identification, Risk Assessment & Control
            </p>
            <div class="mt-4 text-sm text-blue-600 font-medium opacity-0 group-hover:opacity-100 transition">
                Open +
            </div>
        </a>

        <!-- NRA CARD -->
        <a href="{{ route('committee.nra.index') }}"
           class="group bg-white rounded-xl border p-6 shadow-sm hover:shadow-md hover:border-blue-500 transition cursor-pointer">
            <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600">
                NRA
            </h3>
            <p class="text-sm text-gray-500 mt-1">
                Noise Risk Assessment
            </p>
            <div class="mt-4 text-sm text-blue-600 font-medium opacity-0 group-hover:opacity-100 transition">
                Open +
            </div>
        </a>
    </div>

</div>

@endsection
