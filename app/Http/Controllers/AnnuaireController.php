<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Service;


use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnuaireController extends Controller
{
    public function index()
    {
        $data = DB::table('users')
            ->select('projet', 'service')
            ->distinct()
            ->where('service', '!=', '')
            ->whereNotNull('service')
            ->orderBy('projet', 'asc')
            ->orderBy('service', 'asc')
            ->get();

        $projets = [];

        foreach ($data as $row) {
            if (!in_array($row->service, $projets[$row->projet] ?? [])) {
                $projets[$row->projet][] = $row->service;
            }
        }

        //les resp & dir a choisi
        $responsables = DB::table('responsables')->get();
        $directeurs = DB::table('directeurs')->get();


        return view("annuaire.index", [
            "projects" => $projets,
            "responsables" => $responsables,
            "directeurs" => $directeurs
        ]);
    }

    public function updateResponsible($id, $depart, $projet)
    {
        try {
            // Find the current responsible if exists and update their type to "ouvrier"
            $currentResponsible = User::where('service', $depart)
                ->where('projet', $projet)
                ->where('type', 'responsable')
                ->first();

            if ($currentResponsible) {
                $currentResponsible->update([
                    'type' => 'ouvrier',
                ]);
            }

            // Update the new responsible
            $employee = User::findOrFail($id);
            $employee->update([
                'type' => 'responsable',
            ]);
            // Update the responsable_hiarchique for employees in the same service and project
            User::where('service', $depart)
                ->where('projet', $projet)
                ->update(['responsable_hiarchique' => $employee->matricule]);

            return redirect()->back()->with('success', 'Responsable hiérarchique modifié avec succès.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la modification du responsable hiérarchique' . $e->getMessage());
        }
    }

    public function storeService(Request $request)
    {
        // Validate service data
        $validatedData = $request->validate([
            'service' => 'required|string|max:255',
        ]);

        try {
            // Store the service (department) in your database
            $service = new Service();
            $service->nomService = $validatedData['service'];
            $service->save();

            // Call storeEmployee to save the user data after service creation
            if ($this->storeEmployee($request)) {
                return redirect()->route('annuaire.index')->with('success', 'Département créé avec succès!');
            }

        } catch (Exception $e) {
            $service->delete();
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du service: ' . $e->getMessage());
        }
    }


    public function deleteService(Request $request)
    {
        // Validate input fields
        $request->validate([
            'project'    => 'required|string|exists:users,projet',
            'nomService' => 'required|string|exists:users,service',
        ]);
    
        

        try {
            DB::beginTransaction(); // Start transaction
    
            $project = $request->input('project');
            $nomService = $request->input('nomService');
    
            // Fetch users related to this service in the specified project
            $users = User::where('service', $nomService)
                         ->where('projet', $project);
    
            if ($users->count() > 0) {
                // Delete users if they exist
                $users->delete();
            }
            

            // Delete the service
            $deleted = Service::where('nomService', $nomService)->delete();

            if (!$deleted) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Le département n\'existe pas ou ne peut pas être supprimé.');
            }
    
            DB::commit(); // Commit transaction
            return redirect()->back()->with('success', 'Département et ses utilisateurs supprimés avec succès.');
    
        } catch (Exception $e) {
            DB::rollBack(); // Rollback in case of an error
            return redirect()->back()->with('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }
    public function showDepartment($projet, $depart)
    {

        $users = User::where('service', $depart)
            ->where('projet', $projet)
            ->select('id', 'nom', 'prénom', 'matricule', 'type', 'fonction', 'service AS depart', 'projet', 'profile_picture')
            ->get();
        $responsables = DB::table('responsables')->get();
        $directeurs = DB::table('directeurs')->get();

        return view("annuaire.showDepartment", [
            'employees' => $users,
            'projet' => $projet,
            'depart' => $depart,
            'responsables' => $responsables,
            'directeurs' => $directeurs
        ]);
    }
    public function showEmployee($projet, $depart, $employee_nom, $employee_id)
    {
        //employe infos
        $employee = User::findOrFail($employee_id);

        //leur responsable
        $responsable = User::where('matricule', $employee->responsable_hiarchique)->first(['nom', 'prénom']);

        //leur dir
        $directeur = User::where('matricule', $employee->directeur)->first(['nom', 'prénom']);

        //les service a choisir
        $services = DB::table('services')->pluck('nomService');

        //les resp & dir a choisi
        $responsables = DB::table('responsables')->get();
        $directeurs = DB::table('directeurs')->get();

        $employee->resp_matricule = $employee->responsable_hiarchique;
        $employee->dir_matricule = $employee->directeur;

        $employee->responsable_hiarchique = $responsable ? ($responsable->nom . ' ' . $responsable->prénom) : null;
        $employee->directeur = $directeur ? ($directeur->nom . ' ' . $directeur->prénom) : null;

        if ($employee->service !== $depart) {
            abort(404, 'Employee not found in this department');
        }
        return view('annuaire.showEmployee', compact('employee', 'services', 'responsables', 'directeurs'));
    }


    // public function editEmp($depart, $employee_nom, $employee_id){
    //     $employee = User::findOrFail($employee_id);
    //     return view('annuaire.editEmployee', compact('employee'));
    // }

    public function updateEmp($projet, $employee_id, Request $request)
    {
        try {
            $rules = [
                'nom' => 'required|string|max:100',
                'prénom' => 'required|string|max:100',
                'email' => 'nullable|email|max:255',
                'matricule' => 'required|string|max:50',
                'fonction' => 'required|string|max:255',
                'service' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'responsable_hiarchique' => 'nullable|string|max:255',
                'directeur' => 'nullable|string|max:255',
            ];

            // Validate the request data
            $validatedData = $request->validate($rules);
            // dd($request, $validatedData);

            // Find the employee
            $employee = User::findOrFail($employee_id);

            // Update the employee with validated data
            // dd($validatedData);
            $employee->update($validatedData);
            $employee->projet = $projet;
            $employee->refresh();

            // Redirect back with a success message
            return redirect()->route('annuaire.depart', [$employee->projet, $employee->service])->with('success', 'Informations modifiées avec succès');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error lors de la modification, veuillez vérifié les Information saisi');
        }
    }

    public function storeEmployee(Request $request)
    {
        // Validate the incoming request
        try {
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255',
                'prénom' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users',
                'matricule' => 'required|string|max:50|unique:users',
                'fonction' => 'string|max:255',
                'service' => 'required|string|max:255',
                'projet' => 'required|string|max:100',
                'type' => 'required|string|max:255',
                'solde_conge' => 'max:100',
                'responsable_hiarchique' => 'nullable|string|max:255',
                'directeur' => 'nullable|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ]);

            // Create the new employee
            $user = new User();
            $user->nom = $validatedData['nom'];
            $user->prénom = $validatedData['prénom'];
            $user->email = $validatedData['email'];
            $user->matricule = $validatedData['matricule'];
            $user->fonction = $validatedData['fonction'];
            $user->service = $validatedData['service'];
            $user->projet = $validatedData['projet'];
            $user->type = $validatedData['type'];
            $user->solde_conge = $validatedData['solde_conge'] ?? null;
            $user->responsable_hiarchique = $validatedData['responsable_hiarchique'] ?? null;
            $user->directeur = $validatedData['directeur'] ?? null;
            $user->password = bcrypt($validatedData['password']);
            $user->save();

            return redirect()->route('annuaire.depart', [$validatedData['projet'], $validatedData['service']])->with('success', 'Employee crée avec succès.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création vouliez ressayer plus tard! <br>' . $e->getMessage());
        }
    }


    public function destroyEmp($employee_id)
    {
        try {
            $employee = User::findOrFail($employee_id);
            $depart = $employee->service;
            $projet = $employee->projet;
            $employee->delete();

            return redirect()->route('annuaire.depart', [$projet, $depart])->with('success', 'Employee Supprimé avec succès!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error lors de la Suppression <br>' . $e->getMessage());
        }
    }

    public function changePassword($employee_id, Request $request)
    {
        try {
            $validatedData = $request->validate([
                'new_password' => 'required|min:8|max:16',
                'confirm_new_password' => 'required|same:new_password',
            ]);
            $user = User::findOrFail($employee_id);
            $user->update([
                'password' => bcrypt($validatedData['new_password']),
            ]);
            return redirect()->back()->with('success', 'Mot de passe changé avec succès.');
        } catch (Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification' . $e->getMessage());
        }
    }

}
