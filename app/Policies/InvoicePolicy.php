<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;
use App\Models\PurchaseOrder;

class InvoicePolicy
{
    // Qui peut voir le dashboard des factures
    public function viewAccountingDashboard(User $user): bool
    {
        return $user->isComptable() || $user->isPurchase() || $user->isAdmin();
    }

    public function viewAny(User $user): bool
    {
        return $user->isComptable() || $user->isPurchase() || $user->isDirector() || $user->isAdmin();
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->isComptable() || $user->isPurchase() || $user->isDirector() || $user->isAdmin()) {
            return true;
        }
        // Le demandeur original pourrait voir les factures de ses commandes
        return $user->id === $invoice->purchaseOrder->rfq->purchaseRequest->user_id;
    }

    // Qui peut créer une facture pour un PO donné
    public function create(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Le comptable peut créer une facture si le PO est au moins envoyé/confirmé/livré
        return ($user->isComptable() || $user->isAdmin()) &&
               in_array($purchaseOrder->status, [
                   PurchaseOrder::STATUS_SENT_TO_SUPPLIER,
                   PurchaseOrder::STATUS_ACKNOWLEDGED,
                   PurchaseOrder::STATUS_PARTIALLY_DELIVERED,
                   PurchaseOrder::STATUS_FULLY_DELIVERED
               ]);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        // Peut modifier une facture tant qu'elle n'est pas payée ou annulée
        return ($user->isComptable() || $user->isAdmin()) &&
               !in_array($invoice->status, [Invoice::STATUS_PAID, Invoice::STATUS_CANCELLED]);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        // Peut-être seulement si en attente de validation et par un admin
        return $user->isAdmin() && $invoice->status === Invoice::STATUS_PENDING_VALIDATION;
    }

    // Qui peut valider une facture
    public function validateInvoice(User $user, Invoice $invoice): bool
    {
        return ($user->isComptable() || $user->isAdmin()) &&
                $invoice->status === Invoice::STATUS_PENDING_VALIDATION;
    }

    // Qui peut enregistrer un paiement
    public function recordPayment(User $user, Invoice $invoice): bool
    {
        return ($user->isComptable() || $user->isAdmin()) &&
                in_array($invoice->status, [Invoice::STATUS_VALIDATED, Invoice::STATUS_PARTIALLY_PAID]);
    }
}
