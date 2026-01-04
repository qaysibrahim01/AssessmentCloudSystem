@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-6 text-sm">

    <div class="flex justify-between items-start border-b pb-4">
        <div>
            <h1 class="text-2xl font-semibold">
                Uploaded CHRA Report
            </h1>
            <p class="text-gray-500">
                {{ $chra->company_name }} ·
                Assessment Date: {{ $chra->assessment_date?->format('d M Y') }}
            </p>
        </div>

        <a href="{{ route('chra.index') }}"
           class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">
            Back
        </a>
    </div>

    <div class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-2 text-sm">Report Summary</h2>
        <p class="whitespace-pre-line text-gray-700">
            {{ $chra->assessment_objective ?? 'No summary provided.' }}
        </p>
    </div>

    <div class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-3 text-sm">Official CHRA Document</h2>

        <iframe
            src="{{ asset('storage/' . $chra->uploaded_pdf_path) }}"
            class="w-full h-[85vh] border rounded"
        ></iframe>
    </div>

</div>

@endsection
