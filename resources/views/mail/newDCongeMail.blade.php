<!DOCTYPE html>
<html>
<head>
    <title>Nouvelle Demande de Congé</title>
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
        h1 {
            color: #2a2929;
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
		.logomail{
        	max-width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
    <div class="d-flex justify-content-center">
    	//<img src="{{asset("images/logosomasteel.png")}}" class="logomail p-0 m-0" alt />
    </div>
    
        <h2> <u>Nouvelle Demande de Congé</u></h2>
        <p>Bonjour,</p>
        <p>L'Employé <b> {{ $mailInfo['nom']}} {{ $mailInfo['prénom'] }} </b>avec le matricule <b>{{ $mailInfo['matricule'] }}</b> a fait une nouvelle demande de congé.</p>
        <h4>Détails de la demande :</h4>
        <ul>
            <li><strong>Date de début:</strong> {{ $mailInfo['date_debut'] }}</li>
            <li><strong>Date de fin:</strong> {{ $mailInfo['date_fin'] }}</li>
            <li><strong>Motif:</strong> {{ $mailInfo['motif'] }}</li>
            <li><strong>Autre:</strong> {{ $mailInfo['Autre'] }}</li>
        </ul>
        <p>Cordialement,</p>
        <p>SomaSteel, {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        <p>Cet email a été envoyé par {{ config('app.name') }}.</p>
        <small>Le {{ now() }}</small>
    </div>
</body>
</html>
