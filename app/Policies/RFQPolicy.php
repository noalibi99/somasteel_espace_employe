<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rfq;
use App\Models\PurchaseRequest;

class RfqPolicy
{
    /**
     * Determine whether the user can view the purchase dashboard (list of approved PRs).
     * This isn't directly tied to an Rfq model instance, so it's a general ability.
     */
    public function viewPurchaseDashboard(User $user): bool
    {
        return $user->isPurchase() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isPurchase() || $user->isAdmin() || $user->isDirector(); // Directeurs pourraient vouloir voir les Rfqs
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Rfq $rfq): bool
    {
        // Purchase, Admin, Director can view.
        // Optionally, the user who created the original PurchaseRequest if relevant
        if ($user->isPurchase() || $user->isAdmin() || $user->isDirector()) {
            return true;
        }
        // return $user->id === $rfq->purchaseRequest->user_id; // Si le demandeur initial peut voir le Rfq
        return false;
    }

    /**
     * Determine whether the user can create models.
     * On passe PurchaseRequest pour vérifier son statut.
     */
    public function create(User $user, PurchaseRequest $purchaseRequest): bool
    {
        return ($user->isPurchase() || $user->isAdmin()) && $purchaseRequest->status === PurchaseRequest::STATUS_APPROVED;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Rfq $rfq): bool
    {
        // Seulement si le Rfq est encore en brouillon ou non clôturé
        return ($user->isPurchase() || $user->isAdmin()) && $rfq->status !== Rfq::STATUS_CLOSED;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Rfq $rfq): bool
    {
        // Peut-être seulement si c'est un brouillon
        return ($user->isPurchase() || $user->isAdmin()) && $rfq->status === Rfq::STATUS_DRAFT;
    }

    // Add other policy methods like restore, forceDelete if needed
}
