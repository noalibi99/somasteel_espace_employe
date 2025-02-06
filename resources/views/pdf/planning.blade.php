<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning PDF</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            overflow: hidden; /* Prevent overflow */
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 0.25rem;
            margin-bottom: 5px; /* Reduce margin for smaller spacing */
            padding: 4px; /* Reduce padding */
            box-sizing: border-box;
            page-break-inside: avoid; /* Prevent breaking card into multiple pages */
            width: 100%; /* Ensure full width within column */
            font-size: 8pt; /* Further reduce font size */
        }

        .card-header {
            background-color: #ffc105;
            color: white;
            padding: 2px; /* Reduce padding */
            border-bottom: 1px solid #ddd;
            border-radius: 0.25rem 0.25rem 0 0;
            font-size: 8pt; /* Further reduce font size */
        }

        .card-body {
            padding: 2px; /* Reduce padding */
            padding-left: 10px;
        }

        ul {
            padding-left: 5px; /* Reduce padding */
            margin: 0;
            font-size: 8pt; /* Further reduce font size */
        }

        h2 {
            font-size: 10pt; /* Further reduce font size */
            margin: 0 0 5px; /* Reduce margin below title */
        }

        table {
            width: 100%;
            border-collapse: collapse; /* Ensure borders do not overlap */
        }

        td {
            padding: 5px; /* Reduce padding */
            padding-left: 10px;
            padding-right: 10px;
            box-sizing: border-box;
            vertical-align: top; /* Align text to the top */
            text-align: start;
            font-size: 8pt; /* Further reduce font size */
            width: 16.66%; /* Ensure 6 columns fit in one row */
        }

        .col-lg-2, .col-md-4 {
            flex: 0 0 auto; /* Control column width */
            max-width: 100%; /* Ensure column width is responsive */
            box-sizing: border-box;
            padding: 0; /* Remove padding */
        }
        footer {
            width: 100%;
            display: flex;
            justify-content: center;
            font-size: 0.7rem;
            position: absolute; /* Ensure the footer is positioned correctly */
            bottom: 0;
            left: 50%;
            right: 50%;
            transform: translate(-50%, -50%);
            margin-top: auto; /* Pushes the footer to the bottom of the page */
            text-align: center; /* Center align text inside the footer */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="modal-body">
            <div id="team-list-view" class="container mt-4">
                <div style="width: 100%;">
                    <h2 style="display: flex; justify-content: center; width:100%; font-size:1rem;">Planning des équipes de LAMINOIR</h2>
                </div>
                <table>
                    <tbody>
                        @php
                            $teams = $equipesUsers->chunk(6); // Split into chunks of 6
                        @endphp
                        @foreach($teams as $teamRow)
                            <tr>
                                @foreach($teamRow as $equipe)
                                    <td>
                                        <div class="col-lg-2 col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5 style="display: inline; width:fit-content; font-size:1rem;">{{ $equipe->nom_equipe }}</h5> 
                                                    <small style="font-size:0.8rem;">
                                                        @if($equipe->users->isNotEmpty())
                                                            {{ $equipe->users->first()->shift ? $equipe->users->first()->shift->group . " " . $equipe->users->first()->shift->name : "Pas de shift !" }}
                                                        @else
                                                            Aucun utilisateur!!
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="card-body">
                                                    <p><strong>Membres de l'équipe:</strong></p>
                                                    <ul>
                                                        @foreach($equipe->users as $user)
                                                            <li>{{ $user->nom . ' ' . $user->prénom }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <small>Direction, SOMASTEEL le {{ now() }}</small>
    </footer>

</body>
</html>
