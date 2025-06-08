<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Service;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

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
            DB::beginTransaction();

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

            DB::commit();
            return redirect()->back()->with('success', 'Responsable hiérarchique modifié avec succès.');
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Database error while updating responsible', [
                'employee_id' => $id,
                'service' => $depart,
                'projet' => $projet,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Erreur de base de données lors de la modification du responsable hiérarchique.');
            
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Unexpected error while updating responsible', [
                'employee_id' => $id,
                'service' => $depart,
                'projet' => $projet,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'Une erreur inattendue s\'est produite lors de la modification du responsable hiérarchique.');
        }
    }

    public function storeService(Request $request)
    {
        try {
            // Validate service data
            $validatedData = $request->validate([
                'service' => 'required|string|max:255',
            ]);

            DB::beginTransaction();

            // Store the service (department) in your database
            $service = new Service();
            $service->nomService = $validatedData['service'];
            $service->save();

            // Call storeEmployee to save the user data after service creation
            $employeeCreated = $this->storeEmployee($request);
            
            if ($employeeCreated) {
                DB::commit();
                return redirect()->route('annuaire.index')->with('success', 'Département créé avec succès!');
            } else {
                DB::rollBack();
                return redirect()->back()->withInput()->with('error', 'Erreur lors de la création de l\'employé pour le nouveau département.');
            }

        } catch (ValidationException $e) {
            // Validation errors - let Laravel handle these automatically
            throw $e;
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Database error while creating service', [
                'service_name' => $validatedData['service'] ?? 'unknown',
                'error' => $e->getMessage(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            // Check for specific database constraints
            if ($e->getCode() === '23000') { // Integrity constraint violation
                if (str_contains($e->getMessage(), 'nomService')) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Ce nom de département existe déjà. Veuillez choisir un nom différent.');
                }
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'Erreur de base de données lors de la création du département.');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Unexpected error while creating service', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            return redirect()->back()->withInput()
                ->with('error', 'Une erreur inattendue s\'est produite lors de la création du département.');
        }
    }


    public function deleteService(Request $request)
    {
        try {
            // Validate input fields
            $validatedData = $request->validate([
                'project'    => 'required|string|exists:users,projet',
                'nomService' => 'required|string|exists:users,service',
            ]);

            DB::beginTransaction();
    
            $project = $validatedData['project'];
            $nomService = $validatedData['nomService'];
    
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
    
            DB::commit();
            return redirect()->back()->with('success', 'Département et ses utilisateurs supprimés avec succès.');
    
        } catch (ValidationException $e) {
            // Validation errors - let Laravel handle these automatically
            throw $e;
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Database error while deleting service', [
                'project' => $request->input('project'),
                'service' => $request->input('nomService'),
                'error' => $e->getMessage()
            ]);
            
            // Check for foreign key constraints
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->with('error', 'Impossible de supprimer ce département car il contient des données liées.');
            }
            
            return redirect()->back()
                ->with('error', 'Erreur de base de données lors de la suppression du département.');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Unexpected error while deleting service', [
                'project' => $request->input('project'),
                'service' => $request->input('nomService'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Une erreur inattendue s\'est produite lors de la suppression du département.');
        }
    }
    public function showDepartment($projet, $depart)
    {

        $users = User::where('service', $depart)
            ->where('projet', $projet)
            ->select('id', 'nom', 'prénom', 'matricule', 'type', 'fonction', 'service AS depart', 'projet', 'profile_picture')
            ->paginate(5);
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
                'solde_conge' => 'required|numeric|max:30',
                'responsable_hiarchique' => 'nullable|string|max:255',
                'directeur' => 'nullable|string|max:255',
            ];

            // Validate the request data
            $validatedData = $request->validate($rules);

            DB::beginTransaction();

            // Find the employee
            $employee = User::findOrFail($employee_id);

            // Update the employee with validated data
            $employee->update($validatedData);
            $employee->projet = $projet;
            $employee->refresh();

            
            if ($employee->type === 'responsable') {
                // chercher le dernier responsable
                $currentResponsible = User::where('service', $employee->service)
                    ->where('projet', $employee->projet)
                    ->where('type', 'responsable')
                    ->where('id', '!=', $employee->id)
                    ->first();
            
                // si le responsable actuel est trouvé changer le type a ouvrier
                if ($currentResponsible) {
                    $currentResponsible->update(['type' => 'ouvrier']);
                }
            
                // mettre a jour le responsable hiarchique de tous les employés y compris le responsable actuel
                User::where('service', $employee->service)
                    ->where('projet', $employee->projet)
                    ->update(['responsable_hiarchique' => $employee->matricule]);
            }            
            
            //idem pour le directeur
            if ($employee->type === 'directeur') {
                $currentDirecteur = User::where('service', $employee->service)
                    ->where('projet', $employee->projet)
                    ->where('type', 'directeur')
                    ->where('id', '!=', $employee->id)
                    ->first();
            
                if ($currentDirecteur) {
                    $currentDirecteur->update(['type' => 'ouvrier']);
                }
            
                User::where('service', $employee->service)
                    ->where('projet', $employee->projet)
                    ->update(['directeur' => $employee->matricule]);
            }
            
            DB::commit();
            
            // Redirect back with a success message
            return redirect()->route('annuaire.depart', [$employee->projet, $employee->service])
                ->with('success', 'Informations modifiées avec succès');
                
        } catch (ValidationException $e) {
            // Validation errors - let Laravel handle these automatically
            throw $e;
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Database error while updating employee', [
                'employee_id' => $employee_id,
                'projet' => $projet,
                'error' => $e->getMessage(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            // Check for specific database constraints
            if ($e->getCode() === '23000') { // Integrity constraint violation
                if (str_contains($e->getMessage(), 'matricule')) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Ce matricule existe déjà. Veuillez utiliser un matricule différent.');
                }
                if (str_contains($e->getMessage(), 'email')) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Cette adresse email est déjà utilisée.');
                }
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'Erreur de base de données lors de la modification des informations.');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Unexpected error while updating employee', [
                'employee_id' => $employee_id,
                'projet' => $projet,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            return redirect()->back()->withInput()
                ->with('error', 'Une erreur inattendue s\'est produite lors de la modification des informations.');
        }
    }

    public function storeEmployee(Request $request)
    {
        try {
            // Validate the incoming request
            $validatedData = $request->validate([
                'nom' => 'required|string|max:255',
                'prénom' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users',
                'matricule' => 'required|string|max:50|unique:users',
                'fonction' => 'required|string|max:255',
                'service' => 'required|string|max:255',
                'projet' => 'required|string|max:100',
                'type' => 'required|string|max:255',
                'solde_conge' => 'max:30',
                'responsable_hiarchique' => 'nullable|string|max:255',
                'directeur' => 'nullable|string|max:255',
                'password' => 'required|string|min:6|confirmed',
            ]);

            // Use database transaction for data consistency
            DB::beginTransaction();

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
            
            // Get responsable hiarchique if not provided
            if (empty($validatedData['responsable_hiarchique'])) {
                $responsable = User::where('service', $validatedData['service'])
                    ->where('projet', $validatedData['projet'])
                    ->where('type', 'responsable')
                    ->first();
                $user->responsable_hiarchique = $responsable ? $responsable->matricule : null;
            } else {
                $user->responsable_hiarchique = $validatedData['responsable_hiarchique'];
            }
            
            // Get directeur if not provided
            if (empty($validatedData['directeur'])) {
                $directeur = User::where('service', $validatedData['service'])
                    ->where('projet', $validatedData['projet'])
                    ->where('type', 'directeur')
                    ->first();
                $user->directeur = $directeur ? $directeur->matricule : null;
            } else {
                $user->directeur = $validatedData['directeur'];
            }
            
            $user->password = bcrypt($validatedData['password']);
            $user->save();

            // Update hierarchy if user is responsable or directeur
            if ($user->type === 'responsable') {
                User::where('service', $validatedData['service'])
                    ->where('projet', $validatedData['projet'])
                    ->update(['responsable_hiarchique' => $user->matricule]);
            }

            if ($user->type === 'directeur') {
                User::where('service', $validatedData['service'])
                    ->where('projet', $validatedData['projet'])
                    ->update(['directeur' => $user->matricule]);
            }

            DB::commit();

            return redirect()->route('annuaire.depart', [$validatedData['projet'], $validatedData['service']])
                ->with('success', 'Employee crée avec succès.');

        } catch (ValidationException $e) {
            // Validation errors - let Laravel handle these automatically
            throw $e;
            
        } catch (QueryException $e) {
            DB::rollBack();
            
            // Log the specific database error for debugging
            Log::error('Database error while creating employee', [
                'error' => $e->getMessage(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            // Check for specific database constraints
            if ($e->getCode() === '23000') { // Integrity constraint violation
                if (str_contains($e->getMessage(), 'matricule')) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Ce matricule existe déjà. Veuillez utiliser un matricule différent.');
                }
                if (str_contains($e->getMessage(), 'email')) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Cette adresse email est déjà utilisée.');
                }
            }
            
            return redirect()->back()->withInput()
                ->with('error', 'Erreur de base de données. Veuillez vérifier vos données et réessayer.');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            // Log unexpected errors for debugging
            Log::error('Unexpected error while creating employee', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            return redirect()->back()->withInput()
                ->with('error', 'Une erreur inattendue s\'est produite. Veuillez réessayer plus tard.');
        }
    }


    public function destroyEmp($employee_id)
    {
        try {
            DB::beginTransaction();
            
            $employee = User::findOrFail($employee_id);
            $depart = $employee->service;
            $projet = $employee->projet;
            
            // Check if employee is a responsable or directeur and handle hierarchy updates
            if ($employee->type === 'responsable') {
                User::where('service', $depart)
                    ->where('projet', $projet)
                    ->update(['responsable_hiarchique' => null]);
            }
            
            if ($employee->type === 'directeur') {
                User::where('service', $depart)
                    ->where('projet', $projet)
                    ->update(['directeur' => null]);
            }
            
            $employee->delete();
            
            DB::commit();

            return redirect()->route('annuaire.depart', [$projet, $depart])
                ->with('success', 'Employé supprimé avec succès!');
                
        } catch (QueryException $e) {
            DB::rollBack();
            
            Log::error('Database error while deleting employee', [
                'employee_id' => $employee_id,
                'error' => $e->getMessage()
            ]);
            
            // Check for foreign key constraints
            if ($e->getCode() === '23000') {
                return redirect()->back()
                    ->with('error', 'Impossible de supprimer cet employé car il est lié à d\'autres données.');
            }
            
            return redirect()->back()
                ->with('error', 'Erreur de base de données lors de la suppression de l\'employé.');
                
        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error('Unexpected error while deleting employee', [
                'employee_id' => $employee_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Une erreur inattendue s\'est produite lors de la suppression de l\'employé.');
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
            
            Log::info('Password changed successfully', [
                'employee_id' => $employee_id,
                'changed_by' => Auth::id() ?? 'unknown'
            ]);
            
            return redirect()->back()->with('success', 'Mot de passe changé avec succès.');
            
        } catch (ValidationException $e) {
            // Validation errors - let Laravel handle these automatically
            throw $e;
            
        } catch (QueryException $e) {
            Log::error('Database error while changing password', [
                'employee_id' => $employee_id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->withInput()
                ->with('error', 'Erreur de base de données lors du changement de mot de passe.');
                
        } catch (Exception $e) {
            Log::error('Unexpected error while changing password', [
                'employee_id' => $employee_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->withInput()
                ->with('error', 'Une erreur inattendue s\'est produite lors du changement de mot de passe.');
        }
    }

}
