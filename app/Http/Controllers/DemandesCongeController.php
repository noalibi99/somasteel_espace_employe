<?php

namespace App\Http\Controllers;

use App\Mail\DemandeCongeMail;
use App\Mail\DemandeRefusedMail;
use App\Mail\DemandeVliderMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\Demande;
use App\Models\Demandes_conge;

class DemandesCongeController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth'); or i can use the Route::middleware
    }

    public function index(){
        
        return view('demandes/index');
    }

    public function store(){
        //dd(request()->all());
        $demandeCongeForm = request();
        $demandeCongeForm->validate([
            'nom' => 'required|max:255',
            'prénom' => 'required|max:255',
            'matricule' => 'required|numeric|has_demande',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut|sufficient_conge',
            'motif' => 'required|max:255',
        	'Autre' => 'nullable|string|max:255',
            // Add other validation rules for other fields if needed
        ], [
            'has_demande' => "Matricule n'existe pas ou il a déjà demandé",
            'date_fin.after_or_equal' => 'La date de fin doit être supérieur ou égale à la date de début!!',
            'sufficient_conge' => 'Le nombre de jours de congé demandé dépasse le solde de congé disponible.',
        ]);
        //@dd($demandeCongeForm->matricule, User::where('matricule','=' , $demandeCongeForm->matricule)->first()->matricule);

        if ($demandeCongeForm->matricule = User::where('matricule','=' , $demandeCongeForm->matricule)->first()->matricule){
            try {
                $demande = Demande::create([
                    'type' => 'Demande Congé',
                    'user_id' => auth()->user()->id,
                ]);

                $demandeConge = $demande->demandesConge()->create([
                    'start_date' => $demandeCongeForm->date_debut,
                    'end_date' => $demandeCongeForm->date_fin,
                    'motif' => $demandeCongeForm->motif,
                    'autre' => $demandeCongeForm->Autre,
                    'to_responsable_id' => Auth::user()->responsable_hiarchique ? Auth::user()->responsable_hiarchique : 0,
                    'to_directeur_id' => Auth::user()->directeur
                ]);
                
                // new demande mail
                    //select users to recieve 
                    $respDirEmail = DB::select('select u.email from users u, dcinfo d where u.matricule IN (d.to_resp ,d.to_dir) AND d.d_id = ?', [$demande->id]);
                    $rhEmail = User::where('type', '=', 'rh')->value('email');
                    $emails = [
                        'respEmail' => isset($respDirEmail[0]->email) ? $respDirEmail[0]->email : null,
                        'dirEmail' => isset($respDirEmail[1]->email) ? $respDirEmail[1]->email : null,
                        'rhEmail' => $rhEmail ?? null
                    ];
                    //envoiyé les email
                    foreach ($emails as $email) {
                        if ($email) {
                            Mail::to($email)->send(new DemandeCongeMail($demandeCongeForm->all()));
                        }
                    }
                    
                    // dd($respDirEmail, $rhEmail, $demandeCongeForm);
                    // $directeurEmail = DB::select('select u.email from users u, dcinfo d where u.matricule = d.to_dir AND d.d_id = ?', [$demande->id]);
                    
            } catch (Exception $e) {
                // Handle the exception
                // For example, log the error and show an error message
                Log::error('Error creating demande congé: ' . $e->getMessage());
                return redirect()->route('demandes.index')->with('error', 'Une erreur s\'est produite lors de la création de la demande Congé.' . $e->getMessage());
            }
        }

        return redirect()->route('demandes.index')->with('success', 'Demande congé créée avec succès'); // Redirecting to demandes.index route
    }

    public function update($demandeCg_id){
        $formDecision = request();
        $demandeCgAdeside = Demandes_conge::find($demandeCg_id);
        $user = auth()->user();
        $demande = $demandeCgAdeside->demande;
        $demandeOwner = $demande->user;

        //@dd($formDecision->refused);
        if($demandeCgAdeside && $formDecision->accepted){
            if ($user->isResponsable()) {
                $demandeCgAdeside->update([
                    'approuvé_responsable' => $formDecision->accepted
                ]);
                return redirect()->route('demandes.index')->with('success', 'Demande acceptée avec succés');
            }elseif ($user->isDirecteur()) {
                $demandeCgAdeside->update([
                    'approuvé_directeur' => $formDecision->accepted
                ]);
                return redirect()->route('demandes.index')->with('success', 'Demande acceptée avec succés');

            }elseif($user->isRH() && !$demandeCgAdeside->isAcceptedByRH($demandeCg_id)){ //accepter ssi n'est pas encore
                $demandeCgAdeside->update([
                    'approuvé_rh' => $formDecision->accepted
                ]);
                // @dd($demande);
                $demande->update([
                    'status' => 'Validé',
                    'updated_at' => now()
                ]);
                //after update
                $dcinfo_nj_dec = DB::select('select dcinfo.nj_decompter from dcinfo where id = ? ', [$demandeCg_id]);
                // @dd($dcinfo);
                $demandeOwner->update([
                    'solde_conge' => \DB::raw('solde_conge - ' . $dcinfo_nj_dec[0]->nj_decompter)
                ]);
                $dcinfo = DB::select('select * from dcinfo where id = ? ', [$demandeCg_id]);
                //after validation
                // $data = DB::select('SELECT ur.nom AS rNom, ur.prénom AS rPrénom, ud.nom AS dNom, ud.prénom AS dPrénom FROM dcinfo d LEFT JOIN users ur ON ur.id = d.to_resp LEFT JOIN users ud ON ud.id = d.to_dir WHERE d.id = ?', [$demandeCg_id]);
                // $solde = User::findOrFail($demandeCgAdeside->d_id)->solde_conge;

                $responsable = DB::select('select u.nom, u.prénom from users u, dcinfo d where u.matricule = d.to_resp AND d.d_id = ?', [$demande->id]);
                $directeur = DB::select('select u.nom, u.prénom from users u, dcinfo d where u.matricule = d.to_dir AND d.d_id = ?', [$demande->id]);

                $pdfData = [
                            'dcinfo' => $dcinfo[0],
                            // 'rNom' => $data[0]->rNom ?? null,
                            // 'rPrénom' => $data[0]->rPrénom ?? null,
                            // 'dNom' => $data[0]->dNom ?? null,
                            'vDate' => $demande->updated_at,
                            'rNom' => $responsable[0]->nom ?? null,
                            'rPrénom' => $responsable[0]->prénom ?? null,
                            'dNom' => $directeur[0]->nom ?? null,
                            'dPrénom' => $directeur[0]->prénom ?? null,
                        ];
                // Debug to ensure it works
                $demandeCgAdeside::generatePDF($pdfData);
                // dd($demandeOwner->email);
                // $demandeOwner = User::find($demandeOwner->id)->first();
                //ddemande owner
            	$dcinfo = DB::select('select * from dcinfo where id = ? ', [$demandeCg_id]);
                $path = storage_path('app/demandes_conge_pdf/' . $dcinfo[0]->nom_pdf ?? 'vous n\'avait pas encore une demandes');
                // dd($path, $demandeOwner->email, $dcinfo[0]->nom_pdf);
                if ($demandeOwner->email) {
                    Mail::to($demandeOwner->email)->send(new DemandeVliderMail($demandeOwner, $path));
                }
                return redirect()->route('demandes.index')->with('success', 'Demande acceptée et générée avec succès.');
                // after validation logic
            }else{
                return redirect()->route('demandes.index')->with('error', 'ERROR!.');
            }
            // return redirect()->route('demandes.index')->with('success', 'Demande acceptée avec succés');
        }elseif($formDecision->refused) {
            //trouver demande conge concerner
            // $demandeConge = DemandeConge::findOrFail($formDecision->refused);
            $formDecision->validate([
                'raison_refus' => 'required|min:10|string',
            ]);
            //selectioner la demande appartir de demandeconge id            // @dd($formDecision->all());
            $demande->update([
                'status' => $formDecision->refused,
                'raison_refus' => $formDecision->raison_refus,
                'updated_at' => now()
            ]);
            
            if ($demandeOwner->email) {
                Mail::to($demandeOwner->email)->send(new DemandeRefusedMail($demandeOwner));
            }
            return redirect()->route('demandes.index')->with('success', 'Demande Refusée avec succés');
        }
        // return redirect()->route('demandes.index');
    }

   public function downloadConge($dc_id)
{
    $user = auth()->user();

    try {
        if ($user->isResponsable() || $user->isDirecteur() || $user->isRH()) {
            $fileRecord = DB::select('select user_id, nom_pdf from dcinfo where id = ? order by id desc limit 1', [$dc_id]);
        } else {
            $fileRecord = DB::select('select user_id, nom_pdf from dcinfo where user_id = ? and id = ? order by id desc limit 1', [$user->id, $dc_id]);
        }

        if (empty($fileRecord)) {
            return redirect()->back()->with('error', "Il n'y a aucun fichier.");
        }

        $fileName = $fileRecord[0]->nom_pdf;
        $path = storage_path('app/demandes_conge_pdf/' . $fileName);

        if (file_exists($path)) {
            session()->flash('success', 'Le fichier a été téléchargé avec succès');
            return response()->download($path);
        } else {
            return redirect()->back()->with('error', "Il n'y a aucun fichier.");
        }
    } catch (Exception $e) {
        Log::error('Error downloading file', ['exception' => $e->getMessage()]);
        return redirect()->back()->with('error', "Une erreur s'est produite lors du téléchargement du fichier.");
    }
}
}