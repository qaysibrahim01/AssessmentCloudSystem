@extends('layouts.dashboard')

@section('content')

<div class="space-y-6">

    <h1 class="text-2xl font-semibold text-gray-800">
        Committee Dashboard
    </h1>

    <p class="text-sm text-gray-600">
        Welcome. You may view approved Chemical Health Risk Assessment (CHRA) reports
        and download official assessment documents.
    </p>

    <div class="mt-6">
        <a href="{{ route('committee.chra.index') }}"
           class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            View Approved CHRA Reports
        </a>
    </div>

</div>

@endsection
