@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded shadow max-w-md w-full text-center">
        <h1 class="text-2xl font-semibold text-gray-800 mb-3">
            Session Expired
        </h1>

        <p class="text-gray-600 mb-6">
            Your session has expired, possibly due to system maintenance or inactivity.
            Please log in again to continue.
        </p>

        <a href="{{ route('welcome.role') }}"
           class="inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Go to Login
        </a>
    </div>
</div>
@endsection
