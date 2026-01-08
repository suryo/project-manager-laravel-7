<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .error-icon {
            font-size: 120px;
            color: #dc3545;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        .error-code {
            font-size: 5rem;
            font-weight: 900;
            color: #667eea;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        .error-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #718096;
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .contact-admin {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 1rem 1.5rem;
            margin-top: 2rem;
            border-radius: 8px;
            text-align: left;
        }
        .contact-admin strong {
            color: #2d3748;
            display: block;
            margin-bottom: 0.5rem;
        }
        .contact-admin p {
            color: #718096;
            margin: 0;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-shield-x"></i>
        </div>
        
        <div class="error-code">403</div>
        
        <h1 class="error-title">Akses Ditolak</h1>
        
        <p class="error-message">
            Maaf, Anda tidak memiliki wewenang untuk mengakses halaman ini. 
            Silakan hubungi administrator untuk mendapatkan izin akses.
        </p>
        
        <a href="{{ url('/') }}" class="btn-home">
            <i class="bi bi-house-door me-2"></i>Kembali ke Beranda
        </a>
        
        <div class="contact-admin">
            <strong><i class="bi bi-info-circle me-2"></i>Informasi</strong>
            <p>
                Jika Anda merasa ini adalah kesalahan, silakan hubungi administrator sistem 
                atau tim IT untuk bantuan lebih lanjut.
            </p>
        </div>
    </div>
</body>
</html>
