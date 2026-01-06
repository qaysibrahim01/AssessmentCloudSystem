@extends('layouts.dashboard')

@section('content')

<div class="max-w-5xl mx-auto space-y-8 text-sm">

    <!-- HEADER -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">NRA</h1>
            <p class="text-gray-500">
                Company: {{ $nra->company_name }} · Status: {{ ucfirst($nra->status) }}
            </p>
        </div>

        <a href="{{ route('nra.index') }}"
           class="border px-4 py-2 rounded hover:bg-gray-100">
            Back
        </a>
    </div>

    <!-- GENERAL COLUMN -->
    <section class="bg-white p-6 rounded shadow-sm space-y-4">
        <div class="flex items-center gap-3 font-semibold text-lg">
            <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">
                1
            </span>
            <span>General Information</span>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <div class="text-xs text-gray-500">Company Name</div>
                <div class="font-medium">{{ $nra->company_name }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Assessment Type</div>
                <div class="font-medium capitalize">{{ $nra->assessment_type }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-xs text-gray-500">Address</div>
                <div class="font-medium">{{ $nra->company_address }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Assessment Date</div>
                <div class="font-medium">
                    {{ optional($nra->assessment_date)->format('d M Y') ?: '—' }}
                </div>
            </div>
            <div>
                <div class="text-xs text-gray-500">Assessor</div>
                <div class="font-medium">{{ $nra->assessor_name }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-xs text-gray-500">Assessment Scope</div>
                <div class="font-medium leading-relaxed">
                    {{ $nra->assessment_scope ?: '—' }}
                </div>
            </div>
        </div>
    </section>

    <section class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded">
        <div class="font-semibold">Next layout sections</div>
        <p class="mt-1">
            General details are captured above. We can add the detailed NRA layout here next (noise sources, exposure measurements, controls, etc.) once finalized.
        </p>
    </section>
</div>

@endsection
