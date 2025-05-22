<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'offer_id',
        'purchase_request_line_id', // Lien vers la ligne de la demande d'achat originale
        'article_id',               // Si l'offre est pour un article existant
        'description',              // Description de l'article si non standard ou pour plus de détails
        'quantity_requested',       // Quantité de la demande originale (pour référence)
        'quantity_offered',         // Quantité proposée par le fournisseur
        'unit_price',               // Prix unitaire proposé par le fournisseur pour cet article
        'total_price',              // Calculé (quantity_offered * unit_price)
        'notes',                    // Notes spécifiques à cette ligne d'offre
    ];

    protected $casts = [
        'quantity_requested' => 'integer',
        'quantity_offered' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function purchaseRequestLine()
    {
        // La ligne de la demande d'achat à laquelle cette offre répond
        return $this->belongsTo(Line::class, 'purchase_request_line_id');
    }

    public function article()
    {
        // L'article standard si applicable
        return $this->belongsTo(Article::class);
    }

    // Mutateur pour calculer le prix total automatiquement
    protected static function booted()
    {
        static::saving(function ($offerLine) {
            if ($offerLine->quantity_offered && $offerLine->unit_price) {
                $offerLine->total_price = $offerLine->quantity_offered * $offerLine->unit_price;
            }
        });
    }
}
