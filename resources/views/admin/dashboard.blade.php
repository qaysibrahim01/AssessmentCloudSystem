@extends('layouts.dashboard')

@section('content')

<h1 class="text-2xl font-semibold mb-6">Admin Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <a href="{{ route('admin.chra.index') }}"
       class="group bg-white rounded-xl border p-6 hover:border-blue-500">

        <h3 class="text-lg font-semibold group-hover:text-blue-600">
            CHRA Reviews
        </h3>

        <p class="text-sm text-gray-500 mt-1">
            Review submitted CHRA reports
        </p>

        <div class="mt-4 text-sm text-blue-600 opacity-0 group-hover:opacity-100">
            Open →
        </div>
    </a>

</div>

@endsection
