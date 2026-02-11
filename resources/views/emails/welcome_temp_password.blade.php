<!DOCTYPE html>
<html>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; padding: 40px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #1e293b; padding: 30px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; letter-spacing: -0.025em;">MibTech <span style="color: #3b82f6;">CMMS</span></h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #1e293b; margin: 0 0 15px 0; font-size: 20px;">Bienvenue sur la plateforme, {{ $name }} !</h2>
                            <p style="color: #475569; line-height: 1.6; margin: 0 0 25px 0;">Un compte a été créé pour vous sur notre système de gestion de maintenance. Vous pouvez désormais accéder à vos outils de travail avec les identifiants ci-dessous :</p>
                            
                            <div style="background-color: #f1f5f9; border-radius: 12px; padding: 25px; text-align: center; margin-bottom: 25px;">
                                <p style="color: #64748b; font-size: 14px; margin: 0 0 10px 0;">Mot de passe temporaire</p>
                                <div style="font-family: monospace; font-size: 28px; color: #1e293b; font-weight: bold; letter-spacing: 2px;">{{ $password }}</div>
                            </div>

                            <p style="color: #475569; line-height: 1.6; margin: 0 0 25px 0;">Pour des raisons de sécurité, il vous sera demandé de modifier ce mot de passe lors de votre première connexion.</p>
                            
                            <div style="text-align: center;">
                                <a href="{{ url('/login') }}" style="display: inline-block; padding: 12px 30px; background-color: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background-color 0.2s;">Se connecter maintenant</a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; text-align: center;">
                            <div style="border-top: 1px solid #e2e8f0; padding-top: 30px;">
                                <p style="color: #94a3b8; font-size: 13px; margin: 0;">© {{ date('Y') }} MibTech Industrial Solutions. Tous droits réservés.</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
