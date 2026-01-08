<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Manual - {{ $ticket ? $ticket->ticket_number : 'Template' }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11pt; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20pt; }
        .section { margin: 25px 0; }
        .section h2 { background: #333; color: white; padding: 10px; font-size: 14pt; }
        .section h3 { color: #333; font-size: 12pt; margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { border: 1px solid #000; padding: 8px; }
        table th { background: #e0e0e0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>USER MANUAL</h1>
        @if($ticket)
        <p>{{ $ticket->title }}</p>
        <p><strong>Ticket:</strong> {{ $ticket->ticket_number }}</p>
        @endif
    </div>

    <table>
        <tr><th width="30%">Versi</th><td>1.0</td></tr>
        <tr><th>Tanggal</th><td>{{ now()->format('d F Y') }}</td></tr>
        <tr><th>Dokumen</th><td>UM-{{ $ticket ? $ticket->ticket_number : '' }}</td></tr>
    </table>

    <div class="section">
        <h2>1. Pendahuluan</h2>
        <h3>1.1 Tujuan Dokumen</h3>
        <p>Panduan penggunaan sistem/aplikasi...</p>
        
        <h3>1.2 Ruang Lingkup</h3>
        <p>Mencakup prosedur dan panduan...</p>
    </div>

    <div class="section">
        <h2>2. Memulai</h2>
        <h3>2.1 Persyaratan Sistem</h3>
        <p>Hardware dan software yang dibutuhkan...</p>
        
        <h3>2.2 Instalasi/Akses</h3>
        <p>Cara mengakses sistem...</p>
    </div>

    <div class="section">
        <h2>3. Panduan Penggunaan</h2>
        <h3>3.1 Login</h3>
        <p>Cara login ke sistem...</p>
        
        <h3>3.2 Fitur-fitur Utama</h3>
        <p>Deskripsi dan cara menggunakan fitur...</p>
    </div>

    <div class="section">
        <h2>4. FAQ & Troubleshooting</h2>
        <p>Pertanyaan umum dan solusinya...</p>
    </div>

    <p style="margin-top: 50px; text-align: center; color: #666; font-size: 9pt;">
        Generated on {{ $generatedAt }} | Ticketing System
    </p>
</body>
</html>
