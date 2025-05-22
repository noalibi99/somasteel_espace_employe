<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PurchaseOrder;
use App\Models\Rfq;

class PurchaseOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPurchase() || $user->isDirector() || $user->isAdmin() || $user->isComptable();
    }

    public function view(User $user, PurchaseOrder $purchaseOrder): bool
    {
        if ($user->isPurchase() || $user->isDirector() || $user->isAdmin() || $user->isComptable()) {
            return true;
        }
        // Le demandeur original via Rfq pourrait aussi voir le PO
        return $user->id === $purchaseOrder->rfq->purchaseRequest->user_id;
    }

    // On autorise la création d'un PO si le Rfq a une offre sélectionnée
    public function create(User $user, Rfq $rfq): bool
    {
        return ($user->isPurchase() || $user->isAdmin()) &&
                $rfq->selected_offer_id &&
                $rfq->status === Rfq::STATUS_SELECTION_DONE;
    }

    public function update(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Peut-être seulement si en brouillon ou en attente d'approbation
        // return ($user->isPurchase() || $user->isAdmin()) &&
        //        in_array($purchaseOrder->status, [PurchaseOrder::STATUS_DRAFT, PurchaseOrder::STATUS_PENDING_APPROVAL]);

        return ($user->isPurchase() || $user->isAdmin());
    }

    public function delete(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Seulement si en brouillon
        return ($user->isPurchase() || $user->isAdmin()) && $purchaseOrder->status === PurchaseOrder::STATUS_DRAFT;
    }

    public function sendToSupplier(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Si approuvé ou directement si pas de workflow d'approbation interne
        return ($user->isPurchase() || $user->isAdmin()) &&
                in_array($purchaseOrder->status, [PurchaseOrder::STATUS_DRAFT, PurchaseOrder::STATUS_APPROVED]);
    }

    // Ajoutez d'autres actions comme approveInternal, markAsAcknowledged, etc.
}
