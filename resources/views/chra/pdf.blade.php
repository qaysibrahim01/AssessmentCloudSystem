<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CHRA Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.6;
        }

        @page {
            margin: 25mm 20mm;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            font-size: 10px;
            margin-top: 4px;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            font-size: 10.5px;
        }

        .meta-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        .meta-table .label {
            width: 30%;
            background: #eaeaea;
            font-weight: bold;
        }

        h2 {
            font-size: 12px;
            margin-top: 22px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 16px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background: #f2f2f2;
        }

        .page-break {
            page-break-before: always;
        }

        .signature {
            margin-top: 50px;
            width: 100%;
            font-size: 10px;
        }

        .signature,
        .signature td {
            border: none;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
        }

        .footer:after {
            content: "Page " counter(page) " of " counter(pages);
        }
    </style>
</head>

<body>

<div class="header">
    <h1>Chemical Health Risk Assessment (CHRA)</h1>
    <p>USECHH Regulations 2000</p>
</div>

<table class="meta-table">
    <tr>
        <td class="label">Company</td>
        <td>{{ $chra->company_name }}</td>
    </tr>
    <tr>
        <td class="label">Address</td>
        <td>{{ $chra->company_address }}</td>
    </tr>
    <tr>
        <td class="label">Assessor</td>
        <td>{{ $chra->assessor_name }}</td>
    </tr>
    <tr>
        <td class="label">Assessor Reg. No</td>
        <td>{{ $chra->assessor_registration_no ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Assessment Date</td>
        <td>{{ optional($chra->assessment_date)->format('d M Y') }}</td>
    </tr>
</table>

@include('chra.report')

<div class="page-break"></div>

<table class="signature">
    <tr>
        <td width="50%">
            ___________________________<br>
            Assessor Signature<br>
            Name: {{ $chra->assessor_name }}<br>
            DOSH Reg. No: {{ $chra->assessor_registration_no ?? '-' }}
        </td>
        <td width="50%">
            ___________________________<br>
            Employer / Management
        </td>
    </tr>
</table>

<div class="footer">
    Generated using AssessmentCS · CHRA Module
</div>

</body>
</html>
