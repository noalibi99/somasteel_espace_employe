<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Demande Congé</title>
</head>
    <style>
        .text-center{
            text-align: center;
        }
        .content-between{
            display: flex;
            justify-content: space-between;
        }
        .bg-gray{
            background-color: rgb(231, 230, 230) !important;
        }
        .w-50{
            width: 50% !important;
        }
        .w-100{
            width: 100% !important;
        }
    </style>
<body >
    {{-- DEMANDE CONGER TAMPLATE --}}
    <small class="text-center">Valider Le: {{$pdfData['vDate']}}</small>
    <table class="w-100">
        <thead style="padding-top: 0;">
            <tr >
                <th colspan="2" class="bg-gray"><h1 style="margin: 10px;">Demande Congé </h1></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="w-50">
                    <h2 class=""><u>Employé</u></h2>
                    <p><strong>Nom:</strong> {{ $pdfData['dcinfo']->nom }}</p>
                    <p><strong>Prénom:</strong> {{ $pdfData['dcinfo']->prénom }}</p>
                    <p><strong>Solde congé restant:</strong> {{ $pdfData['dcinfo']->solde_rest}}</p>
                </td>
                <td class="w-50">
                    <h2 class=""><u>Demande Details</u></h2>
                    <p><strong>Start Date:</strong> {{ $pdfData['dcinfo']->start_date }}</p>
                    <p><strong>End Date:</strong> {{ $pdfData['dcinfo']->end_date }}</p>
                    <p><strong>Nombre des jours:</strong> {{ $pdfData['dcinfo']->nj_decompter}}</p>
                </td>
            </tr>
            <tr>
                <td class="w-50">
                    <h2 class=""><u>Motif</u></h2>
                    <p>{{ $pdfData['dcinfo']->motif }}</p>
                </td>
                <td class="w-50">
                    <h2 class=""><u>Autre</u></h2>
                    <p>{{ $pdfData['dcinfo']->autre }}</p>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr >
                <td colspan="2">
                    <h2 class="text-center" style="margin: 5px;"><u>Valider par</u></h2>
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Responsable:</strong> {{$pdfData['rNom'] . ' ' . $pdfData['rPrénom']}}
                </td>
                <td>
                    <strong>Directeur:</strong> {{$pdfData['dNom'] . ' ' . $pdfData['dPrénom']}}
                </td>
            </tr>
            <tr>
                <td  colspan="2"  class="text-center" style="padding-top: 10px;">
                    <small>SomaSteel, Ressources Humain.</small>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>