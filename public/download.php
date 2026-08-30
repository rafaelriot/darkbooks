<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descargar DarkBooks App - Contabilidad para Dark Kitchens</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --dark-blue: #1E3A8A;
            --navy-bg: #0F172A;
            --surface-card: #1E293B;
            --emerald-green: #10B981;
            --vibrant-orange: #F97316;
            --light-gray: #F3F4F6;
            --subtext-gray: #9CA3AF;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--navy-bg);
            color: var(--light-gray);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: var(--surface-card);
            border-radius: 24px;
            padding: 32px 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(59, 130, 246, 0.2);
            text-align: center;
        }

        .logo-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(30, 58, 138, 0.4);
            border: 1px solid var(--dark-blue);
            padding: 8px 16px;
            border-radius: 50px;
            margin-bottom: 20px;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            background: var(--vibrant-orange);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .brand-title {
            font-size: 28px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.5px;
        }

        .brand-accent {
            color: var(--vibrant-orange);
        }

        .tagline {
            font-size: 14px;
            color: var(--subtext-gray);
            margin-bottom: 24px;
        }

        .btn-download {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            background: linear-gradient(135deg, var(--emerald-green), #059669);
            color: #FFFFFF;
            text-decoration: none;
            font-size: 18px;
            font-weight: 700;
            padding: 18px 24px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 30px rgba(16, 185, 129, 0.5);
        }

        .qr-box {
            background: #FFFFFF;
            padding: 16px;
            border-radius: 16px;
            display: inline-block;
            margin: 24px 0 16px 0;
        }

        .qr-box img {
            width: 150px;
            height: 150px;
            display: block;
        }

        .qr-label {
            font-size: 12px;
            color: var(--subtext-gray);
        }

        .features-list {
            text-align: left;
            margin-top: 24px;
            background: rgba(15, 23, 42, 0.6);
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .features-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--vibrant-orange);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--light-gray);
            margin-bottom: 8px;
        }

        .feature-item:last-child {
            margin-bottom: 0;
        }

        .steps-box {
            text-align: left;
            margin-top: 20px;
            font-size: 12px;
            color: var(--subtext-gray);
            line-height: 1.6;
        }

        .steps-box strong {
            color: var(--light-gray);
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="logo-badge">
            <div class="logo-icon">🔥</div>
            <span style="font-weight:700; font-size: 14px;">DARKBOOKS KITCHEN</span>
        </div>

        <h1 class="brand-title">Dark<span class="brand-accent">Books</span> App</h1>
        <p class="tagline">Contabilidad simple, crecimiento seguro para tu cocina.</p>

        <!-- Botón Directo de Descarga APK -->
        <a href="darkbooks.apk" download="darkbooks.apk" class="btn-download">
            <span>📥 Descargar APK (Android)</span>
        </a>

        <!-- Código QR para escaneo directo con celular -->
        <div class="qr-box">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://orange-alligator-652108.hostingersite.com/darkbooks.apk" alt="QR Descarga DarkBooks">
        </div>
        <p class="qr-label">📲 Escanea el código QR con la cámara de tu celular para descargar directo</p>

        <!-- Funciones Destacadas -->
        <div class="features-list">
            <div class="features-title">✨ Incluye en esta versión:</div>
            <div class="feature-item">📊 6 Reportes Contables Automáticos (P&L, Food Cost %, Cash Flow)</div>
            <div class="feature-item">🛵 Control de comisiones Uber Eats, DiDi Food y Mostrador</div>
            <div class="feature-item">🧾 Registro de Tickets con Foto de comprobantes</div>
            <div class="feature-item">📦 Control de Inventario con Borrado Lógico y Búsqueda</div>
        </div>

        <!-- Instrucciones de Instalación -->
        <div class="steps-box">
            <p><strong>Guía rápida de instalación:</strong></p>
            <p>1. Presiona el botón verde de descarga o escanea el código QR.</p>
            <p>2. Abre el archivo <code>darkbooks.apk</code> descargado en tu celular.</p>
            <p>3. Si Android lo solicita, activa <em>"Permitir la instalación desde esta fuente"</em>.</p>
        </div>
    </div>

</body>
</html>
