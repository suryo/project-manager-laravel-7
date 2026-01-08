<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Spesifikasi Fungsional, Teknis dan Assets - {{ $ticket ? $ticket->ticket_number : 'Template' }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11pt; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; }
        .section { margin: 20px 0; }
        .section h2 { background: #333; color: white; padding: 10px; font-size: 14pt; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        table th, table td { border: 1px solid #000; padding: 8px; }
        table th { background: #e0e0e0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Spesifikasi Fungsional, Teknis dan Assets</h1>
        @if($ticket)
        <p><strong>Ticket:</strong> {{ $ticket->ticket_number }} - {{ $ticket->title }}</p>
        @endif
    </div>

    <table>
        <tr><th width="30%">Tanggal Dokumen</th><td>{{ now()->format('d F Y') }}</td></tr>
        <tr><th>Nomor Dokumen</th><td>STA-{{ $ticket ? $ticket->ticket_number : '........' }}</td></tr>
    </table>

    <div class="section">
        <h2>1. Spesifikasi Fungsional</h2>
        <p>Detail fitur dan fungsi yang akan dikembangkan...</p>
    </div>

    <div class="section">
        <h2>2. Spesifikasi Teknis</h2>
        <p>Teknologi, arsitektur, dan spesifikasi teknis...</p>
    </div>

    <div class="section">
        <h2>3. Assets</h2>
        <p>Daftar assets (gambar, mockup, wireframe, dll)</p>
    </div>

    <p style="margin-top: 50px; text-align: center; color: #666; font-size: 9pt;">
        Generated on {{ $generatedAt }} | Ticketing System
    </p>
</body>
</html>
