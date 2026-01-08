<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Formulir Permintaan - {{ $ticket ? $ticket->ticket_number : 'Template' }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
            font-size: 10pt;
        }
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            margin: 20px 0;
            border: 2px solid #000;
        }
        .form-group {
            margin: 15px 0;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group .value {
            border-bottom: 1px dotted #666;
            min-height: 25px;
            padding: 5px 0;
        }
        .form-group textarea.value {
            min-height: 100px;
            display: block;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table th {
            background: #e0e0e0;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Formulir Permintaan</h1>
        <p>Request Form untuk Update/Pengembangan Sistem Informasi</p>
        @if($ticket)
        <p><strong>Ticket Number:</strong> {{ $ticket->ticket_number }}</p>
        @endif
    </div>

    <div class="info-box">
        <table>
            <tr>
                <th width="30%">Target Deadline</th>
                <td>{{ \Carbon\Carbon::parse($formData['target_deadline'])->format('d M Y') }}</td>
            </tr>
            <tr>
                <th>Project/System Name</th>
                <td>{{ $formData['project_name'] }}</td>
            </tr>
            <tr>
                <th>Ticket Number</th>
                <td>{{ $ticket ? $ticket->ticket_number : '...........................' }}</td>
            </tr>
        </table>
    </div>

    <div class="form-group">
        <label>Requester Name:</label>
        <div class="value">{{ $formData['requester_name'] ?? '' }}</div>
    </div>

    <div class="form-group">
        <label>Department:</label>
        <div class="value">{{ $formData['requester_department'] ?? '' }}</div>
    </div>

    <div class="form-group">
        <label>Request Reason & Business Impact:</label>
        <div class="value textarea">{{ $formData['request_reason'] ?? '' }}</div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p><strong>Pemohon,</strong></p>
            <div class="signature-line">
                ({{ $formData['nama_pemohon'] ?? '................................' }})
            </div>
        </div>
        <div class="signature-box" style="float: right;">
            <p><strong>Menyetujui,</strong></p>
            <div class="signature-line">
                (................................)
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Generated on {{ $generatedAt }} | Ticketing System</p>
    </div>
</body>
</html>
