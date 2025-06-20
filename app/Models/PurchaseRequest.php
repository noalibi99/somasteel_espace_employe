<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequest extends Model
{
    use HasFactory;

    // Statuts existants et nouveaux
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_RFQ_IN_PROGRESS = 'rfq_in_progress'; // Nouveau statut
    const STATUS_ORDERED = 'ordered'; // Pour plus tard
    const STATUS_PROCESSED = 'processed'; // Statut final


    protected $fillable = [
        'user_id',
        'validator_id',
        'description',
        'motifRejet',
        'status',
        'validated_at'
    ];

    protected $casts = [
        'validated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    public function lines()
    {
        return $this->hasMany(Line::class);
    }

    // Une PurchaseRequest peut avoir un RFQ
    public function rfq()
    {
        return $this->hasOne(RFQ::class);
    }

    protected $appends = ['status_label', 'status_color'];

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_PENDING => 'En attente',
            self::STATUS_APPROVED => 'Approuvée (Attente Achat)', // Libellé mis à jour
            self::STATUS_REJECTED => 'Rejetée',
            self::STATUS_RFQ_IN_PROGRESS => 'RFQ en cours', // Nouveau
            self::STATUS_ORDERED => 'Commandée', // Pour plus tard
            self::STATUS_PROCESSED => 'Traitée',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'primary', // Changé pour se distinguer de 'approved' par directeur
            self::STATUS_REJECTED => 'danger',
            self::STATUS_RFQ_IN_PROGRESS => 'info', // Nouveau
            self::STATUS_ORDERED => 'purple', // Pour plus tard (nécessite CSS)
            self::STATUS_PROCESSED => 'success', // Changé car 'approved' est déjà pris
        ][$this->status] ?? 'dark';
    }
}
