<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de votre mot de passe</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #fafafa;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 400px;
            margin: 40px auto;
            background-color: #ffffff;
            border: 1px solid #dbdbdb;
            border-radius: 3px;
            padding: 40px;
            text-align: center;
        }
        .header {
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #262626;
            text-decoration: none;
        }
        .content {
            color: #262626;
            font-size: 14px;
            line-height: 18px;
            margin-bottom: 30px;
        }
        .btn {
            display: inline-block;
            background-color: #0095f6;
            color: #ffffff !important;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .footer {
            color: #8e8e8e;
            font-size: 12px;
            margin-top: 40px;
            text-align: center;
        }
        .divider {
            height: 1px;
            background-color: #dbdbdb;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if(isset($logo) && file_exists($logo))
                <img src="{{ $message->embed($logo) }}" alt="MibTech" style="width:60px; height:auto; display:block; margin:0 auto 20px;">
            @endif
            <h2 style="color: #24292e; font-size: 24px; font-weight: 400; margin: 0; margin-bottom: 10px;">Réinitialiser votre mot de passe, <span style="font-weight: 600;">{{ $name }}</span></h2>
        </div>
        
        <div class="content" style="text-align: center; color: #586069; font-size: 14px;">
            <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte <strong>MibTech CMMS</strong>. Utilisez le bouton ci-dessous pour confirmer votre identité et sécuriser votre compte.</p>
        </div>

        <a href="{{ $url }}" class="btn">Réinitialiser le mot de passe</a>

        <div class="divider"></div>

        <div class="content" style="color: #8e8e8e; font-size: 12px;">
            <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail en toute sécurité. Votre mot de passe ne sera pas modifié tant que vous n'aurez pas cliqué sur le lien ci-dessus.</p>
        </div>
    </div>

    <div class="footer">
        © {{ date('Y') }} MibTech CMMS — Tous droits réservés.
    </div>
</body>
</html>
