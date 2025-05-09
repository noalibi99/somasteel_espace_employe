<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shift;
use App\Models\Attendance;
use App\Models\Equipe;

use App\Exports\ShiftsExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;


class AbsenceController extends Controller
{

    public function index(Request $request)
    {
        $usersLaminoire = [];
        $usersAcierie = [];
        $usersAdministration = [];
        $usersChauffeur = [];
        $usersLAAC = [];
        // Retrieve users based on the logged-in user's role (RH or Responsable)
        if (Auth::user()->isRH()) {
            $usersLaminoire = User::where('projet', 'LAMINOIR')
                ->select('id', 'matricule', 'nom', 'prénom', 'shift_id', 'service')
                ->paginate();
            $usersAcierie = User::where('projet', 'ACIERIE')
            ->select('id', 'matricule', 'nom', 'prénom', 'shift_id', 'service')
                ->paginate();
            $usersAdministration = User::where('projet', 'ADMINISTRATION')
            ->select('id', 'matricule', 'nom', 'prénom', 'shift_id', 'service')
                ->paginate();
            $usersChauffeur = User::where('projet', 'CHAUFFEUR')
            ->select('id', 'matricule', 'nom', 'prénom', 'shift_id', 'service')
                ->paginate();
        } elseif (Auth::user()->isResponsable()) {
            $usersLAAC = User::where('responsable_hiarchique', Auth::user()->matricule)->whereIn('projet', ['LAMINOIR', 'ACIERIE', 'ADMINISTRATION'])
                ->select('id', 'matricule', 'nom', 'prénom', 'shift_id', 'service')
                ->paginate(10);
        }

        $shifts = Shift::all();
        $today = $request->input('date') ?? Carbon::today();

        // Fetch attendance data for the selected day
        $attendances = DB::table('attendances')
            ->whereDate('date', $today)
            ->get()
            ->keyBy(function ($item) {
                return $item->user_id . '_' . $item->shift_id;
            });

        $declaredAttendances = DB::table('attendances')
            ->join('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->whereDate('attendances.date', $today)
            ->select('attendances.*', 'shifts.name as shift_name')
            ->get();

        // $uniqueServices = User::whereIn('projet', ['LAMINOIR', 'ACIERIE', 'ADMINISTRATION', 'CHAUFFEUR'])
        // ->distinct()
        // ->get(['service']);

        $LaminoireServices = User::where('projet', 'LAMINOIR')
        ->distinct()
        ->get(['service']);

        $AcierieServices = User::where('projet', 'ACIERIE')
        ->distinct()
        ->get(['service']);

        $AdministrationServices = User::where('projet', 'ADMINISTRATION')
        ->distinct()
        ->get(['service']);

        $ChauffeurServices = User::where('projet', 'CHAUFFEUR')
        ->distinct()
        ->get(['service']);

        $tableServices = [
            'laminoire' => $LaminoireServices,
            'acierie' => $AcierieServices,
            'administration' => $AdministrationServices,
            'chauffeur' => $ChauffeurServices
        ];

        // dd($uniqueServices);

        // Fetch result data
        $résultatData = $this->getResultData($today);

        return view('absence.absenceDec', compact('usersLAAC','usersLaminoire', 'usersAcierie', 'usersAdministration', 'usersChauffeur', 'shifts', 'résultatData', 'attendances', 'today', 'declaredAttendances', 'tableServices'));
    }


    public function getResultData($date)
    {
        // Retrieve all users along with their shifts
        $users = User::select(
            'users.id',
            'users.service',
            'shifts.name as shift_name',
            'shifts.group as shift_group',
            'users.nom',
            'users.prénom',
            'users.shift_id',
            'users.matricule'
        )
            ->join('shifts', 'users.shift_id', '=', 'shifts.id')
            ->get();

        // Retrieve attendance data for the given date
        $attendances = DB::table('attendances')
            ->whereDate('date', $date)
            ->get()
            ->keyBy(function ($item) {
                return $item->user_id . '_' . $item->shift_id;
            });

        // Map users to include their attendance status
        return $users->map(function ($user) use ($attendances) {
            // Construct the key for the attendance
            $attendanceKey = $user->id . '_' . $user->shift_id;

            // Retrieve the attendance record
            $attendance = $attendances->get($attendanceKey);

            // Determine the status
            $status = $attendance ? $attendance->status : 'Absent'; // Default to 'Absent' if no record is found

            return (object) [
                'matricule' => $user->matricule,
                'service' => $user->service,
                'shift_name' => $user->shift_name,
                'shift_group' => $user->shift_group,
                'nom' => $user->nom,
                'prénom' => $user->prénom,
                'status' => $status,
                // 'date' => $date
            ];
        });
    }
    public function declareAttendance(Request $request)
    {
        // $shiftId = $request->input('shift_id');

        // // Loop through the status inputs (Présent or Absent)
        // foreach ($request->except(['_token', 'shift_id']) as $key => $value) {
        //     if (Str::startsWith($key, 'status_')) {
        //         // Extract user ID from the key (status_userId_shiftId)
        //         $userId = Str::after($key, 'status_');
        //         $status = $value;

        //         // Save or update the attendance declaration
        //         DB::table('attendances')->updateOrInsert(
        //             [
        //                 'user_id' => $userId,
        //                 'date' => Carbon::today(), // Check for existing record based on user and today's date
        //             ],
        //             [
        //                 'shift_id' => $shiftId,
        //                 'status' => $status,
        //                 'updated_at' => now() // Update with new values or insert if no record exists
        //             ]
        //         );
        //     }
        // }

            // Retrieve all the submitted data
    $attendanceData = $request->input('status');
        $shiftData = $request->input('shift');

    // dd($shiftData);
        // Loop over each user in the form
        foreach ($attendanceData as $userId => $status) {
            $shiftId = $shiftData[$userId];

            // If no status or shift was set for this user, skip processing
            if (empty($status) || empty($shiftId)) {
                continue;
            }

            // Use updateOrInsert to either update or insert attendance records
            DB::table('attendances')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'date' => Carbon::today(), // Conditions for finding existing record
                ],
                [
                    'status' => $status,
                    'shift_id' => $shiftId,
                    'updated_at' => now(),
                    'created_at' => now(),      // If inserted, set created_at
                ]
            );
        }
        return redirect()->back()->with('success', 'Attendance déclarée avec succès.');
    }


    public function export(Request $request)
    {
        //dd($request->all());
        // Determine the date to use
        $date = $request->input('date') ?? Carbon::today()->toDateString();

        // Fetch data for export
        $users = User::whereIn('projet', ['LAMINOIR', 'ACIERIE'])->get(['id', 'matricule', 'nom', 'prénom', 'service', 'shift_id']);
        $declaredAttendances = DB::table('attendances')
            ->join('shifts', 'attendances.shift_id', '=', 'shifts.id')
            ->whereDate('attendances.date', $date)
            ->select('attendances.*', 'shifts.name as shift_name')
            ->get();

        // Prepare data for export
        $exportData = $users->map(function ($user) use ($declaredAttendances) {
            $status = 'N/A';
            $selectedShift = null;

            foreach ($declaredAttendances as $attendance) {
                if ($attendance->user_id == $user->id) {
                    $status = $attendance->status;
                    $selectedShift = $attendance->shift_id;
                    break;
                }
            }

            $shiftName = $selectedShift ? Shift::find($selectedShift)->name : 'N/A'; // Get shift name

            return [
                $user->matricule,
                $user->nom . ' ' . $user->prénom,
                $user->service,
                $status,
                $shiftName,
            ];
        });

        return Excel::download(new ShiftsExport($exportData->toArray(), $date), 'Absneces_'. $date .'.xlsx');
    }



    public function store(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:users,id',
            'shift_id' => 'required|exists:shifts,id',
        ]);

        try{
            foreach ($request->employee_ids as $userId) {
                $status = $request->input('status_' . $userId . '_' . $request->shift_id);
                Attendance::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'shift_id' => $request->shift_id,
                        'date' => now()->toDateString(),
                    ],
                    ['status' => $status]
                );
            }

            return redirect()->back()->with('success', 'List de Présence est enregistré avec succès.');

        }catch(Exception $e){
            return redirect()->back()->withInput()->with('error', 'Un erreur ce produit vouliez vérifier votre choix!.');
        }
    }

    public function updateShift(Request $request)
    {
        $shiftId = $request->input('selected_shift_id');
        $userIds = $request->input('user_ids');
        $teamIds = $request->input('team_ids');

        if ($shiftId) {
            if ($userIds) {
                User::whereIn('id', $userIds)->update(['shift_id' => $shiftId]);
            }

            if ($teamIds) {
                User::whereIn('equipe_id', $teamIds)->update(['shift_id' => $shiftId]);
            }

            return redirect()->back()->with('success', 'Utilisateurs mis à jour avec succès.');
        } else {
            return redirect()->back()->with('error', 'Non shift sélectionné.');
        }
    }

    public function updateEquipe(Request $request, $id)
    {
        $equipe = Equipe::findOrFail($id);

        // Renommer équipe
        if ($request->has('team_name')) {
            $equipe->nom_equipe = $request->input('team_name');
        }

        // Add the selected shift to the team
        if ($request->has('shift_id')) {
            $shiftId = $request->input('shift_id');
            // Ensure shift exists
            $shift = Shift::find($shiftId);
            if ($shift) {
                $equipe->shift_id = $shiftId;
            }
        }


        // Ajouter utilisateur à l'équipe
        // if ($request->has('add_user_team')) {
        //     $userId = $request->input('add_user_team');
        //     $user = User::find($userId);
        //     if ($user) {
        //         $user->equipe_id = $equipe->id;  // Update the user’s equipe_id
        //         $user->save();
        //     }
        // }
        if ($request->has('add_user_team')) {
            $userIds = $request->input('add_user_team');
            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user) {
                    $user->equipe_id = $equipe->id;  // Update the user’s equipe_id
                    $user->save();
                }
            }
        }
        // Retirer utilisateur de l'équipe
        if ($request->has('remove_user_team')) {
            $userId = $request->input('remove_user_team');
            $user = User::find($userId);
            if ($user && $user->equipe_id == $equipe->id) {
                $user->equipe_id = null;  // Remove the user from the current team
                $user->save();
            }
        }

        // Déplacer utilisateur à une autre équipe
        if ($request->has('move_user_team') && $request->has('move_to_team')) {
            $userId = $request->input('move_user_team');
            $newEquipeId = $request->input('move_to_team');
            $user = User::find($userId);
            if ($user) {
                $user->equipe_id = $newEquipeId;  // Update the user’s equipe_id to the new team
                $user->save();
            }
        }

        $equipe->save();

        return redirect()->back()->with('success', 'Équipe mise à jour avec succès');
    }

    public function createEquipe(Request $request)
    {
        $request->validate([
            'new_team_name' => 'required|string|max:255',
        ]);

        $equipe = new Equipe();
        $equipe->nom_equipe = $request->input('new_team_name');
        $equipe->save();

        return redirect()->back()->with('success', 'Nouvelle équipe créée avec succès');
    }

    public function deleteEquipe($id)
    {
        try{

            $equipe = Equipe::findOrFail($id);
            $equipe->delete();

            return redirect()->back()->with('success', 'Équipe supprimée avec succès');
        }catch(Exception $e){
            return redirect()->back()->with('error', 'L\'Équipe à Supprimée doit être vide!');
        }
    }

    public function downloadPlanning()
    {
        $equipesUsers = Equipe::with(['users.shift'])->get();

        // Render the view without the buttons
        $html = view('pdf.planning', compact('equipesUsers'))->render();

        // Set options for DOMPDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);


        // Initialize Dompdf
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A3', 'landscape');
        $dompdf->render();

        // Download the PDF
        return $dompdf->stream('planning.pdf', ['Attachment' => 1]);
    }

    

}