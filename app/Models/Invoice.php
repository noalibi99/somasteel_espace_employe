<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    const STATUS_PENDING_VALIDATION = 'pending_validation'; // En attente de validation par la compta
    const STATUS_VALIDATED = 'validated';           // Validée, en attente de paiement
    const STATUS_PARTIALLY_PAID = 'partially_paid';   // Paiement partiel effectué
    const STATUS_PAID = 'paid';                   // Totalement payée
    const STATUS_REJECTED = 'rejected';             // Facture rejetée (non conforme)
    const STATUS_CANCELLED = 'cancelled';           // Facture annulée

    protected $fillable = [
        'purchase_order_id',
        'supplier_id',          // Redondant si on a purchase_order_id, mais peut être utile
        'invoice_number',       // Numéro de facture du fournisseur
        'invoice_date',
        'due_date',             // Date d'échéance
        'amount_ht',            // Montant Hors Taxe
        'vat_amount',           // Montant TVA
        'total_amount',         // Montant TTC (amount_ht + vat_amount)
        'amount_paid',          // Montant déjà payé sur cette facture
        'status',
        'payment_date',         // Date du dernier paiement
        'payment_method',
        'payment_reference',    // Référence du paiement (chèque, virement)
        'notes',                // Notes de la compta sur cette facture
        'document_path',        // Scan de la facture fournisseur
        'payment_document_path',// Scan de la preuve de règlement
        'validated_by_id',
        'validated_at',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'datetime',
        'validated_at' => 'datetime',
        'amount_ht' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    protected $appends = ['status_label', 'status_color', 'is_fully_paid'];

    // Relation avec PurchaseOrder (déjà présente)
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    // Relation avec Supplier (déjà présente)
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by_id');
    }

    // Calculer le montant restant à payer
    public function getAmountDueAttribute(): float
    {
        return (float) $this->total_amount - (float) $this->amount_paid;
    }

    public function getIsFullyPaidAttribute(): bool
    {
        return $this->amount_due <= 0 && $this->total_amount > 0; // S'assurer que c'est payé et qu'il y avait un montant
    }


    protected static function boot()
    {
        parent::boot();
        static::saving(function ($invoice) {
            // S'assurer que total_amount est calculé si ht et tva sont présents
            if (is_null($invoice->total_amount) && !is_null($invoice->amount_ht) && !is_null($invoice->vat_amount)) {
                $invoice->total_amount = $invoice->amount_ht + $invoice->vat_amount;
            }
            // Si amount_paid est mis à jour et égal au total, marquer comme payée
            if ($invoice->isDirty('amount_paid') && $invoice->total_amount > 0 && $invoice->amount_paid >= $invoice->total_amount) {
                if ($invoice->status !== self::STATUS_PAID) { // Éviter de re-trigger si déjà payé
                    $invoice->status = self::STATUS_PAID;
                    if (empty($invoice->payment_date)) {
                        $invoice->payment_date = now();
                    }
                }
            } elseif ($invoice->isDirty('amount_paid') && $invoice->total_amount > 0 && $invoice->amount_paid > 0 && $invoice->amount_paid < $invoice->total_amount) {
                 $invoice->status = self::STATUS_PARTIALLY_PAID;
            }

        });
    }


    public function getStatusLabelAttribute()
    {
        return [
            self::STATUS_PENDING_VALIDATION => 'Attente Validation Compta',
            self::STATUS_VALIDATED => 'Validée (Attente Paiement)',
            self::STATUS_PARTIALLY_PAID => 'Partiellement Payée',
            self::STATUS_PAID => 'Payée',
            self::STATUS_REJECTED => 'Rejetée',
            self::STATUS_CANCELLED => 'Annulée',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            self::STATUS_PENDING_VALIDATION => 'warning',
            self::STATUS_VALIDATED => 'info',
            self::STATUS_PARTIALLY_PAID => 'primary',
            self::STATUS_PAID => 'success',
            self::STATUS_REJECTED => 'danger',
            self::STATUS_CANCELLED => 'secondary',
        ][$this->status] ?? 'dark';
    }
}
