<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; // Import the Auth facade
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


use Illuminate\Validation\ValidationException;

use App\Models\Demandes_conge;
use App\Models\User;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        //$this->middleware('auth'); or i can use the Route::middleware
    }

    /**
     * Show the application dashboard.
     *
     */
    public function index()
    {
        $userInfo = auth()->user();
        $userResp = $userInfo->select('nom', 'prénom')
        ->where('matricule', '=', $userInfo->responsable_hiarchique ? $userInfo->responsable_hiarchique : $userInfo->directeur )
        ->first();

        $dcinfo = DB::select('select v_resp, v_dir, v_rh, status, raison_refus, id, nom_pdf  from dcinfo where user_id = ? order by id desc', [$userInfo->id]);
        $nomPDF = !empty($dcinfo) ? ($dcinfo[0]->nom_pdf ?? 'NOT_FOUND') : 'Vous n\'avez pas encore de demandes';
        $dcinfoRecord = $dcinfo[0] ?? (object)[
            'v_resp' => null,
            'v_dir' => null,
            'v_rh' => null,
            'status' => null,
            'id' => null,
            'nom_pdf' => null,
            'raison_refus' => null
        ];

        return view('home',[    'userInfo'=> $userInfo,
                                'userResp'=> $userResp,
                                'file'=> $nomPDF,
                                'dcinfo' => $dcinfoRecord,
                            ]);
    }

    public function updateEmail()
    {
        // dd($user_id, $request->email);
        try {
            $userForm = request();

            $userForm->validate([
                'email' => 'nullable|email',
            ], [
                'error' => 'Veuillez entrer une adresse email valide et non utilisé.',
            ]);

            $user = Auth::user();

            $user->update([
                'email'=> $userForm->email,
            ]);
            // session()->flash('success','Email enregistré avec succès.');
            return redirect()->route('home')->with('success', 'Email enregistré avec succès.');
        }catch (Exception $e) {
            // session()->put('error', $e->getMessage() . 'Verifier votre email que vous avais saisis.');
            return redirect()->route('home')->with('error', 'Veuillez entrer une adresse email valide et non déjà utilisé.');
        }
    }
    public function updatePicture(Request $request)
    {
        try {
            $request->validate([
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $user = Auth::user();
            if ($request->file('profile_picture')) {
                if ($user->profile_picture) {
                    $user->deleteProfilePicture();
                }
                $file = $request->file('profile_picture');
                $filename = $user->matricule . '_' . $user->nom . '_img.' . $file->getClientOriginalExtension();

                // Store the file in the specified directory
                $file->storeAs('profiles_imgs', $filename);

                // Update the user's profile_picture field with the path
                $user->profile_picture = $filename;
                $user->save();
            }
            return redirect()->back()->with('success', 'Photo de profil modifiée avec succès.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Image incompatible (max taille 2Mo, format: jpeg, png, jpg)');
        }
    }
    public function deleteProfilePicture()
    {
        $user = Auth::user();
        if ($user->deleteProfilePicture()) {
            return redirect()->back()->with('success', 'Photo profile supprimée avec succès.');
        } else {
            return redirect()->back()->with('error', 'Impossible de supprimer la photo de profil.');
        }
    }
    public function getProfileImage($filename)
    {
        $path = storage_path('app/profiles_imgs/' . $filename);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }
    public function getApp() {
        // Log the route access
        Log::info('Download app route accessed', [
            'user_id' => auth()->id(), // Log the user ID if authenticated
            'ip_address' => request()->ip(), // Log the IP address of the user
            'timestamp' => now()->toDateTimeString() // Log the timestamp
        ]);
        Session::flash('success', 'App download started successfully.');

        // Return the file download
        return Storage::disk('public')->download('somasteel_android_app.apk');
    }
}
