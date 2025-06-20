<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rfq extends Model
{
    use HasFactory;

    protected $table = 'rfqs';

    const STATUS_DRAFT = 'draft';
    const STATUS_SENT = 'sent';
    const STATUS_RECEIVING_OFFERS = 'receiving_offers'; // Nouveau: Après envoi, en attente/réception d'offres
    const STATUS_PROCESSING_OFFERS = 'processing_offers'; // Maintenu: En cours de comparaison/décision
    const STATUS_SELECTION_DONE = 'selection_done'; // Nouveau: Une offre a été sélectionnée
    const STATUS_CLOSED = 'closed'; // RFQ terminé (commande passée ou annulé)

    const STATUS_ORDER_CREATED = 'order_created';  //BDC créé à partir de ce RFQ

    protected $fillable = [
        'purchase_request_id',
        'status',
        'notes',
        'deadline_for_offers',
        'selected_offer_id', // Nouveau champ
    ];

    protected $casts = [
        'deadline_for_offers' => 'datetime',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'rfq_supplier');
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    // Relation pour l'offre sélectionnée
    public function selectedOffer()
    {
        return $this->belongsTo(Offer::class, 'selected_offer_id');
    }

    protected $appends = ['status_label', 'status_color'];

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_SENT => 'Envoyé aux fournisseurs',
            self::STATUS_RECEIVING_OFFERS => 'Réception des offres', // Nouveau
            self::STATUS_PROCESSING_OFFERS => 'Comparaison des offres', // Libellé ajusté
            self::STATUS_SELECTION_DONE => 'Offre sélectionnée',
            self::STATUS_ORDER_CREATED => 'Bon de Commande Créé',
            self::STATUS_CLOSED => 'Clôturé',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_SENT => 'info',
            self::STATUS_RECEIVING_OFFERS => 'primary', // Nouveau
            self::STATUS_PROCESSING_OFFERS => 'warning',
            self::STATUS_SELECTION_DONE => 'success', // Nouveau
            self::STATUS_ORDER_CREATED => 'purple',
            self::STATUS_CLOSED => 'dark', // Changé pour se distinguer
        ][$this->status] ?? 'dark';
    }

    // Méthode pour vérifier si une offre est sélectionnée
    public function hasSelectedOffer(): bool
    {
        return !is_null($this->selected_offer_id);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
