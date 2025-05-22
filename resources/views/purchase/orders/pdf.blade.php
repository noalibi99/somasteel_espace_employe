<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bon de Commande - {{ $purchaseOrder->po_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; margin: 0; padding:0; } /* DejaVu Sans pour UTF-8 */
        .container { width: 90%; margin: 30px auto; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .company-info, .supplier-info, .shipping-info { margin-bottom: 20px; padding: 10px; border: 1px solid #eee; }
        .company-info p, .supplier-info p, .shipping-info p { margin: 2px 0; }
        .po-details p { margin: 2px 0; }
        .po-details { float: right; text-align: right; }
        .clear { clear: both; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .total-section { margin-top: 20px; float: right; width: 40%; }
        .total-section th, .total-section td {text-align: right;}
        .notes-section { margin-top: 30px; border-top: 1px solid #eee; padding-top:10px; }
        .footer { font-size: 9px; position: fixed; bottom: 0; width:100%; text-align:center; }
        .logo { max-width: 150px; max-height: 70px; float: left; margin-right: 20px;}
        .header-content { overflow: auto; /* to contain floats */ }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                @if(public_path('images/logosomasteel.png')) {{-- Ajustez le chemin du logo --}}
                    <img src="{{ public_path('images/logosomasteel.png') }}" alt="Logo" class="logo">
                @endif
                <div>
                    <h1>BON DE COMMANDE</h1>
                    <strong>{{ config('app.name', 'SOMASTEEL') }}</strong><br>
                    {{-- Votre Adresse, Tel, Email, etc. --}}
                    Adresse de votre entreprise<br>
                    Tél: Votre numéro | Email: votre.email@example.com
                </div>
            </div>
        </div>
         <div class="clear"></div>

        <table style="width:100%; margin-bottom: 20px; border:0;">
            <tr>
                <td style="width:50%; border:0; vertical-align:top;">
                    <div class="supplier-info">
                        <strong>Fournisseur :</strong><br>
                        {{ $purchaseOrder->supplier->name }}<br>
                        @if($purchaseOrder->supplier->contact_phone) Tél: {{ $purchaseOrder->supplier->contact_phone }}<br>@endif
                        @if($purchaseOrder->supplier->contact_email) Email: {{ $purchaseOrder->supplier->contact_email }}<br>@endif
                        {{-- Adresse du fournisseur si disponible --}}
                    </div>
                </td>
                <td style="width:50%; border:0; vertical-align:top;">
                    <div class="po-details">
                        <strong>N° BDC :</strong> {{ $purchaseOrder->po_number }}<br>
                        <strong>Date Commande :</strong> {{ $purchaseOrder->order_date->format('d/m/Y') }}<br>
                        @if($purchaseOrder->rfq)<strong>N° RFQ :</strong> RFQ#{{ $purchaseOrder->rfq->id }}<br>@endif
                        <strong>Créé par :</strong> {{ $purchaseOrder->user->nom ?? 'N/A' }}
                    </div>
                </td>
            </tr>
             <tr>
                <td style="width:50%; border:0; vertical-align:top;">
                    <div class="shipping-info">
                        <strong>Adresse de Livraison :</strong><br>
                        {!! nl2br(e($purchaseOrder->shipping_address)) !!}
                    </div>
                </td>
                <td style="width:50%; border:0; vertical-align:top;">
                     <div class="shipping-info"> {{-- Utilisation de la même classe pour le style --}}
                        <strong>Adresse de Facturation :</strong><br>
                        {!! nl2br(e($purchaseOrder->billing_address)) !!}
                    </div>
                </td>
            </tr>
        </table>


        <table>
            <thead>
                <tr>
                    <th class="text-center">#</th>
                    <th>Description Article</th>
                    <th class="text-center">Qté</th>
                    <th class="text-end">Prix Unitaire</th>
                    <th class="text-end">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseOrder->purchaseOrderLines as $index => $line)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        {{ $line->description }}
                        @if($line->article) <small>(Réf: {{ $line->article->reference }})</small> @endif
                    </td>
                    <td class="text-center">{{ $line->quantity_ordered }}</td>
                    <td class="text-end">{{ number_format($line->unit_price, 2, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($line->total_price, 2, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="total-section">
            <tr>
                <th style="width:70%;">Sous-Total HT :</th>
                <td style="width:30%;">{{ number_format($purchaseOrder->total_po_price, 2, ',', ' ') }} {{-- devise --}}</td>
            </tr>
            {{-- Ajoutez ici TVA, Frais de port, etc. si applicable --}}
            {{-- <tr>
                <th>TVA (20%) :</th>
                <td>{{ number_format($purchaseOrder->total_po_price * 0.20, 2, ',', ' ') }}</td>
            </tr> --}}
            <tr style="font-weight:bold; background-color: #f2f2f2;">
                <th>TOTAL À PAYER :</th>
                <td>{{ number_format($purchaseOrder->total_po_price, 2, ',', ' ') }} {{-- devise --}}</td>
            </tr>
        </table>
        <div class="clear"></div>

        @if($purchaseOrder->payment_terms)
        <div class="notes-section">
            <strong>Termes de Paiement :</strong> {{ $purchaseOrder->payment_terms }}
        </div>
        @endif

        @if($purchaseOrder->notes)
        <div class="notes-section">
            <strong>Notes :</strong><br>
            {!! nl2br(e($purchaseOrder->notes)) !!}
        </div>
        @endif

        <div class="footer">
            Merci de votre collaboration. {{ config('app.name', 'SOMASTEEL') }} - Page <span class="page-number"></span>
        </div>
    </div>
</body>
</html>
