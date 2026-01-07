@extends('layouts.dashboard')

@section('content')

<div class="max-w-5xl mx-auto space-y-8 text-sm">

    <!-- HEADER -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">NRA</h1>
            <p class="text-gray-500">
                Company: {{ $nra->company_name }} • Status: {{ ucfirst($nra->status) }}
            </p>
        </div>

        <a href="{{ route('nra.index') }}"
           class="border px-4 py-2 rounded hover:bg-gray-100">
            Back
        </a>
    </div>

    <form method="POST" action="{{ route('nra.update', $nra) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">1.0</span>
                <span>Introduction</span>
            </div>
            <textarea name="introduction" class="border rounded px-3 py-2 w-full" rows="2">{{ old('introduction', $nra->introduction) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">2.0</span>
                <span>Objectives</span>
            </div>
            <textarea name="objectives" class="border rounded px-3 py-2 w-full" rows="2">{{ old('objectives', $nra->objectives) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-3">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">3.0</span>
                <span>Process Descriptions</span>
            </div>
            <textarea name="process_description" class="border rounded px-3 py-2 w-full" rows="2">{{ old('process_description', $nra->process_description) }}</textarea>

            <label class="block text-xs text-gray-600">3.1 General description of work activities</label>
            <textarea name="work_activities" class="border rounded px-3 py-2 w-full" rows="2">{{ old('work_activities', $nra->work_activities) }}</textarea>

            <label class="block text-xs text-gray-600">3.2 Work Schedule</label>
            <textarea name="work_schedule" class="border rounded px-3 py-2 w-full" rows="2">{{ old('work_schedule', $nra->work_schedule) }}</textarea>

            <label class="block text-xs text-gray-600">3.3 Work Force</label>
            <textarea name="work_force" class="border rounded px-3 py-2 w-full" rows="2">{{ old('work_force', $nra->work_force) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">4.0</span>
                <span>Work Unit Description</span>
            </div>
            <textarea name="work_unit_description" class="border rounded px-3 py-2 w-full" rows="3">{{ old('work_unit_description', $nra->work_unit_description) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-3">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">5.0</span>
                <span>Methodology and Instrumentation</span>
            </div>
            <label class="block text-xs text-gray-600">Methodology</label>
            <textarea name="methodology" class="border rounded px-3 py-2 w-full" rows="2">{{ old('methodology', $nra->methodology) }}</textarea>

            <label class="block text-xs text-gray-600">5.1 Instrumentation</label>
            <textarea name="instrumentation" class="border rounded px-3 py-2 w-full" rows="2">{{ old('instrumentation', $nra->instrumentation) }}</textarea>

            <label class="block text-xs text-gray-600">5.2 Area Monitoring</label>
            <textarea name="area_monitoring" class="border rounded px-3 py-2 w-full" rows="2">{{ old('area_monitoring', $nra->area_monitoring) }}</textarea>

            <label class="block text-xs text-gray-600">5.3 Noise Mapping</label>
            <textarea name="noise_mapping" class="border rounded px-3 py-2 w-full" rows="2">{{ old('noise_mapping', $nra->noise_mapping) }}</textarea>

            <label class="block text-xs text-gray-600">5.4 Personal Exposure Monitoring</label>
            <textarea name="personal_exposure_monitoring" class="border rounded px-3 py-2 w-full" rows="2">{{ old('personal_exposure_monitoring', $nra->personal_exposure_monitoring) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">6.0</span>
                <span>Findings of Assessment</span>
            </div>
            <label class="block text-xs text-gray-600">6.1 Results of Area Monitoring</label>
            <textarea name="findings_area" class="border rounded px-3 py-2 w-full" rows="3">{{ old('findings_area', $nra->findings_area) }}</textarea>

            <label class="block text-xs text-gray-600">6.2 Results of Personal Exposure Monitoring</label>
            <textarea name="findings_personal" class="border rounded px-3 py-2 w-full" rows="3">{{ old('findings_personal', $nra->findings_personal) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">7.0</span>
                <span>Discussion</span>
            </div>
            <textarea name="discussion" class="border rounded px-3 py-2 w-full" rows="3">{{ old('discussion', $nra->discussion) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">8.0</span>
                <span>Recommendation</span>
            </div>
            <textarea name="recommendation" class="border rounded px-3 py-2 w-full" rows="3">{{ old('recommendation', $nra->recommendation) }}</textarea>
        </section>

        <div class="text-right">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
        </div>
    </form>

    <div class="flex justify-end gap-3">
        <form method="POST" action="{{ route('nra.save-draft', $nra) }}">
            @csrf
            <button class="border px-4 py-2 rounded">Save Draft</button>
        </form>
        <form method="POST" action="{{ route('nra.submit', $nra) }}">
            @csrf
            <button class="bg-green-600 text-white px-4 py-2 rounded">Submit for Approval</button>
        </form>
    </div>
</div>

@endsection
