@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-6 text-sm">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold">
                CHRA Details
            </h1>
            <p class="text-gray-500">
                Company: {{ $chra->company_name }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('committee.chra.pdf', $chra) }}"
               class="bg-green-600 text-white px-4 py-2 rounded">
                Download PDF
            </a>

            <a href="{{ route('committee.chra.index') }}"
               class="border px-4 py-2 rounded">
                Back
            </a>
        </div>
    </div>

    {{-- Reuse existing CHRA display --}}
    @include('chra.partials.view', ['chra' => $chra])

    @if($chra->uploaded_pdf_path)
        <div class="mt-6">
            <a href="{{ route('committee.chra.pdf', $chra) }}"
            class="bg-green-600 text-white px-4 py-2 rounded">
                Download Official PDF
            </a>
        </div>
    @endif


</div>

@endsection
