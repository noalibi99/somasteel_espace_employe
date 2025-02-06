<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Demande extends Model
{
    use HasFactory;

    protected $table = 'demandes';
    protected $policies = [
        'App\models\DemandeCg' => 'App\Policies\DemandePolicy',
    ];
    
    protected $fillable = [
        'user_id',
        'type',
        'status',
        'raison_refus'
    ];
    public function areRefused($demandId){
        return $this->findOrFail($demandId)->status == 'Refusé';
    }
    public function areValidated($demandId){
        return $this->findOrFail($demandId)->status == 'Validé';
    }
    
    public function dmOwnerOuv($demandId){
        return (new User)->isOuvrier($this->findOrFail($demandId)->user_id);
    }

    public function dmOwnerResp($demandId){
        return (new User)->isResponsable($this->findOrFail($demandId)->user_id);
    }
    public function dmOwnerDir($demandId){
        $user = new User;
        $demande = $this->findOrFail($demandId);
        return $user->isDirecteur($demande->user_id);
    }
    public function dmOwnerRH($demandId){
        $user = new User;
        $demande = $this->findOrFail($demandId);
        return $user->isRH($demande->user_id);
    }
    public function ToDate($string){
        return \Carbon\Carbon::parse($string);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function demandesConge(){
        return $this->hasOne(Demandes_conge::class, 'demande_id');
    }
    public function absence(){
        return $this->hasOne(Absence::class, 'demande_id');
    }
}