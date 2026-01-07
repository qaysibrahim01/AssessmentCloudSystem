@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-8 text-sm text-gray-800">

    <!-- HEADER -->
    <div class="border-b pb-4 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">
                Chemical Health Risk Assessment (CHRA)
            </h1>
            <p class="text-gray-500 mt-1">
                Company: {{ $chra->company_name }} •
                Assessment Date: {{ optional($chra->assessment_date)->format('d M Y') ?? '—' }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('chra.index') }}"
               class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">
                Back
            </a>

            @if($chra->canEdit())
                <a href="{{ route('chra.edit', $chra) }}"
                class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs">
                    Edit
                </a>
            @endif

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
    <!-- STATUS -->
    <div>
        @if($chra->status === 'draft')
            <div class="bg-yellow-50 border border-yellow-300 p-4 rounded">
                <strong>Status:</strong> Draft
            </div>
        @elseif($chra->status === 'pending')
            <div class="bg-blue-50 border border-blue-300 p-4 rounded">
                <strong>Status:</strong> Pending Approval
            </div>
        @elseif($chra->status === 'rejected')
            <div class="bg-red-50 border border-red-300 p-4 rounded">
                <strong>Status:</strong> Rejected
                @if($chra->admin_reason)
                    <p class="mt-2 text-xs">
                        <strong>Admin Remarks:</strong> {{ $chra->admin_reason }}
                    </p>
                @endif
            </div>
        @else
            <div class="bg-green-50 border border-green-300 p-4 rounded">
                <strong>Status:</strong> Approved
            </div>
        @endif
    </div>

    <!-- REPORT -->
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


    <!-- ACTION -->
    @if($chra->canEdit())
        <p class="text-xs text-red-600 text-right mb-1">
            * Mandatory CHRA sections must be completed before submission.
        </p>
        <form method="POST"
              action="{{ route('chra.submit', $chra) }}"
              class="text-right"
              @if(! $chra->isReadyForSubmission())
                  onsubmit="alert('Please complete all mandatory CHRA sections before submission.'); return false;"
              @endif>
            @csrf
            <button class="bg-green-600 text-white px-4 py-2 rounded text-sm"
                    onclick="return confirm('Submit CHRA for approval?')">
                Submit for Approval
            </button>
        </form>
    @endif

{{-- ================= TIMELINE ================= --}}
<div class="bg-white border rounded-lg p-6 space-y-4">
    <h2 class="text-sm font-semibold text-gray-700">
        CHRA Activity Timeline
    </h2>

    <ol class="border-l border-gray-300 pl-4 space-y-4 text-xs">
        @foreach($chra->timeline() as $event)
            <li class="relative">
                <span class="absolute -left-[7px] top-1.5 w-3 h-3 rounded-full
                    {{ str_contains($event['type'], 'approved') ? 'bg-green-500' :
                       (str_contains($event['type'], 'rejected') ? 'bg-red-500' :
                       'bg-blue-500') }}">
                </span>

                <p class="font-medium">
                    {{ $event['label'] }}
                </p>

                <p class="text-gray-500">
                    {{ $event['date_formatted'] }}
                    Â· {{ $event['by'] }}
                </p>

                @if(!empty($event['note']))
                    <p class="text-gray-600 italic mt-1">
                        â€œ{{ $event['note'] }}â€
                    </p>
                @endif
            </li>
        @endforeach
    </ol>
</div>


<!-- FOOTER -->
    <div class="text-right text-xs text-gray-400 border-t pt-4">
        End of Chemical Health Risk Assessment Report
    </div>

</div>

@endsection


