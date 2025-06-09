<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prénom',
        'matricule',
        'fonction',
        'service',
        'projet',
        'type',
        'solde_conge',
        'responsable_hiarchique',
        'directeur',
        'email',
        'equipe_id',
        'shift_id',
        'password',
    ];
    public function equipes()
    {
        return $this->belongsTo(Equipe::class);
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }
    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
    public function hasDemandes()
    {
        return $this->demandes()->whereNotIn('status', ['Validé', 'Refusé'])->exists();
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    
    public function isAdmin(){
        return $this->type === 'administrateur';
    }
    
    //check with arg and without if not need to (current user by default)
    
    public function isRH($specificId = null){
        if($specificId !== null){
            $user = User::find($specificId);
            return $user && $user->type === 'rh' || $user->isAdmin();
        } else {
            return $this->type === 'rh' || $this->isAdmin();
        }
    }

    public function isResponsable($specificId = null){
        if($specificId !== null){
            $user = User::find($specificId);
            return $user && $user->type === 'responsable';
        } else {
            return $this->type === 'responsable';
        }
    }  
    
    public function isDirecteur($specificId = null){
        if($specificId !== null){
            $user = User::find($specificId);
            return $user && $user->type === 'directeur';
        } else {
            return $this->type === 'directeur';
        }
    }

    public function isOuvrier($specificId = null){
        if($specificId !== null){
            $user = User::find($specificId);
            return $user && $user->type === 'ouvrier';
        } else {
            return $this->type === 'ouvrier';
        }
    }

    public function isMagasinier()
    {
        return $this->type === 'magasinier';
    }

    public function isDirector()
    {
        return $this->type === 'directeur';
    }

    public function isPurchase()
    {
        return strtolower($this->service) === 'achat';
    }

    public function isComptable()
    {
        return strtolower($this->service) === 'comptabilite';
    }


    // Relations
    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function validatedRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'validator_id');
    }

    public function deleteProfilePicture()
    {
        if ($this->profile_picture) {
            // Delete the profile picture file from storage
            Storage::delete('profiles_imgs/' . $this->profile_picture);
            
            // Remove the profile picture path from the user record
            $this->profile_picture = null;
            $this->save();
            
            return true;
        }
        
        return false;
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}