@extends('layouts.dashboard')

@section('content')

<div class="max-w-5xl mx-auto space-y-8 text-sm">

    <!-- HEADER -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-semibold">HIRARC</h1>
            <p class="text-gray-500">
                Company: {{ $hirarc->company_name }} • Status: {{ ucfirst($hirarc->status) }}
            </p>
        </div>

        <a href="{{ route('hirarc.index') }}"
           class="border px-4 py-2 rounded hover:bg-gray-100">
            Back
        </a>
    </div>

    <form method="POST" action="{{ route('hirarc.update', $hirarc) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">1.0</span>
                <span>Introduction</span>
            </div>
            <textarea name="introduction" class="border rounded px-3 py-2 w-full" rows="2">{{ old('introduction', $hirarc->introduction) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">2.0</span>
                <span>Objectives</span>
            </div>
            <textarea name="objectives" class="border rounded px-3 py-2 w-full" rows="2">{{ old('objectives', $hirarc->objectives) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-3">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">3.0</span>
                <span>Process Descriptions</span>
            </div>
            <textarea name="process_description" class="border rounded px-3 py-2 w-full" rows="2">{{ old('process_description', $hirarc->process_description) }}</textarea>

            <label class="block text-xs text-gray-600">3.1 General description of work activities</label>
            <textarea name="work_activities" class="border rounded px-3 py-2 w-full" rows="2">{{ old('work_activities', $hirarc->work_activities) }}</textarea>

            <label class="block text-xs text-gray-600">3.2 Work Schedule</label>
            <textarea name="work_schedule" class="border rounded px-3 py-2 w-full" rows="2">{{ old('work_schedule', $hirarc->work_schedule) }}</textarea>

            <label class="block text-xs text-gray-600">3.3 Work Force</label>
            <textarea name="work_force" class="border rounded px-3 py-2 w-full" rows="2">{{ old('work_force', $hirarc->work_force) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">4.0</span>
                <span>Work Unit Description</span>
            </div>
            <textarea name="work_unit_description" class="border rounded px-3 py-2 w-full" rows="3">{{ old('work_unit_description', $hirarc->work_unit_description) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">5.0</span>
                <span>Hazard Identification</span>
            </div>
            <div class="space-y-2 text-xs text-gray-600">
                <div>Work Activity</div>
                <textarea name="hazard_identification" class="border rounded px-3 py-2 w-full" rows="6" placeholder="Work Activity&#10;Hazard&#10;Hazard Category (Physical, Chemical, Biological, Ergonomic, Psychosocial)&#10;Incident and Consequences (Effect)&#10;Existing Risk Controls">{{ old('hazard_identification', $hirarc->hazard_identification) }}</textarea>
            </div>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">6.0</span>
                <span>Findings of Assessment (Risk Assessment)</span>
            </div>
            <textarea name="risk_assessment" class="border rounded px-3 py-2 w-full" rows="6" placeholder="Risk Assessment&#10;Likelihood Justification&#10;Likelihood (L)&#10;Severity (S)&#10;Risk Matrix Number (RMN)">{{ old('risk_assessment', $hirarc->risk_assessment) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">7.0</span>
                <span>Discussion</span>
            </div>
            <textarea name="discussion" class="border rounded px-3 py-2 w-full" rows="3">{{ old('discussion', $hirarc->discussion) }}</textarea>
        </section>

        <section class="bg-white p-6 rounded shadow-sm space-y-4">
            <div class="flex items-center gap-3 font-semibold text-lg">
                <span class="bg-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center text-sm">8.0</span>
                <span>Recommendation</span>
            </div>
            <textarea name="recommendation" class="border rounded px-3 py-2 w-full" rows="3">{{ old('recommendation', $hirarc->recommendation) }}</textarea>
        </section>

        <div class="text-right">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
        </div>
    </form>

    <div class="flex justify-end gap-3">
        <form method="POST" action="{{ route('hirarc.save-draft', $hirarc) }}">
            @csrf
            <button class="border px-4 py-2 rounded">Save Draft</button>
        </form>
        <form method="POST" action="{{ route('hirarc.submit', $hirarc) }}">
            @csrf
            <button class="bg-green-600 text-white px-4 py-2 rounded">Submit for Approval</button>
        </form>
    </div>
</div>

@endsection
