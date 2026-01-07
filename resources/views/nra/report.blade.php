{{-- NRA report sections --}}

<section>
    <h2>1.0 Introduction</h2>
    <p>{{ $nra->introduction ?? '—' }}</p>
</section>

<section>
    <h2>2.0 Objectives</h2>
    <p>{{ $nra->objectives ?? '—' }}</p>
</section>

<section>
    <h2>3.0 Process Descriptions</h2>
    <p>{{ $nra->process_description ?? '—' }}</p>
    <p><strong>3.1 General description of work activities</strong><br>{{ $nra->work_activities ?? '—' }}</p>
    <p><strong>3.2 Work Schedule</strong><br>{{ $nra->work_schedule ?? '—' }}</p>
    <p><strong>3.3 Work Force</strong><br>{{ $nra->work_force ?? '—' }}</p>
</section>

<section>
    <h2>4.0 Work Unit Description</h2>
    <p class="whitespace-pre-line">{{ $nra->work_unit_description ?? '—' }}</p>
</section>

<section>
    <h2>5.0 Methodology and Instrumentation</h2>
    <p><strong>Methodology</strong><br>{{ $nra->methodology ?? '—' }}</p>
    <p><strong>5.1 Instrumentation</strong><br>{{ $nra->instrumentation ?? '—' }}</p>
    <p><strong>5.2 Area Monitoring</strong><br>{{ $nra->area_monitoring ?? '—' }}</p>
    <p><strong>5.3 Noise Mapping</strong><br>{{ $nra->noise_mapping ?? '—' }}</p>
    <p><strong>5.4 Personal Exposure Monitoring</strong><br>{{ $nra->personal_exposure_monitoring ?? '—' }}</p>
</section>

<section>
    <h2>6.0 Findings of Assessment</h2>
    <p><strong>6.1 Results of Area Monitoring</strong><br>{{ $nra->findings_area ?? '—' }}</p>
    <p><strong>6.2 Results of Personal Exposure Monitoring</strong><br>{{ $nra->findings_personal ?? '—' }}</p>
</section>

<section>
    <h2>7.0 Discussion</h2>
    <p class="whitespace-pre-line">{{ $nra->discussion ?? '—' }}</p>
</section>

<section>
    <h2>8.0 Recommendation</h2>
    <p class="whitespace-pre-line">{{ $nra->recommendation ?? '—' }}</p>
</section>
