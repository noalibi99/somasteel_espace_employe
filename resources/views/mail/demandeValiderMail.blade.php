<!DOCTYPE html>
<html>
<head>
    <title>Demande de Congé Approuvée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #2a2929;
            text-decoration: underline;
        }
        p {
            color: #666;
            line-height: 1.6;
        }
        ul {
            list-style: none;
            padding-left: 0;
        }
        li {
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 20px;
            color: #ffa928;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><u>Demande de Congé Approuvée</u></h2>
        <p>Bonjour {{ $user['nom'] . ' ' . $user['prénom'] }}, {{ $user['matricule'] }}</p>
        <p>Votre demande de congé a été <b>Approuvée</b>, Vous trouverez ci-joint le fichier PDF représentant votre demande de congé.</p>
        {{-- <h4>Détails de la demande :</h4>
        <ul>
            <li><strong>Date de début:</strong> {{ $demande['date_debut'] }}</li>
            <li><strong>Date de fin:</strong> {{ $demande['date_fin'] }}</li>
            <li><strong>Motif:</strong> {{ $demande['motif'] }}</li>
        </ul> --}}
        <p>Cordialement,</p>
        <p>SomaSteel, {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        <p>Cet email a été envoyé par {{ config('app.name') }}.</p>
        <small>Le {{ now() }}</small>
    </div>
</body>
</html>
