<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Demande;
use App\Models\Demandes_conge;

class DemandesController extends Controller
{
    
    public function __construct()
    {
        //$this->middleware('auth'); or i can use the Route::middleware
    }

    public function index(){
        // $demandesFromDB = Demandes_conge::all();
        $user = auth()->user();
        $demandesCongeFromDB = null;
        // $demandesCongeFromDB = DB::table('DCINFO')
        //                             ->where('to_resp', '=', $user->matricule)
        //                             ->orWhere('to_dir', '=', $user->matricule)
        //                             ->get();
        if ($user->isAdmin() || $user->isRH()) {
            //admin vision global
            $demandesCongeFromDB = DB::table('dcinfo')->paginate(5);
        }elseif ($user->isResponsable()) {
            //responsable vas voire les demande didier a lui
            $demandesCongeFromDB = DB::table('dcinfo')
                ->where('to_resp', '=', $user->matricule)
                ->orWhere('user_id', '=', $user->id)
                ->paginate(5);
            // @dd($demandesCongeFromDB, $user->matricule);
        }elseif($user->isDirecteur()){
            //directeur vas voire les demande didier a lui
            $demandesCongeFromDB = DB::table('dcinfo')
                ->where('to_dir', '=', $user->matricule)
                ->orWhere('user_id', '=', $user->id)
                ->paginate(5);
        }else{//ouvirer
            $demandesCongeFromDB = DB::table('dcinfo')
                ->where('user_id', '=', $user->id)
                ->paginate(5);
        }
        // @dd($demandesCongeFromDB, $demandesCongeFromDB);
        // $demandesFromDB = Demande::where(); specify wich data for each type of users
        // $verifierDemande = new Demandes_Conge;
        return view('demandes/index', [
            'demandesConge' => $demandesCongeFromDB, //demandes to display
            'verifierDC' => new Demandes_conge, //demande instance
            'demande' => new Demande,
            'currentUser' => $user //user instance
        ]); 
    }
}