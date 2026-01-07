{{-- Shared report content for SHOW + PDF --}}
{{-- CONTENT ONLY – NO STYLES --}}

<section>
    <h2>1.0 Introduction</h2>
    <table>
        <tr>
            <th>Organisation</th>
            <td>{{ $chra->company_name }}</td>
        </tr>
        <tr>
            <th>DOSH Reg. No.</th>
            <td>{{ $chra->assessor_registration_no ?? '—' }}</td>
        </tr>
        <tr>
            <th>Assessment address</th>
            <td>{{ $chra->company_address }}</td>
        </tr>
        <tr>
            <th>Business Nature</th>
            <td>{{ $chra->business_nature ?? '—' }}</td>
        </tr>
        <tr>
            <th>Assessor</th>
            <td>{{ $chra->assessor_name }}</td>
        </tr>
        <tr>
            <th>Assisted by</th>
            <td>{{ $chra->assisted_by ?? '—' }}</td>
        </tr>
        <tr>
            <th>DOSH Ref. Num.</th>
            <td>{{ $chra->dosh_ref_num ?? '—' }}</td>
        </tr>
        <tr>
            <th>Assessment date</th>
            <td>{{ optional($chra->assessment_date)->format('d M Y') ?? '—' }}</td>
        </tr>
    </table>
</section>

<section>
    <h2>2.0 Objective</h2>
    <p><strong>General Objective</strong><br>
        {{ $chra->general_objective ?? '—' }}
    </p>
    <p><strong>Specified Objectives</strong></p>
    @php $specified = array_filter($chra->specified_objectives ?? []); @endphp
    @if(count($specified))
        <ol type="a">
            @foreach($specified as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ol>
    @else
        <p>—</p>
    @endif
</section>

<section>
    <h2>3.0 Process Description</h2>
    <p><strong>Overview</strong><br>
        {{ $chra->process_description ?? '—' }}
    </p>

    <p><strong>3.1 General description of work activities</strong><br>
        {{ $chra->work_activities ?? '—' }}
    </p>

    <p><strong>3.2 Description of the work activities which involves chemicals</strong><br>
        {{ $chra->chemical_usage_areas ?? '—' }}
    </p>

    <p><strong>3.3 Description of each work area that involves chemicals</strong><br>
        {{ $chra->assessment_location ?? '—' }}
    </p>
</section>

<section>
    <h2>4.0 Work Unit Description</h2>

    <table>
        <thead>
            <tr>
                <th>Work Unit</th>
                <th>Work Area</th>
                <th>No. of Worker (Male)</th>
                <th>No. of Worker (Female)</th>
                <th>Main job task</th>
            </tr>
        </thead>
        <tbody>
            @forelse($chra->workUnits as $unit)
                <tr>
                    <td>{{ $unit->name }}</td>
                    <td>{{ $unit->work_area }}</td>
                    <td>{{ $unit->male_count ?? '–' }}</td>
                    <td>{{ $unit->female_count ?? '–' }}</td>
                    <td>{{ $unit->main_task ?? '–' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No work units recorded</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>

<section>
    <h2>5.0 Methodology</h2>
    <p><strong>5.1 Formation of Assessment Team</strong><br>
        {{ $chra->methodology_team ?? '—' }}
    </p>
    <p><strong>5.2 Determining The Degree of Hazard</strong><br>
        {{ $chra->methodology_degree_hazard ?? '—' }}
    </p>
    <p><strong>5.3 Assess Exposure</strong><br>
        {{ $chra->methodology_assess_exposure ?? '—' }}
    </p>
    <p><strong>5.4 Adequate of Control Measure</strong><br>
        {{ $chra->methodology_control_adequacy ?? '—' }}
    </p>
    <p><strong>5.5 Conclusion of Assessment</strong><br>
        {{ $chra->methodology_conclusion ?? '—' }}
    </p>
</section>

<section>
    <h2>6.0 Findings of Assessment</h2>
    @php $chemicalsByUnit = $chra->chemicals->groupBy(fn($chem) => $chem->workUnit->name ?? 'Unassigned'); @endphp
    <table>
        <thead>
            <tr>
                <th>Chemical Exposed</th>
                <th>Health Hazard</th>
                <th>H-Code</th>
                <th>Hazard Rating (Inhalation 1-5)</th>
                <th>Dermal (Y/N)</th>
                <th>Ingestion (Y/N)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($chemicalsByUnit as $unitName => $chems)
                <tr>
                    <td colspan="6" style="font-style: italic; font-weight: bold; background: #f9fafb;">
                        Work Unit: {{ $unitName }} (Number of chemicals = {{ $chems->count() }})
                    </td>
                </tr>
                @foreach($chems as $chem)
                    <tr>
                        <td>{{ $chem->chemical_name }}</td>
                        <td style="white-space: pre-line;">{{ $chem->health_hazard ?? 'Not classified as health hazard' }}</td>
                        <td>{{ $chem->h_code ?? 'NC' }}</td>
                        <td class="text-center">{{ $chem->hazard_rating ?? '-' }}</td>
                        <td class="text-center">{{ $chem->route_dermal ?? '-' }}</td>
                        <td class="text-center">{{ $chem->route_ingestion ?? '-' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="6">No chemicals recorded</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>

<section>
    <h2>7.0 Discussion</h2>
    <p>{{ $chra->assessor_conclusion ?? '—' }}</p>
</section>

<section>
    <h2>8.0 Recommendations</h2>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Action Priority</th>
                <th>Recommendation</th>
            </tr>
        </thead>
        <tbody>
            @forelse($chra->recommendations as $rec)
                <tr>
                    <td>{{ $rec->category }}</td>
                    <td>{{ $rec->action_priority }}</td>
                    <td>{{ $rec->recommendation }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No recommendations recorded</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>

<section>
    <h2>Overall Risk Summary</h2>

    <table>
        <tr>
            <th>Overall Risk Profile</th>
            <td>{{ $chra->highestRiskLevel() ?? 'Not assessed' }}</td>
        </tr>
        <tr>
            <th>Recommended Action Priority</th>
            <td>{{ $chra->recommendedActionPriority() ?? 'Not assessed' }}</td>
        </tr>
        <tr>
            <th>Implementation Timeframe</th>
            <td>{{ $chra->implementation_timeframe ?? '—' }}</td>
        </tr>
    </table>
</section>

<section>
    <h2>Assessor Conclusion</h2>
    <p>{{ $chra->assessor_conclusion ?? '-' }}</p>
</section>
