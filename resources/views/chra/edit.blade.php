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


    <p class="text-xs text-red-600">
        * Mandatory for submission. Incomplete sections will block approval.
    </p>

    <!-- =========================
        SECTIONS A + B + G
    ========================== -->
    <form method="POST" action="{{ route('chra.update-sections', $chra) }}">
        @csrf

        <!-- SECTION 1.0 INTRODUCTION -->
        <section id="section-a" class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">
                    1.0
                </span>
                <span>Introduction</span>
                <span class="text-red-500">*</span>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Organisation</label>
                    <input type="text"
                           value="{{ $chra->company_name }}"
                           class="border rounded px-3 py-2 w-full bg-gray-100"
                           readonly>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Assessment Date</label>
                    <input type="text"
                           value="{{ optional($chra->assessment_date)->format('d M Y') }}"
                           class="border rounded px-3 py-2 w-full bg-gray-100"
                           readonly>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Assessment Address</label>
                    <input type="text"
                           value="{{ $chra->company_address }}"
                           class="border rounded px-3 py-2 w-full bg-gray-100"
                           readonly>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Business Nature</label>
                    <input type="text"
                           name="business_nature"
                           value="{{ old('business_nature', $chra->business_nature) }}"
                           class="border rounded px-3 py-2 w-full"
                           {{ $chra->isLocked() ? 'readonly' : '' }}>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Assessor</label>
                    <input type="text"
                           value="{{ $chra->assessor_name }}"
                           class="border rounded px-3 py-2 w-full bg-gray-100"
                           readonly>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Assisted By</label>
                    <input type="text"
                           name="assisted_by"
                           value="{{ old('assisted_by', $chra->assisted_by) }}"
                           class="border rounded px-3 py-2 w-full"
                           {{ $chra->isLocked() ? 'readonly' : '' }}>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">DOSH Reg. No.</label>
                    <input type="text"
                        name="assessor_registration_no"
                        value="{{ old('assessor_registration_no', $chra->assessor_registration_no) }}"
                        placeholder="e.g. JKKP HIE 1234"
                        class="border rounded px-3 py-2 w-full"
                        {{ $chra->isLocked() ? 'readonly' : '' }}>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">DOSH Ref. Num.</label>
                    <input type="text"
                           name="dosh_ref_num"
                           value="{{ old('dosh_ref_num', $chra->dosh_ref_num) }}"
                           class="border rounded px-3 py-2 w-full"
                           {{ $chra->isLocked() ? 'readonly' : '' }}>
                </div>
            </div>
        </section>

        <!-- SECTION 2.0 OBJECTIVE -->
        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">
                    2.0
                </span>
                <span>Objective</span>
                <span class="text-red-500">*</span>
            </div>

            <div>
                <label class="block text-xs text-gray-600 mb-1">General Objective</label>
                <textarea name="general_objective"
                          rows="2"
                          class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                          {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('general_objective', $chra->general_objective) }}</textarea>
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs text-gray-600">Specified Objectives (minimum 2, up to 5)</label>
                    <span class="text-[11px] text-gray-500">Leave unused lines blank if not needed.</span>
                </div>
                @php
                    $specified = old('specified_objectives', $chra->specified_objectives ?? []);
                    $specified = is_array($specified) ? $specified : [];
                @endphp
                @for($i = 0; $i < 5; $i++)
                    <input type="text"
                           name="specified_objectives[]"
                           value="{{ $specified[$i] ?? '' }}"
                           class="border rounded px-3 py-2 w-full"
                           placeholder="({{ chr(97 + $i) }})"
                           {{ $chra->isLocked() ? 'readonly' : '' }}>
                @endfor
            </div>
        </section>

        <!-- SECTION 3.0 PROCESS DESCRIPTION -->
        <section id="section-b" class="bg-white p-6 rounded shadow-sm space-y-3">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-4">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">
                    3.0
                </span>
                Process Description
                <span class="text-red-500">*</span>
            </h2>


            <textarea name="process_description"
                      rows="2"
                      class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                      {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('process_description', $chra->process_description) }}</textarea>

            <h3 class="flex items-center gap-3 font-semibold text-lg mb-4">3.1 General description of work activities</h3>

            <textarea name="work_activities"
                      rows="2"
                      class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                      {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('work_activities', $chra->work_activities) }}</textarea>

            <h3 class="flex items-center gap-3 font-semibold text-lg mb-4">3.2 Description of the work activities which involves chemicals</h3>

            <textarea name="chemical_usage_areas"
                      rows="2"
                      class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                      {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('chemical_usage_areas', $chra->chemical_usage_areas) }}</textarea>

            <h3 class="flex items-center gap-3 font-semibold text-lg mb-4">3.3 Description of each work area that involves chemicals</h3>

            <textarea name="assessment_location"
                      rows="2"
                      class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                      {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('assessment_location', $chra->assessment_location) }}</textarea>
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
                    4.0
                </span>
                Work Unit Description
                <span class="text-red-500">*</span>
            </h2>

        @if($chra->canEdit())
        <form method="POST" action="{{ route('chra.workunit', $chra) }}" class="flex flex-wrap gap-2 mb-3">
            @csrf
            <input name="name" placeholder="Work Unit" value="{{ old('name') }}" class="border px-2 py-1 rounded" required>
            <input name="work_area" placeholder="Work Area" value="{{ old('work_area') }}" class="border px-2 py-1 rounded" required>
            <input type="number" name="male_count" min="0" placeholder="Male" value="{{ old('male_count') }}" class="border px-2 py-1 rounded w-24">
            <input type="number" name="female_count" min="0" placeholder="Female" value="{{ old('female_count') }}" class="border px-2 py-1 rounded w-24">
            <input name="main_task" placeholder="Main job task" value="{{ old('main_task') }}" class="border px-2 py-1 rounded flex-1 min-w-[200px]">
            <button class="bg-blue-600 text-white px-4 py-1 rounded">Add</button>
        </form>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left border">Work Unit</th>
                        <th class="px-3 py-2 text-left border">Work Area</th>
                        <th class="px-3 py-2 text-center border">No. of Worker<br>Male</th>
                        <th class="px-3 py-2 text-center border">No. of Worker<br>Female</th>
                        <th class="px-3 py-2 text-left border">Main job task</th>
                        @if($chra->canEdit())
                            <th class="px-3 py-2 text-center border">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($chra->workUnits as $unit)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border">{{ $unit->name }}</td>
                            <td class="px-3 py-2 border">{{ $unit->work_area }}</td>
                            <td class="px-3 py-2 border text-center">{{ $unit->male_count ?? '-' }}</td>
                            <td class="px-3 py-2 border text-center">{{ $unit->female_count ?? '-' }}</td>
                            <td class="px-3 py-2 border">{{ $unit->main_task ?? '-' }}</td>
                            @if($chra->canEdit())
                                <td class="px-3 py-2 border text-center">
                                    <form method="POST" action="{{ route('chra.workunit.delete', $unit) }}">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 text-xs">Remove</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $chra->canEdit() ? 6 : 5 }}" class="px-3 py-4 text-center text-gray-500">
                                No work units recorded.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>


    <!-- =========================
        SECTION D – METHODOLOGY
    ========================== -->
    <form method="POST" action="{{ route('chra.update-sections', $chra) }}">
        @csrf
        <section id="section-methodology" class="bg-white p-6 rounded shadow-sm space-y-4">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-2">
                <span class="bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm">
                    5.0
                </span>
                Methodology
            </h2>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        5.1 Formation of Assessment Team
                    </label>
                    <textarea name="methodology_team" rows="2"
                        class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                        {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('methodology_team', $chra->methodology_team) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        5.2 Determining The Degree of Hazard
                    </label>
                    <textarea name="methodology_degree_hazard" rows="2"
                        class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                        {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('methodology_degree_hazard', $chra->methodology_degree_hazard) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        5.3 Assess Exposure
                    </label>
                    <textarea name="methodology_assess_exposure" rows="2"
                        class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                        {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('methodology_assess_exposure', $chra->methodology_assess_exposure) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        5.4 Adequate of Control Measure
                    </label>
                    <textarea name="methodology_control_adequacy" rows="2"
                        class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                        {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('methodology_control_adequacy', $chra->methodology_control_adequacy) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        5.5 Conclusion of Assessment
                    </label>
                    <textarea name="methodology_conclusion" rows="2"
                        class="border rounded px-3 py-2 w-full leading-relaxed resize-y"
                        {{ $chra->isLocked() ? 'readonly' : '' }}>{{ old('methodology_conclusion', $chra->methodology_conclusion) }}</textarea>
                </div>
            </div>

            @if($chra->canEdit())
                <div class="text-right">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">
                        Save Methodology
                    </button>
                </div>
            @endif
        </section>
    </form>

    <!-- =========================
        SECTION E - FINDINGS OF ASSESSMENT
    ========================== -->
    <section id="section-e" class="bg-white p-6 rounded shadow-sm space-y-8">

        <!-- HEADER -->
        <div class="flex items-center gap-3">
            <span class="bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center font-semibold">
                6.0
            </span>
            <div>
                <h2 class="text-lg font-semibold">
                    Findings of Assessment
                    <span class="text-red-500">*</span>
                </h2>
            </div>
        </div>

        @if($chra->canEdit())
        <!-- INPUT FORM -->
        <form method="POST"
            action="{{ route('chra.chemical', $chra) }}"
            class="space-y-6 border rounded-lg p-5 bg-gray-50">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Work Unit
                    </label>
                    <select name="chra_work_unit_id" class="border rounded px-3 py-2 w-full">
                        <option value="">Select Work Unit</option>
                        @foreach($chra->workUnits as $unit)
                            <option value="{{ $unit->id }}" @selected(old('chra_work_unit_id') == $unit->id)>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Chemical Exposed
                    </label>
                    <input name="chemical_name" class="border rounded px-3 py-2 w-full" placeholder="e.g. AVESTA 308L" value="{{ old('chemical_name') }}" required>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        H-Code
                    </label>
                    <input name="h_code" class="border rounded px-3 py-2 w-full" placeholder="e.g. H351" value="{{ old('h_code') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Health Hazard
                    </label>
                    <textarea name="health_hazard" rows="3" class="border rounded px-3 py-2 w-full" placeholder="Not classified as health hazard">{{ old('health_hazard') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Hazard Rating (Inhalation 1-5)
                    </label>
                    <input type="number" name="hazard_rating" min="1" max="5" class="border rounded px-3 py-2 w-full" placeholder="-" value="{{ old('hazard_rating') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Dermal (Y/N)
                    </label>
                    <input name="route_dermal" class="border rounded px-3 py-2 w-full" placeholder="Y or N" value="{{ old('route_dermal') }}">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Ingestion (Y/N)
                    </label>
                    <input name="route_ingestion" class="border rounded px-3 py-2 w-full" placeholder="Y or N" value="{{ old('route_ingestion') }}">
                </div>
            </div>

            <!-- ACTION -->
            <div class="flex justify-end">
                <button class="bg-blue-600 text-white px-5 py-2 rounded">
                    Add Finding
                </button>
            </div>
        </form>
        @endif

        @php
            $chemicalsByUnit = $chra->chemicals->groupBy(fn($chem) => $chem->workUnit->name ?? 'Unassigned');
        @endphp
        <div class="overflow-x-auto border rounded-lg">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-3 py-2 border text-left w-1/4">Chemical Exposed</th>
                        <th class="px-3 py-2 border text-left w-1/3">Health Hazard</th>
                        <th class="px-3 py-2 border text-center w-20">H-Code</th>
                        <th class="px-3 py-2 border text-center w-28">Hazard Rating<br>(Inhalation 1-5)</th>
                        <th class="px-3 py-2 border text-center w-20">Dermal<br>(Y/N)</th>
                        <th class="px-3 py-2 border text-center w-20">Ingestion<br>(Y/N)</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y">
                    @forelse($chemicalsByUnit as $unitName => $chems)
                        <tr class="bg-gray-50 font-semibold">
                            <td colspan="6" class="px-3 py-2 border italic">
                                Work Unit: {{ $unitName }} (Number of chemicals = {{ $chems->count() }})
                            </td>
                        </tr>
                        @foreach($chems as $chem)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 border">{{ $chem->chemical_name }}</td>
                                <td class="px-3 py-2 border whitespace-pre-line">{{ $chem->health_hazard ?? 'Not classified as health hazard' }}</td>
                                <td class="px-3 py-2 border text-center">{{ $chem->h_code ?? 'NC' }}</td>
                                <td class="px-3 py-2 border text-center">{{ $chem->hazard_rating ?? '-' }}</td>
                                <td class="px-3 py-2 border text-center">{{ $chem->route_dermal ?? '-' }}</td>
                                <td class="px-3 py-2 border text-center">{{ $chem->route_ingestion ?? '-' }}</td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                No chemicals recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </section>


        <!-- SECTION G -->
        <section id="section-g" class="bg-white p-6 rounded shadow-sm space-y-4">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-4">
                <span class="bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm">
                    7.0 
                </span>
                Discussion
                <span class="text-red-500">*</span>
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
                @php $impl = old('implementation_timeframe', $chra->implementation_timeframe); @endphp
                <option value="Immediate" @selected($impl === 'Immediate')>
                    Immediate
                </option>
                <option value="Within 3 months" @selected($impl === 'Within 3 months')>
                    Within 3 months
                </option>
                <option value="Within 6 months" @selected($impl === 'Within 6 months')>
                    Within 6 months
                </option>
                <option value="Ongoing monitoring" @selected($impl === 'Ongoing monitoring')>
                    Ongoing monitoring
                </option>
            </select>
        </section>

        <!-- =========================
        SECTION F – RECOMMENDATIONS
    ========================== -->
    <section id="section-f" class="bg-white p-6 rounded shadow-sm">
            <h2 class="flex items-center gap-3 font-semibold text-lg mb-4">
                <span class="bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-sm">
                    8.0 
                </span>
               Recommendations
                <span class="text-red-500">*</span>
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
                <option value="TC" @selected(old('category') === 'TC')>Technical Control</option>
                <option value="OC" @selected(old('category') === 'OC')>Operational Control</option>
                <option value="PPE" @selected(old('category') === 'PPE')>PPE</option>
                <option value="ERP" @selected(old('category') === 'ERP')>Emergency Response</option>
                <option value="Monitoring" @selected(old('category') === 'Monitoring')>Monitoring</option>
            </select>

            <!-- ACTION PRIORITY -->
            <select name="action_priority"
                    class="border px-2 py-1 rounded"
                    required>
                <option value="">Action Priority</option>
                <option value="AP-1" @selected(old('action_priority') === 'AP-1')>AP-1 (Immediate)</option>
                <option value="AP-2" @selected(old('action_priority') === 'AP-2')>AP-2 (Planned)</option>
                <option value="AP-3" @selected(old('action_priority') === 'AP-3')>AP-3 (Monitoring)</option>
            </select>

            <!-- RECOMMENDATION -->
            <input name="recommendation"
                placeholder="Recommended control measure"
                class="border px-2 py-1 rounded col-span-2"
                value="{{ old('recommendation') }}"
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

    <!-- GLOBAL ACTIONS -->
    <div class="flex gap-3">
        @if($chra->canEdit())
            <form method="POST" action="{{ route('chra.save-draft', $chra) }}">
                @csrf
                <button class="border px-4 py-2 rounded">Save Draft</button>
            </form>

            <form method="POST"
                action="{{ route('chra.submit', $chra) }}">
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
    'textarea[name="general_objective"], \
     textarea[name="process_description"], \
     textarea[name="work_activities"], \
     textarea[name="chemical_usage_areas"], \
     textarea[name="assessment_location"], \
     textarea[name="assessor_conclusion"], \
     input[name="business_nature"], \
     input[name="assisted_by"], \
     input[name="assessor_registration_no"], \
     input[name="dosh_ref_num"], \
     input[name="specified_objectives[]"], \
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
                    general_objective: document.querySelector('[name="general_objective"]')?.value,
                    process_description: document.querySelector('[name="process_description"]')?.value,
                    work_activities: document.querySelector('[name="work_activities"]')?.value,
                    chemical_usage_areas: document.querySelector('[name="chemical_usage_areas"]')?.value,
                    assessment_location: document.querySelector('[name="assessment_location"]')?.value,
                    assessor_conclusion: document.querySelector('[name="assessor_conclusion"]')?.value,
                    implementation_timeframe: document.querySelector('[name="implementation_timeframe"]')?.value,
                    business_nature: document.querySelector('[name="business_nature"]')?.value,
                    assisted_by: document.querySelector('[name="assisted_by"]')?.value,
                    assessor_registration_no: document.querySelector('[name="assessor_registration_no"]')?.value,
                    dosh_ref_num: document.querySelector('[name="dosh_ref_num"]')?.value,
                    specified_objectives: Array.from(document.querySelectorAll('input[name="specified_objectives[]"]'))
                        .map(el => el.value)
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
