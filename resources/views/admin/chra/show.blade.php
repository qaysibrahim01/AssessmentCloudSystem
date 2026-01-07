@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-8 text-sm">

    <!-- HEADER -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">CHRA Review</h1>
            <p class="text-gray-500">
                {{ $chra->company_name }} •
                Status: <span class="capitalize">{{ $chra->status }}</span>
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.chra.index') }}"
               class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">
                Back
            </a>

            @if($chra->status === 'approved')
                <a href="{{ route('chra.download', $chra) }}"
                   class="bg-green-600 text-white px-3 py-1.5 rounded text-xs">
                    Download PDF
                </a>
            @endif
        </div>
    </div>

    @if($chra->hasPendingDeleteRequest())
        <div class="bg-orange-50 border border-orange-300 p-3 rounded text-xs text-orange-700">
            A delete request has been submitted. Editing is disabled until the request is reviewed by admin.
        </div>
    @endif

    <div class="bg-white border rounded-lg p-8 space-y-8">
        <style>
            section {
                padding-bottom: 1.5rem;
                border-bottom: 1px solid #e5e7eb;
            }

            section:last-child {
                border-bottom: none;
            }

            section h2 {
                font-size: 1rem;
                font-weight: 600;
                margin-bottom: 0.5rem;
            }

            section p {
                font-size: 0.875rem;
                line-height: 1.6;
                color: #374151;
            }

            section table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.875rem;
                margin-top: 0.75rem;
            }

            section th {
                background: #f9fafb;
                font-weight: 600;
                text-align: left;
                padding: 0.5rem;
                border: 1px solid #e5e7eb;
            }

            section td {
                padding: 0.5rem;
                border: 1px solid #e5e7eb;
            }
        </style>

        @include('chra.report')
    </div>

    @if($mode === 'delete' && $deleteRequest)
        <div class="bg-yellow-50 border border-yellow-300 p-4 rounded mt-6">
            <h3 class="font-semibold mb-2 text-sm">
                Delete Request Review
            </h3>

            <p class="text-xs mb-3">
                <strong>Requested by:</strong>
                {{ $deleteRequest->requester->name }}<br>

                <strong>Reason:</strong>
                {{ $deleteRequest->reason }}
            </p>

            <div class="flex gap-3">
                <form method="POST"
                      action="{{ route('admin.chra.delete.approve', $deleteRequest) }}">
                    @csrf
                    <button class="bg-red-600 text-white px-4 py-2 rounded">
                        Approve Delete
                    </button>
                </form>

                <form method="POST"
                      action="{{ route('admin.chra.delete.reject', $deleteRequest) }}">
                    @csrf
                    <input name="admin_remark"
                           placeholder="Reason for keeping CHRA"
                           class="border px-3 py-2 rounded mr-2"
                           required>

                    <button class="bg-green-600 text-white px-4 py-2 rounded">
                        Keep CHRA
                    </button>
                </form>
            </div>
        </div>
    @endif


    <!-- =========================
        ADMIN ACTIONS
    ========================== -->
    @if($mode === 'normal' && $chra->status === 'pending' && ! $deleteRequest)
        <div class="flex gap-3 pt-4">
            <form method="POST" action="{{ route('admin.chra.approve', $chra) }}">
                @csrf
                <button class="bg-green-600 text-white px-4 py-2 rounded">
                    Approve
                </button>
            </form>

            <form method="POST" action="{{ route('admin.chra.reject', $chra) }}">
                @csrf
                <input name="reason"
                       placeholder="Rejection reason"
                       class="border px-3 py-2 rounded mr-2"
                       required>

                <button class="bg-red-600 text-white px-4 py-2 rounded">
                    Reject
                </button>
            </form>
        </div>
    @endif
</div>

@endsection
