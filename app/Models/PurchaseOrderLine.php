<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'offer_line_id',            // Lien vers la ligne de l'offre sélectionnée
        'article_id',               // Article commandé
        'description',              // Description de l'article commandé
        'quantity_ordered',
        'unit_price',               // Prix unitaire convenu
        'quantity_received',
        'total_price',              // Calculé
        'expected_delivery_date',   // Date de livraison attendue pour cette ligne (optionnel)
        'notes',
    ];

    protected $casts = [
        'quantity_ordered' => 'integer',
        'quantity_received' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'expected_delivery_date' => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function offerLine()
    {
        // La ligne de l'offre à laquelle cette ligne de commande correspond
        return $this->belongsTo(OfferLine::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    protected static function booted()
    {
        static::saving(function ($poLine) {
            if ($poLine->quantity_ordered && $poLine->unit_price) {
                $poLine->total_price = $poLine->quantity_ordered * $poLine->unit_price;
            }
        });
    }

    public function deliveryLines()
    {
        // Une ligne de PO peut avoir plusieurs lignes de livraison (si livraisons partielles multiples)
        return $this->hasMany(DeliveryLine::class);
    }

    // Calcule la quantité totale reçue pour cette ligne de PO
    public function getTotalQuantityReceivedAttribute(): int
    {
        // Sum des quantity_received de toutes les DeliveryLines associées à cette PO Line
        // et qui sont confirmées (si vous avez une logique de confirmation par ligne de livraison)
        // return $this->deliveryLines()->where('is_confirmed', true)->sum('quantity_received');
        // Ou plus simplement, si la quantité est mise à jour directement sur la PO line (moins flexible)
        return $this->quantity_received ?? 0; // Utiliser le champ direct pour l'instant
    }

    public function getQuantityRemainingAttribute(): int
    {
        return $this->quantity_ordered - $this->total_quantity_received;
    }

    public function isFullyDelivered(): bool
    {
        return $this->quantity_remaining <= 0;
    }
}
