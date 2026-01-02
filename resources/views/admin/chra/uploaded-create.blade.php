@extends('layouts.dashboard')

@section('content')

<div class="max-w-3xl mx-auto space-y-6 text-sm">

    <h1 class="text-2xl font-semibold">
        Upload Official CHRA Report
    </h1>

    <form method="POST"
          action="{{ route('admin.chra.uploaded.store') }}"
          enctype="multipart/form-data"
          class="bg-white p-6 rounded shadow space-y-4">
        @csrf

        <div>
            <label class="font-medium">Company Name</label>
            <input name="company_name" class="border w-full px-3 py-2 rounded" required>
        </div>

        <div>
            <label class="font-medium">Company Address</label>
            <textarea name="company_address" class="border w-full px-3 py-2 rounded" required></textarea>
        </div>

        <div>
            <label class="font-medium">Assessment Date</label>
            <input type="date" name="assessment_date" class="border w-full px-3 py-2 rounded" required>
        </div>

        <div>
            <label class="font-medium">Report Summary</label>
            <textarea name="summary" class="border w-full px-3 py-2 rounded" required></textarea>
        </div>

        <div>
            <label class="font-medium">Upload PDF</label>
            <input type="file" name="pdf" accept="application/pdf" required>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('admin.chra.index') }}"
               class="border px-4 py-2 rounded">
                Cancel
            </a>
            <button class="bg-blue-600 text-white px-6 py-2 rounded">
                Upload Report
            </button>
        </div>

    </form>
</div>

@endsection
