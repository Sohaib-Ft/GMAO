<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Équipement Irréparable</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; background-color: #f9fafb;">
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="color: #dc2626;">Équipement Déclaré Irréparable</h1>
        </div>

        <p>Bonjour <strong>{{ $workOrder->employe->name }}</strong>,</p>

        <p>Nous vous informons que suite à votre demande d'intervention, le technicien a jugé que l'équipement suivant n'est pas réparable :</p>

        <div style="background-color: #fff; padding: 15px; border-radius: 8px; border-left: 4px solid #dc2626; margin: 20px 0;">
            <p><strong>Équipement :</strong> {{ $workOrder->equipement->nom ?? 'N/A' }}</p>
            <p><strong>Code :</strong> {{ $workOrder->equipement->code ?? 'N/A' }}</p>
            <p><strong>Raison de l'échec :</strong></p>
            <p style="font-style: italic; color: #555;">"{{ $reason }}"</p>
        </div>

        <p><strong>Statut de l'équipement :</strong> <span style="color: #dc2626; font-weight: bold;">Inactif / Hors Service</span></p>

        <p>Veuillez contacter le service administratif pour procéder à une demande de remplacement si nécessaire.</p>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center; font-size: 12px; color: #888;">
            <p>Ceci est un message automatique, merci de ne pas répondre.</p>
        </div>
    </div>
</body>
</html>
