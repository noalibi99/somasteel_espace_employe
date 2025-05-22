<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Delivery;
use App\Models\PurchaseOrder;

class DeliveryPolicy
{
    // Qui peut voir le dashboard des livraisons (PO en attente de livraison)
    public function viewDeliveryDashboard(User $user): bool
    {
        return $user->isMagasinier() || $user->isPurchase() || $user->isAdmin();
    }

    public function viewAny(User $user): bool
    {
        // Magasinier, Achat, Admin, Directeur peuvent voir toutes les livraisons
        return $user->isMagasinier() || $user->isPurchase() || $user->isAdmin() || $user->isDirector();
    }

    public function view(User $user, Delivery $delivery): bool
    {
        // Idem que viewAny, ou plus restrictif si besoin
        return $user->isMagasinier() || $user->isPurchase() || $user->isAdmin() || $user->isDirector() ||
               $user->id === $delivery->purchaseOrder->rfq->purchaseRequest->user_id; // Demandeur original
    }

    // Qui peut créer une réception pour un PO donné
    public function create(User $user, PurchaseOrder $purchaseOrder): bool
    {
        // Le magasinier peut créer une réception si le PO est envoyé ou partiellement livré
        return ($user->isMagasinier() || $user->isAdmin()) &&
               in_array($purchaseOrder->status, [
                   PurchaseOrder::STATUS_SENT_TO_SUPPLIER,
                   PurchaseOrder::STATUS_ACKNOWLEDGED,
                   PurchaseOrder::STATUS_PARTIALLY_DELIVERED
               ]);
    }

    // Qui peut mettre à jour/confirmer une livraison (ex: si elle est en 'pending_confirmation')
    public function update(User $user, Delivery $delivery): bool
    {
        return ($user->isMagasinier() || $user->isAdmin()) &&
               $delivery->status === Delivery::STATUS_PENDING_CONFIRMATION;
    }

    public function delete(User $user, Delivery $delivery): bool
    {
        // Supprimer une livraison peut être complexe à cause des impacts sur les stocks/statuts.
        // Peut-être seulement si en pending_confirmation et par un admin.
        return $user->isAdmin() && $delivery->status === Delivery::STATUS_PENDING_CONFIRMATION;
    }
}
