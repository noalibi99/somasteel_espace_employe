<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'supplier_id',
        // 'price', // On supprime le prix global ici, il sera calculé
        'terms',
        'valid_until',
        'notes',
        'attachment_path',
    ];

    protected $casts = [
        'valid_until' => 'date',
    ];

    // Accesseur pour calculer le prix total de l'offre à partir de ses lignes
    protected $appends = ['total_offer_price'];

    public function rfq()
    {
        return $this->belongsTo(RFQ::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Une offre a plusieurs lignes d'articles/prix
    public function offerLines()
    {
        return $this->hasMany(OfferLine::class);
    }

    // Accesseur pour calculer le prix total de l'offre
    public function getTotalOfferPriceAttribute(): float
    {
        return $this->offerLines()->sum('total_price');
    }
}
