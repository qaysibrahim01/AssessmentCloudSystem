<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>HIRARC PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 6px; }
        h2 { font-size: 14px; margin: 12px 0 6px; }
        section { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #ccc; padding: 4px; text-align: left; }
    </style>
</head>
<body>
    <h1>HIRARC Report</h1>
    <p><strong>Company:</strong> {{ $hirarc->company_name }}<br>
       <strong>Assessment Date:</strong> {{ optional($hirarc->assessment_date)->format('d M Y') ?? '—' }}</p>

    @include('hirarc.report')
</body>
</html>
