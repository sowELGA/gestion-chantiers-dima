<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #F1F5F9;
            padding: 40px 20px;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
        }

        .card {
            background: #FFFFFF;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
        }

        .header {
            background: #0F172A;
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-icon {
            width: 44px;
            height: 44px;
            background: #1C9F93;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .header-icon svg {
            width: 24px;
            height: 24px;
        }

        .header-title {
            font-size: 20px;
            font-weight: 800;
            color: #FFFFFF;
        }

        .header-sub {
            font-size: 11px;
            color: #1C9F93;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            margin-top: 2px;
        }

        .body {
            padding: 32px;
        }

        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 10px;
        }

        .intro {
            font-size: 14px;
            color: #64748B;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .divider {
            height: 1px;
            background: #F1F5F9;
            margin: 24px 0;
        }

        .btn {
            display: block;
            text-align: center;
            background: #1C9F93;
            color: #FFFFFF;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            margin: 24px 0;
        }

        .btn-warning {
            background: #D97706;
        }

        .lien-texte {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 12px;
            color: #64748B;
            word-break: break-all;
            margin-bottom: 20px;
        }

        .lien-texte a {
            color: #1C9F93;
            text-decoration: none;
        }

        .warning {
            background: #FFFBEB;
            border: 1px solid #FCD34D;
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .warning-text {
            font-size: 12px;
            color: #92400E;
            line-height: 1.6;
        }

        .info-box {
            background: #F0FDF4;
            border: 1px solid #86EFAC;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 13px;
            color: #166534;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .footer {
            background: #F8FAFC;
            border-top: 1px solid #E2E8F0;
            padding: 20px 32px;
            text-align: center;
        }

        .footer-text {
            font-size: 11px;
            color: #94A3B8;
            line-height: 1.7;
        }

        .footer-logo {
            font-size: 13px;
            font-weight: 800;
            color: #1C9F93;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            @yield('contenu')
            <div class="footer">
                <div class="footer-logo">DIMA GROUPE</div>
                <div class="footer-text">
                    © {{ date('Y') }} Dima Groupe · Sotrac Mermoz, Dakar, Sénégal<br>
                    Système de Gestion et Suivi des Chantiers v1.0<br>
                    Cet email a été envoyé automatiquement — ne pas répondre.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
