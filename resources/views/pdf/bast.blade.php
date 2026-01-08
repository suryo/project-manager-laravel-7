<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BAST - {{ $ticket ? $ticket->ticket_number : 'Template' }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11pt; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { border: 1px solid #000; padding: 8px; }
        .signatures { margin-top: 50px; }
        .signature-box { display: inline-block; width: 45%; text-align: center; vertical-align: top; }
        .signature-line { border-top: 1px solid #000; margin-top: 80px; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>BERITA ACARA SERAH TERIMA</h1>
        <p>{{ $ticket ? $ticket->ticket_number : '' }}</p>
    </div>

    <p>Pada hari ini, tanggal <strong>..........</strong>, telah dilakukan serah terima sistem/aplikasi:</p>

    <table>
        <tr><th width="30%">Nama Sistem</th><td>{{ $ticket ? $ticket->title : '................................' }}</td></tr>
        <tr><th>Ticket Number</th><td>{{ $ticket ? $ticket->ticket_number : '................................' }}</td></tr>
        <tr><th>Tanggal Serah Terima</th><td>................................</td></tr>
    </table>

    <h3>Pihak Pertama (Pengembang)</h3>
    <table>
        <tr><th width="30%">Nama</th><td>{{ $ticket && $ticket->assignee ? $ticket->assignee->name : '................................' }}</td></tr>
        <tr><th>Jabatan</th><td>Developer</td></tr>
    </table>

    <h3>Pihak Kedua (Penerima)</h3>
    <table>
        <tr><th width="30%">Nama</th><td>{{ $ticket ? $ticket->requester->name : '................................' }}</td></tr>
        <tr><th>Unit</th><td>................................</td></tr>
    </table>

    <h3>Detail Serah Terima</h3>
    <p>Sistem/aplikasi yang diserahkan telah melalui tahapan:</p>
    <ol>
        <li>Analisis Kebutuhan</li>
        <li>Perencanaan</li>
        <li>Pengembangan</li>
        <li>Pengujian (UAT)</li>
        <li>Dokumentasi</li>
    </ol>

    <div class="signatures">
        <div class="signature-box">
            <p><strong>Pihak Pertama</strong></p>
            <div class="signature-line">(................................)</div>
        </div>
        <div class="signature-box" style="float: right;">
            <p><strong>Pihak Kedua</strong></p>
            <div class="signature-line">(................................)</div>
        </div>
    </div>
</body>
</html>
