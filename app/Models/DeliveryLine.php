<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_id',
        'purchase_order_line_id', // Lien vers la ligne du BDC concernée
        'article_id',             // Article reçu
        'quantity_received',
        'notes',                  // Ex: état à la réception, non-conformité partielle
        'is_confirmed',           // Si le magasinier confirme cette ligne spécifique
    ];

    protected $casts = [
        'quantity_received' => 'integer',
        'is_confirmed' => 'boolean',
    ];

    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function purchaseOrderLine()
    {
        return $this->belongsTo(PurchaseOrderLine::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
