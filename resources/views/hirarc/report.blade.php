{{-- HIRARC report sections --}}

<section>
    <h2>1.0 Introduction</h2>
    <p>{{ $hirarc->introduction ?? '—' }}</p>
</section>

<section>
    <h2>2.0 Objectives</h2>
    <p>{{ $hirarc->objectives ?? '—' }}</p>
</section>

<section>
    <h2>3.0 Process Descriptions</h2>
    <p>{{ $hirarc->process_description ?? '—' }}</p>
    <p><strong>3.1 General description of work activities</strong><br>{{ $hirarc->work_activities ?? '—' }}</p>
    <p><strong>3.2 Work Schedule</strong><br>{{ $hirarc->work_schedule ?? '—' }}</p>
    <p><strong>3.3 Work Force</strong><br>{{ $hirarc->work_force ?? '—' }}</p>
</section>

<section>
    <h2>4.0 Work Unit Description</h2>
    <p class="whitespace-pre-line">{{ $hirarc->work_unit_description ?? '—' }}</p>
</section>

<section>
    <h2>5.0 Hazard Identification</h2>
    <p class="whitespace-pre-line">{{ $hirarc->hazard_identification ?? '—' }}</p>
</section>

<section>
    <h2>6.0 Findings of Assessment (Risk Assessment)</h2>
    <p class="whitespace-pre-line">{{ $hirarc->risk_assessment ?? '—' }}</p>
</section>

<section>
    <h2>7.0 Discussion</h2>
    <p class="whitespace-pre-line">{{ $hirarc->discussion ?? '—' }}</p>
</section>

<section>
    <h2>8.0 Recommendation</h2>
    <p class="whitespace-pre-line">{{ $hirarc->recommendation ?? '—' }}</p>
</section>
