<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CHRA Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.55;
            color: #000;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .header img {
            height: 60px;
            margin-bottom: 6px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header p {
            margin: 4px 0 0;
            font-size: 10px;
        }

        /* ===== META TABLE ===== */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .meta-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        .meta-table td:first-child {
            width: 25%;
            font-weight: bold;
            background: #f5f5f5;
        }

        /* ===== SECTION TITLES ===== */
        h2 {
            font-size: 12.5px;
            margin-top: 22px;
            margin-bottom: 6px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 3px;
        }

        /* ===== TABLES ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            page-break-inside: auto;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #efefef;
            font-weight: bold;
            text-align: left;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* ===== PAGE CONTROL ===== */
        .page-break {
            page-break-before: always;
        }

        /* ===== SIGNATURE ===== */
        .signature {
            margin-top: 45px;
            width: 100%;
        }

        .signature td {
            border: none;
            padding-top: 35px;
            font-size: 10px;
        }

        /* ===== FOOTER ===== */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #444;
        }

        @page {
            margin: 25mm 20mm;
        }

        .footer:after {
            content: "Page " counter(page) " of " counter(pages);
        }



    </style>
</head>

<body>

{{-- ================= HEADER ================= --}}
<div class="header">
    @if($chra->company_logo)
        <img src="{{ public_path('storage/' . $chra->company_logo) }}">
    @endif

    <h1>Chemical Health Risk Assessment (CHRA)</h1>
    <p>USECHH Regulations 2000</p>
</div>

{{-- ================= META ================= --}}
<table class="meta-table">
    <tr>
        <td width="25%"><strong>Company</strong></td>
        <td>{{ $chra->company_name }}</td>
    </tr>
    <tr>
        <td><strong>Address</strong></td>
        <td>{{ $chra->company_address }}</td>
    </tr>
    <tr>
        <td><strong>Assessor</strong></td>
        <td>{{ $chra->assessor_name }}</td>
    </tr>
    <tr>
        <td><strong>Assessor Reg. No</strong></td>
        <td>{{ $chra->assessor_registration_no ?? '—' }}</td>
    </tr>
    <tr>
        <td><strong>Assessment Date</strong></td>
        <td>{{ optional($chra->assessment_date)->format('d M Y') }}</td>
    </tr>
</table>



{{-- ================= SECTION A ================= --}}
<h2>
Section A: Introduction & Objective<br>
<small>(USECHH Reg. 9 & 10)</small>
</h2>

<p>{{ $chra->assessment_objective ?: '-' }}</p>

{{-- ================= SECTION B ================= --}}
<h2>
Section B: Process Description<br>
<small>(USECHH Reg. 9(1)(a))</small>
</h2>


<p><strong>Process Description:</strong><br>
{{ $chra->process_description ?: '-' }}</p>

<p><strong>Work Activities:</strong><br>
{{ $chra->work_activities ?: '-' }}</p>

<p><strong>Chemical Usage Areas:</strong><br>
{{ $chra->chemical_usage_areas ?: '-' }}</p>

{{-- ================= SECTION C ================= --}}
<h2>
Section C: Work Units<br>
<small>(USECHH Reg. 9(1)(b))</small>
</h2>


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
                <td colspan="2" style="text-align:center;">No work units recorded</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- ================= SECTION D ================= --}}
<h2>
Section D: Chemical Register<br>
<small>(USECHH Reg. 7 & 8)</small>
</h2>


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
                <td colspan="2" style="text-align:center;">No chemicals recorded</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- ================= SECTION E ================= --}}
<h2>
Section E: Exposure Assessment & Risk Evaluation<br>
<small>(USECHH Reg. 10)</small>
</h2>


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
                <td colspan="6" style="text-align:center;">No exposure assessment recorded</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- ================= SECTION F ================= --}}
<h2>
Section F: Recommended Control Measures<br>
<small>(USECHH Reg. 11)</small>
</h2>


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
                <td colspan="3" style="text-align:center;">No recommendations recorded</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- ================= SECTION G ================= --}}
<h2>Section G: Assessor Conclusion</h2>
<p>{{ $chra->assessor_conclusion ?: '-' }}</p>



@if($chra->status === 'approved')
    <p style="margin-top:30px;">
        <strong>Approved By:</strong>
        {{ optional($chra->approvedBy)->name ?? 'Admin' }} <br>
        <strong>Date:</strong>
        {{ optional($chra->approved_at)->format('d M Y') }}
    </p>

    <p style="margin-top:15px;">
        <em>This CHRA has been reviewed and approved in accordance with USECHH Regulations 2000.</em>
    </p>
@endif


<table class="signature">
    <tr>
        <td>
            ___________________________<br>
            Assessor Signature<br>
            Name: {{ $chra->assessor_name }}<br>
            DOSH Reg. No: {{ $chra->assessor_registration_no ?? '-' }}<br>
            Date:
        </td>
        <td>
            ___________________________<br>
            Employer / Management<br>
            Date:
        </td>
    </tr>
</table>

@if($chra->status === 'approved')
    <img
        src="{{ public_path('storage/stamps/approved.png') }}"
        class="approval-stamp"
        alt="Approved"
    >
@endif


<div class="footer">
    Generated using AssessmentCS · CHRA Module
</div>

</body>
</html>
