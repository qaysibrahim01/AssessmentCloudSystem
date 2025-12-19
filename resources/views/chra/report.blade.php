{{-- This file is shared by show.blade AND pdf.blade --}}

<h2>Section A: Introduction & Objective</h2>
<p>{{ $chra->assessment_objective ?: '-' }}</p>

<h2>Section B: Process Description</h2>

<p><strong>Process Description:</strong><br>
{{ $chra->process_description ?: '-' }}</p>

<p><strong>Work Activities:</strong><br>
{{ $chra->work_activities ?: '-' }}</p>

<p><strong>Chemical Usage Areas:</strong><br>
{{ $chra->chemical_usage_areas ?: '-' }}</p>

<h2>Section C: Work Units</h2>
<table>
    <thead>
        <tr>
            <th>Work Unit</th>
            <th>Work Area</th>
        </tr>
    </thead>
    <tbody>
        @forelse($chra->workUnits as $unit)
            <tr>
                <td>{{ $unit->name }}</td>
                <td>{{ $unit->work_area }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2">No work units recorded</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h2>Section D: Chemical Register</h2>
<table>
    <thead>
        <tr>
            <th>Chemical Name</th>
            <th>H-Code</th>
        </tr>
    </thead>
    <tbody>
        @forelse($chra->chemicals as $chemical)
            <tr>
                <td>{{ $chemical->chemical_name }}</td>
                <td>{{ $chemical->h_code ?? 'NC' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2">No chemicals recorded</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h2>Section E: Exposure Assessment & Risk Evaluation</h2>
<table>
    <thead>
        <tr>
            <th>Work Unit</th>
            <th>Chemical</th>
            <th>ER</th>
            <th>HR</th>
            <th>Risk Level</th>
            <th>Action Priority</th>
        </tr>
    </thead>
    <tbody>
        @forelse($chra->exposures as $exp)
            @if($exp->riskEvaluation)
                <tr>
                    <td>{{ $exp->workUnit->name ?? '-' }}</td>
                    <td>{{ $exp->chemical->chemical_name ?? '-' }}</td>
                    <td>{{ $exp->riskEvaluation->exposure_rating }}</td>
                    <td>{{ $exp->riskEvaluation->hazard_rating }}</td>
                    <td>{{ strtoupper($exp->riskEvaluation->risk_level) }}</td>
                    <td>{{ $exp->riskEvaluation->action_priority }}</td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="6">No exposure assessment recorded</td>
            </tr>
        @endforelse
    </tbody>
</table>

<h2>Section F: Recommended Control Measures</h2>
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

<h2>Section G: Assessor Conclusion</h2>
<p>{{ $chra->assessor_conclusion ?: '-' }}</p>
