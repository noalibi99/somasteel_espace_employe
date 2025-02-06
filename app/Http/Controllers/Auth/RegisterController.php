<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'matricule' => ['required', 'number'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    
    // protected function update(){
    //     //$data = request()->all()
    //     $maticule = request()->matricule;
    //     $password = request()->password;
    //     $user = User::find($maticule);
    //     if($user && !User::where('password', $password)->pluck('password')->first()){ //CHECK AUTH INFO
            
    //         User::update([
    //             'password' => Hash::make($password) //or $data['password']
    //         ]);
    //         return redirect('/login')->with('success','Password créer avec succés');
    //     }else {
    //         return redirect()->back()->withInput()->withErrors(['matricule', 'matricule incorrect']);
    //     }
    // }
    
    // protected function create(array $data)
    // {
    //     return User::create([
    //         'matricule' => $data['matricule'],
    //         // 'email' => $data['email'],
    //         'password' => Hash::make($data['password']),
    //     ]);
    // }
    
}