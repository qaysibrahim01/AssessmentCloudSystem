@extends('layouts.dashboard')

@section('content')

<div class="max-w-5xl mx-auto space-y-6 text-sm">

    <div class="flex justify-between items-start border-b pb-4">
        <div>
            <h1 class="text-2xl font-semibold">
                Uploaded HIRARC Report
            </h1>
            <p class="text-gray-500">
                {{ $hirarc->company_name }} -
                Assessment Date: {{ $hirarc->assessment_date?->format('d M Y') }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.hirarc.index') }}"
               class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">
                Back
            </a>

            <a href="{{ asset('storage/' . $hirarc->uploaded_pdf_path) }}"
               target="_blank"
               class="bg-green-600 text-white px-3 py-1.5 rounded text-xs">
                Download PDF
            </a>
        </div>
    </div>

    <div class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-2 text-sm">
            Report Summary
        </h2>

        <p class="whitespace-pre-line text-gray-700">
            {{ $hirarc->assessment_objective ?? 'No summary provided.' }}
        </p>
    </div>

    <div class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-3 text-sm">
            Official HIRARC Document
        </h2>

        <iframe
            src="{{ asset('storage/' . $hirarc->uploaded_pdf_path) }}"
            class="w-full h-[85vh] border rounded"
        ></iframe>
    </div>

    <form method="POST"
          action="{{ route('admin.hirarc.uploaded.destroy', $hirarc) }}"
          onsubmit="return confirm('Delete this uploaded HIRARC?')">
        @csrf
        @method('DELETE')

        <button class="bg-red-600 text-white px-3 py-1.5 rounded text-xs">
            Delete Uploaded HIRARC
        </button>
    </form>

</div>

@endsection
