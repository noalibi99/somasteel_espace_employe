<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Pour générer le numéro de PO

class PurchaseOrder extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft'; // BDC en cours de préparation
    const STATUS_PENDING_APPROVAL = 'pending_approval'; // Si une validation interne est nécessaire avant envoi
    const STATUS_APPROVED = 'approved'; // Approuvé en interne
    const STATUS_SENT_TO_SUPPLIER = 'sent_to_supplier'; // Envoyé au fournisseur
    const STATUS_ACKNOWLEDGED = 'acknowledged'; // Confirmé par le fournisseur
    const STATUS_PARTIALLY_DELIVERED = 'partially_delivered';
    const STATUS_FULLY_DELIVERED = 'fully_delivered';
    const STATUS_COMPLETED = 'completed'; // Livré et facturé/payé
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'po_number',        // Numéro unique du Bon de Commande
        'rfq_id',           // Lien vers le Rfq
        'offer_id',         // Lien vers l'Offre sélectionnée
        'supplier_id',
        'user_id',          // Utilisateur qui a créé le BDC (Service Achat)
        'status',
        'order_date',
        'expected_delivery_date_global', // Date de livraison globale attendue
        'shipping_address',
        'billing_address',
        'payment_terms',
        'notes',            // Notes générales sur le BDC
        // 'approved_by_id', // Si validation interne
        // 'approved_at',
        'sent_to_supplier_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date_global' => 'date',
        // 'approved_at' => 'datetime',
        'sent_to_supplier_at' => 'datetime',
    ];

    protected $appends = ['total_po_price', 'status_label', 'status_color'];

    // Relation avec Rfq (déjà présente, mais confirmons)
    public function rfq()
    {
        return $this->belongsTo(Rfq::class);
    }

    // Relation avec l'Offre qui a mené à ce BDC
    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    // Relation avec Fournisseur (déjà présente)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relation avec l'utilisateur créateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Un BDC a plusieurs lignes d'articles
    public function purchaseOrderLines()
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    // Relations pour les phases suivantes (déjà présentes)
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }


    // Générer un numéro de PO unique lors de la création
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($purchaseOrder) {
            if (empty($purchaseOrder->po_number)) {
                // Format: PO-ANNEE-MOIS-IDINCREMENTAL (ou autre format souhaité)
                // Pour cet exemple, un identifiant unique simple.
                // Vous pouvez rendre cela plus robuste.
                $prefix = 'PO-' . date('Ym');
                $lastPO = self::where('po_number', 'like', $prefix . '%')->orderBy('po_number', 'desc')->first();
                $nextId = 1;
                if ($lastPO) {
                    $lastIdNumber = (int) Str::afterLast($lastPO->po_number, '-');
                    $nextId = $lastIdNumber + 1;
                }
                $purchaseOrder->po_number = $prefix . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
            if (empty($purchaseOrder->order_date)) {
                $purchaseOrder->order_date = now();
            }
        });
    }

    public function getTotalPoPriceAttribute(): float
    {
        return $this->purchaseOrderLines()->sum('total_price');
    }

    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_DRAFT => 'Brouillon',
            self::STATUS_PENDING_APPROVAL => 'Attente Approbation Interne',
            self::STATUS_APPROVED => 'Approuvé',
            self::STATUS_SENT_TO_SUPPLIER => 'Envoyé au Fournisseur',
            self::STATUS_ACKNOWLEDGED => 'Confirmé par Fournisseur',
            self::STATUS_PARTIALLY_DELIVERED => 'Partiellement Livré',
            self::STATUS_FULLY_DELIVERED => 'Totalement Livré',
            self::STATUS_COMPLETED => 'Terminé',
            self::STATUS_CANCELLED => 'Annulé',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_PENDING_APPROVAL => 'warning',
            self::STATUS_APPROVED => 'info',
            self::STATUS_SENT_TO_SUPPLIER => 'primary',
            self::STATUS_ACKNOWLEDGED => 'teal', // Nécessite CSS si couleur non bootstrap
            self::STATUS_PARTIALLY_DELIVERED => 'info',
            self::STATUS_FULLY_DELIVERED => 'success',
            self::STATUS_COMPLETED => 'dark',
            self::STATUS_CANCELLED => 'danger',
        ][$this->status] ?? 'dark';
    }
}
