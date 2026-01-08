<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Requirements Document - {{ $ticket ? $ticket->ticket_number : 'Template' }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11pt; line-height: 1.6; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
        .section { margin: 20px 0; page-break-inside: avoid; }
        .section h2 { background: #333; color: white; padding: 10px; font-size: 14pt; margin: 20px 0 10px 0; }
        .section .content { border: 1px solid #ccc; padding: 15px; min-height: 80px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table th, table td { border: 1px solid #000; padding: 8px; }
        table th { background: #e0e0e0; }
        .footer { position: absolute; bottom: 20px; text-align: center; font-size: 9pt; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>User Requirements Document (URD)</h1>
        <p>Dokumen Kebutuhan Pengguna</p>
        @if($ticket)
        <p><strong>Ticket:</strong> {{ $ticket->ticket_number }} - {{ $ticket->title }}</p>
        @endif
    </div>

    <table>
        <tr>
            <th width="30%">Tanggal Dokumen</th>
            <td>{{ now()->format('d F Y') }}</td>
        </tr>
        <tr>
            <th>Nomor Dokumen</th>
            <td>URD-{{ $ticket ? $ticket->ticket_number : '........' }}</td>
        </tr>
        <tr>
            <th>Pemohon</th>
            <td>{{ $ticket ? $ticket->requester->name : '................................' }}</td>
        </tr>
    </table>

    <div class="section">
        <h2>1. Kebutuhan Fungsional</h2>
        <div class="content">{!! nl2br(e($formData['functional_requirements'] ?? '-')) !!}</div>
    </div>

    <div class="section">
        <h2>2. Kebutuhan Non-Fungsional</h2>
        <div class="content">{!! nl2br(e($formData['non_functional_requirements'] ?? '-')) !!}</div>
    </div>

    <div class="section">
        <h2>3. User Stories</h2>
        <div class="content">{!! nl2br(e($formData['user_stories'] ?? '-')) !!}</div>
    </div>

    <div class="section">
        <h2>4. Kriteria Penerimaan</h2>
        <div class="content">{!! nl2br(e($formData['acceptance_criteria'] ?? '-')) !!}</div>
    </div>

    <div class="footer">
        <p>Generated on {{ $generatedAt }} | Ticketing System</p>
    </div>
</body>
</html>
