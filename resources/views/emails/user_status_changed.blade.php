@component('mail::message')
<!DOCTYPE html>
<html>
<head>
    <style>
        .status-badge {
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 20px;
        }
        .active-badge { background-color: #dcfce7; color: #166534; }
        .inactive-badge { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
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
                            @if($statusLabel === 'réactivé')
                                <div style="padding: 8px 16px; border-radius: 50px; font-weight: bold; display: inline-block; margin-bottom: 20px; background-color: #dcfce7; color: #166534;">
                                    Compte Activé
                                </div>
                                <h2 style="color: #1e293b; margin: 0 0 15px 0; font-size: 20px;">Bonjour {{ $user->name }},</h2>
                                <p style="color: #475569; line-height: 1.6; margin: 0 0 25px 0;">Bonne nouvelle ! Votre compte d'accès au système de gestion de maintenance a été réactivé par l'administrateur.</p>
                                <div style="text-align: center;">
                                    <a href="{{ url('/login') }}" style="display: inline-block; padding: 12px 30px; background-color: #3b82f6; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background-color 0.2s;">Se connecter</a>
                                </div>
                            @else
                                <div style="padding: 8px 16px; border-radius: 50px; font-weight: bold; display: inline-block; margin-bottom: 20px; background-color: #fee2e2; color: #991b1b;">
                                    Compte Suspendu
                                </div>
                                <h2 style="color: #1e293b; margin: 0 0 15px 0; font-size: 20px;">Bonjour {{ $user->name }},</h2>
                                <p style="color: #475569; line-height: 1.6; margin: 0 0 25px 0;">Votre compte d'accès au système a été temporairement suspendu. Vous ne pourrez plus vous connecter à la plateforme jusqu'à nouvel ordre.</p>
                                <div style="padding: 15px; background-color: #f1f5f9; border-radius: 8px; border-left: 4px solid #cbd5e1;">
                                    <p style="color: #64748b; font-size: 14px; margin: 0;">Si vous pensez qu'il s'agit d'une erreur, veuillez contacter votre responsable technique ou l'administrateur du système.</p>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; text-align: center;">
                            <div style="border-top: 1px solid #e2e8f0; padding-top: 30px;">
                                <p style="color: #94a3b8; font-size: 13px; margin: 0;">© {{ date('Y') }} MibTech Industrial Solutions. Tous droits réservés.</p>
                                <p style="color: #94a3b8; font-size: 11px; margin: 10px 0 0 0;">Ceci est un message automatique, veuillez ne pas y répondre.</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
