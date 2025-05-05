<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Demande;
use App\Models\Demandes_Conge;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('sufficient_conge', function ($attribute, $value, $parameters, $validator) {
            $user = auth()->user();
            $solde_conge = $user->solde_conge; // Assuming 'solde_conge' is a field in your user model

            // Get the start and end dates from the request data
            $start_date = $validator->getData()['date_debut'];
            $end_date = $validator->getData()['date_fin'];

            // Calculate the difference in days
            $diffInDays = strtotime($end_date) - strtotime($start_date);
            $diffInDays = round($diffInDays / (60 * 60 * 24));

            // Compare the difference with the solde_conge
            // @dd($diffInDays, $solde_conge);
            return abs($diffInDays) <= $solde_conge && abs($diffInDays) >= 0;
        });
        
        Validator::extend('has_demande', function ($attribute, $value, $parameters, $validator){
           //get matricule
            $user = User::where('matricule', $value)->first();
            if($user){
                $matricule = $user->matricule;
            };
            // @dd($user && $user->hasDemandes());
            return $user && !$user->hasDemandes();
        });
    
    }
}