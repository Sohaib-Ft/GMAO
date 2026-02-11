<!DOCTYPE html>
<html>
<head>
    <title>Nouveau mot de passe temporaire</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: 0 auto; }
        .header { border-bottom: 2px solid #3b82f6; padding-bottom: 10px; margin-bottom: 20px; }
        .password-box { background: #f3f4f6; padding: 15px; border-radius: 8px; font-size: 20px; font-weight: bold; text-align: center; color: #2563eb; letter-spacing: 2px; }
        .footer { margin-top: 30px; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>GMAO - Récupération de compte</h2>
        </div>
        <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
        <p>Suite à votre demande de récupération de mot de passe, l'administrateur a généré un nouveau mot de passe temporaire pour vous.</p>
        
        <p>Votre nouveau mot de passe est :</p>
        <div class="password-box">
            {{ $temporaryPassword }}
        </div>

        <p><strong>Important:</strong> Pour des raisons de sécurité, nous vous conseillons vivement de changer ce mot de passe dès votre première connexion dans votre profil.</p>
        
        <p>Si vous n'êtes pas à l'origine de cette demande, veuillez contacter immédiatement l'administrateur.</p>

        <p>Cordialement,<br>L'équipe Support CMMS</p>


    </div>
</body>
</html>
