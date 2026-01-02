@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-8 text-sm">

    <!-- HEADER -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">CHRA Review</h1>
            <p class="text-gray-500">
                {{ $chra->company_name }} ·
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

    <!-- =========================
        ADMIN ACTIONS
    ========================== -->
    @if($chra->status === 'pending')
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
