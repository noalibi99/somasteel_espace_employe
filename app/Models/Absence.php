<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;
    protected $table = 'absences';
    protected $fillable = [
        'demande_id',
        'date_demandé',
        'motif',
        'piece_jointe',
        'to_responsable_id',
        'absence_type'
    ];

    public function getAbsences(){
        self::get();
    }
    public function getAbsenceFor($responsable_id){
        self::where('to_responsable_id', $responsable_id)->get();
    }
    public function setAbsnece($demande_id, $date_demandé, $motif, $piece_jointe, $to_responsable_id, $absence_type){

    }
    // public function setDecision($decision){
    //     $this->update(['approved_at' => now()]);
    //     if ($decision == 'Validé') {
    //         $this->demande()->update(['status']);
    //     }
    // }

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}
