<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    const STATUS_PENDING_CONFIRMATION = 'pending_confirmation'; // En attente de validation par le magasinier
    const STATUS_PARTIALLY_RECEIVED = 'partially_received'; // Certains articles reçus, d'autres en attente
    const STATUS_FULLY_RECEIVED = 'fully_received'; // Tous les articles de cette livraison spécifique sont confirmés
    const STATUS_COMPLETED_WITH_ISSUES = 'completed_with_issues'; // Reçu, mais avec des problèmes (quantité, qualité)

    protected $fillable = [
        'purchase_order_id',
        'delivery_reference',   // Numéro de Bon de Livraison (BL) du fournisseur
        'delivery_date',
        'received_by_id',     // ID de l'utilisateur magasinier
        'status',
        'notes',                // Notes générales sur la livraison (ex: état du colis)
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    protected $appends = ['status_label', 'status_color'];


    // Relation avec PurchaseOrder (déjà présente, on vérifie)
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Un BL du fournisseur peut concerner plusieurs articles (lignes)
    public function deliveryLines()
    {
        return $this->hasMany(DeliveryLine::class);
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }

    // La relation 'article()' n'est plus pertinente ici,
    // car une livraison concerne plusieurs articles via DeliveryLine.

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_PENDING_CONFIRMATION => 'En attente de confirmation',
            self::STATUS_PARTIALLY_RECEIVED => 'Partiellement Reçu',
            self::STATUS_FULLY_RECEIVED => 'Totalement Reçu',
            self::STATUS_COMPLETED_WITH_ISSUES => 'Reçu avec problèmes',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_PENDING_CONFIRMATION => 'warning',
            self::STATUS_PARTIALLY_RECEIVED => 'info',
            self::STATUS_FULLY_RECEIVED => 'success',
            self::STATUS_COMPLETED_WITH_ISSUES => 'danger',
        ][$this->status] ?? 'dark';
    }
}
