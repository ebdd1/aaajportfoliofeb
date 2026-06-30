<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - Portfolio</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f5f0e8 0%, #e8dfd4 100%);
            color: #5c4a3d;
        }
        .container {
            text-align: center;
            padding: 2rem;
        }
        .icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #8b5e3c;
        }
        p {
            font-size: 1.1rem;
            max-width: 400px;
            line-height: 1.6;
            color: #7a6a5a;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🔧</div>
        <h1>Sedang Maintenance</h1>
        <p>{{ $message ?? 'Portfolio sedang dalam perawatan. Silakan kembali dalam beberapa saat.' }}</p>
    </div>
</body>
</html>
