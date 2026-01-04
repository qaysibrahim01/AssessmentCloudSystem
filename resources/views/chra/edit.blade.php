@extends('layouts.dashboard')

@section('content')

<div class="max-w-6xl mx-auto space-y-10 text-sm">

    <!-- HEADER -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">Chemical Health Risk Assessment (CHRA)</h1>
            <p class="text-gray-500">
                Company: {{ $chra->company_name }} · Status: {{ ucfirst($chra->status) }}
            </p>
        </div>

        <a href="{{ route('chra.index') }}"
           class="border px-4 py-2 rounded hover:bg-gray-100">
            ← Back
        </a>
    </div>

    <!-- REJECTION NOTICE -->
    @if($chra->status === 'rejected')
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded">
            <strong>Rejected by Admin</strong>
            <p class="mt-1 text-sm">
                {{ $chra->admin_reason ?? 'Please revise and resubmit.' }}
            </p>
        </div>
    @endif

    @if(session('submit_errors'))
        <div class="bg-red-50 border border-red-300 text-red-800 p-4 rounded">
            <strong class="block mb-2">
                Submission blocked. Please complete the following:
            </strong>

            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach(session('submit_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <!-- =========================
        SECTIONS A + B + G
    ========================== -->
    <form method="POST" action="{{ route('chra.update-sections', $chra) }}">
        @csrf

        <div class="mb-3">
            <label class="block text-xs text-gray-500 mb-1">
                Competent Person Registration No (DOSH)
            </label>

            <input type="text"
                name="assessor_registration_no"
                value="{{ old('assessor_registration_no', $chra->assessor_registration_no) }}"
                placeholder="e.g. JKKP HIE 1234"
                class="border rounded px-3 py-2 w-full"
                {{ $chra->isLocked() ? 'readonly' : '' }}>
        </div>


        <!-- SECTION A -->
        <section id="section-a" class="bg-white p-6 rounded shadow-sm">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-4">
                <span class="bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm">
                    A
                </span>
                Introduction & Objective
            </h2>

            <textarea name="assessment_objective"
                      rows="3"
                      class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                      {{ $chra->isLocked() ? 'readonly' : '' }}>{{ $chra->assessment_objective }}</textarea>
        </section>

        <!-- SECTION B -->
        <section id="section-b" class="bg-white p-6 rounded shadow-sm space-y-3">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-4">
                <span class="bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm">
                    B
                </span>
                Process Description
            </h2>


            <textarea name="process_description"
                      rows="2"
                      class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                      {{ $chra->isLocked() ? 'readonly' : '' }}>{{ $chra->process_description }}</textarea>

            <h3 class="flex items-center gap-3 font-semibold text-lg mb-4">Work Activities</h3>

            <textarea name="work_activities"
                      rows="2"
                      class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                      {{ $chra->isLocked() ? 'readonly' : '' }}>{{ $chra->work_activities }}</textarea>

            <h3 class="flex items-center gap-3 font-semibold text-lg mb-4">Chemical Usage Areas</h3>

            <textarea name="chemical_usage_areas"
                      rows="2"
                      class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                      {{ $chra->isLocked() ? 'readonly' : '' }}>{{ $chra->chemical_usage_areas }}</textarea>
        </section>

        @if($chra->canEdit())
            <div class="text-right">
                <button class="bg-blue-600 text-white px-6 py-2 rounded">
                    Save Sections A, B
                </button>
            </div>
        @endif
    </form>

    <!-- =========================
        SECTION C – WORK UNITS
    ========================== -->
    <section id="section-c" class="bg-white p-6 rounded shadow-sm">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-4">
                <span class="bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm">
                    C
                </span>
                Work Units
            </h2>

        @if($chra->canEdit())
        <form method="POST" action="{{ route('chra.workunit', $chra) }}" class="flex gap-2 mb-3">
            @csrf
            <input name="name" placeholder="Work Unit" class="border px-2 py-1 rounded">
            <input name="work_area" placeholder="Work Area" class="border px-2 py-1 rounded">
            <button class="bg-blue-600 text-white px-4 py-1 rounded">Add</button>
        </form>
        @endif

        <ul class="space-y-1">
            @foreach($chra->workUnits as $unit)
                <li class="flex justify-between items-center">
                    <span>{{ $unit->name }} — {{ $unit->work_area }}</span>
                    @if($chra->canEdit())
                        <form method="POST" action="{{ route('chra.workunit.delete', $unit) }}">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs">Remove</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>

    <!-- =========================
        SECTION D – CHEMICALS
    ========================== -->
    <section id="section-d" class="bg-white p-6 rounded shadow-sm">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-4">
                <span class="bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm">
                    D
                </span>
                Chemical Register
            </h2>

        @if($chra->canEdit())
        <form method="POST" action="{{ route('chra.chemical', $chra) }}" class="flex gap-2 mb-3">
            @csrf
            <input name="chemical_name" placeholder="Chemical Name" class="border px-2 py-1 rounded">
            <input name="h_code" placeholder="H-Code" class="border px-2 py-1 rounded">
            <button class="bg-blue-600 text-white px-4 py-1 rounded">Add</button>
        </form>
        @endif

        <ul class="space-y-1">
            @foreach($chra->chemicals as $chemical)
                <li class="flex justify-between items-center">
                    <span>{{ $chemical->chemical_name }} ({{ $chemical->h_code ?? 'NC' }})</span>
                    @if($chra->canEdit())
                        <form method="POST" action="{{ route('chra.chemical.delete', $chemical) }}">
                            @csrf @method('DELETE')
                            <button class="text-red-600 text-xs">Remove</button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>

    <!-- =========================
        SECTION E – EXPOSURE ASSESSMENT
    ========================== -->
    <section id="section-e" class="bg-white p-6 rounded shadow-sm space-y-8">

        <!-- HEADER -->
        <div class="flex items-center gap-3">
            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-semibold">
                E
            </span>
            <div>
                <h2 class="text-lg font-semibold">Exposure Assessment</h2>
                <p class="text-xs text-gray-500">
                    Assessment of chemical exposure based on work activity, route and frequency (Forms A–D)
                </p>
            </div>
        </div>

        @if($chra->canEdit())
        <!-- INPUT FORM -->
        <form method="POST"
            action="{{ route('chra.exposure.store', $chra) }}"
            class="space-y-6 border rounded-lg p-5 bg-gray-50">
            @csrf

            <!-- GROUP 1 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Work Unit
                    </label>
                    <select name="chra_work_unit_id" class="border rounded px-3 py-2 w-full" required>
                        <option value="">Select Work Unit</option>
                        @foreach($chra->workUnits as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Chemical
                    </label>
                    <select name="chra_chemical_id" class="border rounded px-3 py-2 w-full" required>
                        <option value="">Select Chemical</option>
                        @foreach($chra->chemicals as $chem)
                            <option value="{{ $chem->id }}">{{ $chem->chemical_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Exposure Route
                    </label>
                    <select name="exposure_route" class="border rounded px-3 py-2 w-full" required>
                        <option value="">Select Route</option>
                        <option value="inhalation">Inhalation (Form A)</option>
                        <option value="dermal">Dermal (Form B)</option>
                        <option value="ingestion">Ingestion (Form C)</option>
                    </select>
                </div>
            </div>

            <!-- GROUP 2 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Task Description
                    </label>
                    <input name="task" class="border rounded px-3 py-2 w-full"
                        placeholder="e.g. Mixing, spraying, cleaning">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Exposure Frequency
                    </label>
                    <input name="exposure_frequency" class="border rounded px-3 py-2 w-full"
                        placeholder="e.g. Daily / Weekly">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Exposure Duration
                    </label>
                    <input name="exposure_duration" class="border rounded px-3 py-2 w-full"
                        placeholder="e.g. 2 hours per shift">
                </div>
            </div>

            <!-- GROUP 3 -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Control Adequacy
                    </label>
                    <select name="control_adequacy" class="border rounded px-3 py-2 w-full">
                        <option value="">Select</option>
                        <option value="adequate">Adequate</option>
                        <option value="inadequate">Inadequate</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Exposure Rating
                    </label>
                    <input type="number" name="exposure_rating" min="1" max="5"
                        class="border rounded px-3 py-2 w-full"
                        placeholder="1 (Low) – 5 (High)" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Hazard Rating
                    </label>

                    <input type="number"
                        name="hazard_rating_display"
                        class="border rounded px-3 py-2 w-full bg-gray-100"
                        readonly
                        id="hazard-rating-display"
                        placeholder="Auto-calculated">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Existing Control Measures
                    </label>
                    <textarea name="existing_control" rows="2"
                            class="border rounded px-3 py-2 w-full resize-y"
                            placeholder="Engineering, administrative, PPE"></textarea>
                </div>
            </div>

            <!-- ACTION -->
            <div class="flex justify-end">
                <button class="bg-blue-600 text-white px-5 py-2 rounded">
                    Add Exposure Assessment
                </button>
            </div>
        </form>
        @endif

        <!-- RESULTS TABLE -->
        <p class="text-xs text-gray-500 mb-2">
            Risk Level is calculated based on Hazard Rating × Exposure Rating.
        </p>

        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-3 py-2 border text-left">Work Unit</th>
                        <th class="px-3 py-2 border text-left">Chemical</th>
                        <th class="px-3 py-2 border text-left">Exposure Route</th>
                        <th class="px-3 py-2 border text-center">Exposure Rating</th>
                        <th class="px-3 py-2 border text-center">Hazard Rating</th>
                        <th class="px-3 py-2 border text-center">Risk Level</th>
                        <th class="px-3 py-2 border text-center">Action Priority</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y">
                    @forelse($chra->exposures as $exp)

                        @php
                            $risk = optional($exp->riskEvaluation)->risk_level;
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">
                                {{ $exp->workUnit->name }}
                            </td>

                            <td class="px-3 py-2 border">
                                {{ $exp->chemical->chemical_name }}
                            </td>

                            <td class="px-3 py-2 border capitalize">
                                {{ $exp->exposure_route }}
                            </td>

                            <td class="px-3 py-2 border text-center">
                                {{ optional($exp->riskEvaluation)->exposure_rating ?? '-' }}
                            </td>

                            <td class="px-3 py-2 border text-center">
                                {{ optional($exp->riskEvaluation)->hazard_rating ?? '-' }}
                            </td>

                            <!-- RISK LEVEL -->
                            <td class="px-3 py-2 border text-center">
                                @if($risk === 'high')
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-700">
                                        High
                                    </span>
                                @elseif($risk === 'moderate')
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-yellow-100 text-yellow-700">
                                        Moderate
                                    </span>
                                @elseif($risk === 'low')
                                    <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">
                                        Low
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">–</span>
                                @endif
                            </td>

                            <!-- ACTION PRIORITY -->
                            <td class="px-3 py-2 border text-center font-medium">
                                {{ optional($exp->riskEvaluation)->action_priority ?? '-' }}
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                No exposure assessments recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </section>



    <!-- =========================
        SECTION F – RECOMMENDATIONS
    ========================== -->
    <section id="section-f" class="bg-white p-6 rounded shadow-sm">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-4">
                <span class="bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm">
                    F
                </span>
                Recommendations & Control Measures
            </h2>

        @if($chra->canEdit())
        <form method="POST"
            action="{{ route('chra.recommendation', $chra) }}"
            class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
            @csrf

            <!-- CATEGORY -->
            <select name="category"
                    class="border px-2 py-1 rounded"
                    required>
                <option value="">Category</option>
                <option value="TC">Technical Control</option>
                <option value="OC">Operational Control</option>
                <option value="PPE">PPE</option>
                <option value="ERP">Emergency Response</option>
                <option value="Monitoring">Monitoring</option>
            </select>

            <!-- ACTION PRIORITY -->
            <select name="action_priority"
                    class="border px-2 py-1 rounded"
                    required>
                <option value="">Action Priority</option>
                <option value="AP-1">AP-1 (Immediate)</option>
                <option value="AP-2">AP-2 (Planned)</option>
                <option value="AP-3">AP-3 (Monitoring)</option>
            </select>

            <!-- RECOMMENDATION -->
            <input name="recommendation"
                placeholder="Recommended control measure"
                class="border px-2 py-1 rounded col-span-2"
                required>

            <div class="text-right md:col-span-4">
                <button class="bg-blue-600 text-white px-4 py-1 rounded">
                    Add Recommendation
                </button>
            </div>
        </form>
        @endif

        <!-- LIST -->
        <ul class="space-y-2 text-sm">
            @foreach($chra->recommendations as $rec)
                <li class="flex justify-between items-start border-b pb-2">
                    <div>
                        <span class="font-semibold">
                            {{ $rec->action_priority }}
                        </span>
                        ·
                        <span class="uppercase text-xs">
                            {{ $rec->category }}
                        </span>
                        <div class="text-gray-700">
                            {{ $rec->recommendation }}
                        </div>
                    </div>

                    @if($chra->canEdit())
                        <form method="POST"
                            action="{{ route('chra.recommendation.delete', $rec) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 text-xs">
                                Remove
                            </button>
                        </form>
                    @endif
                </li>
            @endforeach
        </ul>
    </section>

        <!-- SECTION G -->
        <section id="section-g" class="bg-white p-6 rounded shadow-sm space-y-4">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-4">
                <span class="bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm">
                    G
                </span>
                Assessor Conclusion & Summary
            </h2>

            <!-- AUTO SUMMARY -->
            <div class="bg-gray-50 p-4 rounded text-sm">
                <p>
                    <strong>Overall Risk Profile:</strong>
                    {{ $chra->highestRiskLevel() ?? 'Not assessed yet' }}
                </p>
                <p>
                    <strong>Recommended Action Priority:</strong>
                    {{ $chra->recommendedActionPriority() ?? 'Not assessed yet' }}
                </p>
            </div>

            <!-- MANUAL ASSESSOR CONCLUSION -->
            <textarea name="assessor_conclusion"
                rows="3"
                placeholder="Assessor’s professional judgement and justification"
                class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('assessor_conclusion', $chra->assessor_conclusion) }}</textarea>


            <select name="implementation_timeframe"
                    class="border rounded px-3 py-2 w-full"
                    {{ $chra->isLocked() ? 'disabled' : '' }}>
                <option value="">Implementation Timeframe</option>
                <option value="Immediate" @selected($chra->implementation_timeframe === 'Immediate')>
                    Immediate
                </option>
                <option value="Within 3 months" @selected($chra->implementation_timeframe === 'Within 3 months')>
                    Within 3 months
                </option>
                <option value="Within 6 months" @selected($chra->implementation_timeframe === 'Within 6 months')>
                    Within 6 months
                </option>
                <option value="Ongoing monitoring" @selected($chra->implementation_timeframe === 'Ongoing monitoring')>
                    Ongoing monitoring
                </option>
            </select>
        </section>

    <!-- GLOBAL ACTIONS -->
    <div class="flex gap-3">
        @if($chra->canEdit())
            <form method="POST" action="{{ route('chra.save-draft', $chra) }}">
                @csrf
                <button class="border px-4 py-2 rounded">Save Draft</button>
            </form>

            <form method="POST"
                action="{{ route('chra.submit', $chra) }}"
                @if(! $chra->isReadyForSubmission())
                    onsubmit="alert('Please complete all mandatory CHRA sections before submission.'); return false;"
                @endif
            >
                @csrf
                <button class="bg-green-600 text-white px-4 py-2 rounded"
                        onclick="return confirm('Submit CHRA for approval?')">
                    Submit for Approval
                </button>
            </form>
        @else
            <span class="text-gray-500">
                CHRA is locked (submitted or approved)
            </span>
        @endif
    </div>

</div>


<script>
let autoSaveTimer = null;

document.querySelectorAll(
    'textarea[name="assessment_objective"], \
     textarea[name="process_description"], \
     textarea[name="work_activities"], \
     textarea[name="chemical_usage_areas"], \
     textarea[name="assessor_conclusion"], \
     select[name="implementation_timeframe"]'
).forEach(el => {
    el.addEventListener('input', () => {
        clearTimeout(autoSaveTimer);

        autoSaveTimer = setTimeout(() => {
            fetch("{{ route('chra.autosave', $chra) }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    assessment_objective: document.querySelector('[name="assessment_objective"]')?.value,
                    process_description: document.querySelector('[name="process_description"]')?.value,
                    work_activities: document.querySelector('[name="work_activities"]')?.value,
                    chemical_usage_areas: document.querySelector('[name="chemical_usage_areas"]')?.value,
                    assessor_conclusion: document.querySelector('[name="assessor_conclusion"]')?.value,
                    implementation_timeframe: document.querySelector('[name="implementation_timeframe"]')?.value
                })
            });
        }, 1500);
    });
});


const chemicalHazards = @json(
    $chra->chemicals->mapWithKeys(fn($c) => [
        $c->id => $c->derived_hazard_rating ?? 1
    ])
);

const chemicalSelect = document.querySelector('[name="chra_chemical_id"]');
const hazardInput = document.getElementById('hazard-rating-display');

if (chemicalSelect && hazardInput) {
    chemicalSelect.addEventListener('change', function () {
        hazardInput.value = chemicalHazards[this.value] ?? '';
    });
}
</script>


@endsection
