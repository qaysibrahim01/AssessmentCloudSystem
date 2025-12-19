@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-8 text-sm leading-relaxed text-gray-800">

    <!-- REPORT HEADER -->
    <div class="border-b pb-4 flex justify-between items-start">

        <div>
            <h1 class="text-2xl font-bold tracking-wide">
                CHEMICAL HEALTH RISK ASSESSMENT (CHRA)
            </h1>
            <p class="mt-1">
                <strong>Company:</strong> {{ $chra->company_name }}
            </p>
            <p>
                <strong>Assessment Date:</strong> {{ $chra->assessment_date }}
            </p>
        </div>

        <div class="flex gap-2">

            <a href="{{ route('chra.index') }}"
               class="border px-3 py-1.5 rounded text-xs hover:bg-gray-100">
                Back
            </a>

            @if(in_array($chra->status, ['draft', 'rejected']))
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

    <!-- STATUS BOX -->
    <div>
        @if($chra->status === 'draft')
            <div class="bg-yellow-50 border border-yellow-300 p-4 rounded">
                <strong>Status:</strong> Draft  
                <p class="text-xs mt-1">
                    This CHRA has not been submitted for approval and may still be edited.
                </p>
            </div>

        @elseif($chra->status === 'pending')
            <div class="bg-blue-50 border border-blue-300 p-4 rounded">
                <strong>Status:</strong> Submitted for Approval  
                <p class="text-xs mt-1">
                    This CHRA is currently under administrative review. Editing is locked.
                </p>
            </div>

        @elseif($chra->status === 'rejected')
            <div class="bg-red-50 border border-red-300 p-4 rounded">
                <strong>Status:</strong> Rejected  
                <p class="text-xs mt-1">
                    This CHRA was rejected and requires revision.
                </p>
                @if($chra->admin_reason)
                    <p class="text-xs mt-2">
                        <strong>Admin Remarks:</strong> {{ $chra->admin_reason }}
                    </p>
                @endif
            </div>

        @elseif($chra->status === 'approved')
            <div class="bg-green-50 border border-green-300 p-4 rounded">
                <strong>Status:</strong> Approved  
                <p class="text-xs mt-1">
                    This CHRA has been approved and finalized.
                </p>
            </div>
        @endif
    </div>

    <!-- SECTION A -->
    <section class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-2">
            1. Introduction & Objective
        </h2>
        <p class="whitespace-pre-line">
            {{ $chra->assessment_objective ?: '-' }}
        </p>
    </section>

    <!-- SECTION B -->
    <section class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-2">
            2. Process Description
        </h2>

        <p class="whitespace-pre-line mb-3">
            {{ $chra->process_description ?: '-' }}
        </p>

        <p>
            <strong>Work Activities:</strong><br>
            {{ $chra->work_activities ?: '-' }}
        </p>

        <p class="mt-2">
            <strong>Chemical Usage Areas:</strong><br>
            {{ $chra->chemical_usage_areas ?: '-' }}
        </p>
    </section>

    <!-- SECTION C -->
    <section class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-3">
            3. Work Unit Description
        </h2>

        <table class="w-full border text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">Work Unit</th>
                    <th class="border px-2 py-1">Work Area</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chra->workUnits as $unit)
                    <tr>
                        <td class="border px-2 py-1">{{ $unit->name }}</td>
                        <td class="border px-2 py-1">{{ $unit->work_area }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="border px-2 py-2 text-center text-gray-400">
                            No work units recorded
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <!-- SECTION D -->
    <section class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-3">
            4. Chemical Register
        </h2>

        <table class="w-full border text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">Chemical Name</th>
                    <th class="border px-2 py-1">H-Code</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chra->chemicals as $chemical)
                    <tr>
                        <td class="border px-2 py-1">{{ $chemical->chemical_name }}</td>
                        <td class="border px-2 py-1">{{ $chemical->h_code ?? 'NC' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="border px-2 py-2 text-center text-gray-400">
                            No chemicals recorded
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <!-- SECTION E -->
    <section class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-3">
            5. Exposure Assessment & Risk Evaluation
        </h2>

        <table class="w-full border text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-2 py-1">Work Unit</th>
                    <th class="border px-2 py-1">Chemical</th>
                    <th class="border px-2 py-1">Exposure Route</th>
                    <th class="border px-2 py-1 text-center">ER</th>
                    <th class="border px-2 py-1 text-center">HR</th>
                    <th class="border px-2 py-1 text-center">Risk Level</th>
                    <th class="border px-2 py-1 text-center">Action Priority</th>
                </tr>
            </thead>

            <tbody>
                @forelse($chra->exposures as $exp)
                    <tr>
                        <td class="border px-2 py-1">
                            {{ $exp->workUnit->name ?? '-' }}
                        </td>

                        <td class="border px-2 py-1">
                            {{ $exp->chemical->chemical_name ?? '-' }}
                        </td>

                        <td class="border px-2 py-1 capitalize">
                            {{ $exp->exposure_route }}
                        </td>

                        <td class="border px-2 py-1 text-center">
                            {{ $exp->riskEvaluation->exposure_rating ?? '-' }}
                        </td>

                        <td class="border px-2 py-1 text-center">
                            {{ $exp->riskEvaluation->hazard_rating ?? '-' }}
                        </td>

                        <td class="border px-2 py-1 text-center">
                            @php
                                $level = $exp->riskEvaluation->risk_level ?? null;
                            @endphp

                            @if($level === 'high')
                                <span class="text-red-600 font-semibold">High</span>
                            @elseif($level === 'moderate')
                                <span class="text-yellow-600 font-semibold">Moderate</span>
                            @elseif($level === 'low')
                                <span class="text-green-600 font-semibold">Low</span>
                            @else
                                -
                            @endif
                        </td>

                        <td class="border px-2 py-1 text-center font-semibold">
                            {{ $exp->riskEvaluation->action_priority ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7"
                            class="border px-2 py-3 text-center text-gray-400 italic">
                            No exposure assessment recorded
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <p class="text-xs text-gray-500 mt-3">
            ER = Exposure Rating · HR = Hazard Rating
        </p>
    </section>



    <!-- SECTION F -->
    <section class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-3">
            6. Recommended Control Measures
        </h2>

        <ul class="list-disc pl-5 space-y-2">
            @forelse($chra->recommendations as $rec)
                <li>
                    <strong>{{ $rec->action_priority }}</strong>
                    ({{ $rec->category }}) —
                    {{ $rec->recommendation }}
                </li>
            @empty
                <li class="text-gray-400 italic">
                    No recommendations recorded
                </li>
            @endforelse
        </ul>
    </section>

    <!-- SECTION G -->
    <section class="bg-white border rounded p-5">
        <h2 class="font-semibold mb-3">
            7. Assessor Conclusion & Summary
        </h2>

        <p>
            <strong>Overall Risk Profile:</strong>
            {{ $chra->highestRiskLevel() ?? '-' }}
        </p>

        <p>
            <strong>Recommended Action Priority:</strong>
            {{ $chra->recommendedActionPriority() ?? '-' }}
        </p>

        <p class="mt-3 whitespace-pre-line">
            {{ $chra->assessor_conclusion ?: '-' }}
        </p>
    </section>

            <form method="POST"
                action="{{ route('chra.submit', $chra) }}"
                @if(! $chra->isReadyForSubmission())
                    onsubmit="alert('Please complete all mandatory CHRA sections before submission.'); return false;"
                @endif
            >
                @csrf
                <button class="mt-1 bg-green-600 text-white px-3 py-1.5 rounded text-xs hover:bg-green-700"
                        onclick="return confirm('Submit CHRA for approval?')">
                    Submit for Approval
                </button>
            </form>


    <!-- FOOTER -->
    <div class="text-right text-xs text-gray-500 pt-4 border-t">
        End of Chemical Health Risk Assessment Report
    </div>

</div>

@endsection
