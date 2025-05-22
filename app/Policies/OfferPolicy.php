<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Offer;
use App\Models\RFQ;

class OfferPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, RFQ $rfq): bool
    {
        // Service achat, admin, directeur peuvent voir les offres d'un RFQ
        return $user->isPurchase() || $user->isAdmin() || $user->isDirector();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Offer $offer): bool
    {
        return $user->isPurchase() || $user->isAdmin() || $user->isDirector();
    }

    /**
     * Determine whether the user can create models.
     * On passe le RFQ pour vérifier son statut.
     */
    public function create(User $user, RFQ $rfq): bool
    {
        // On peut ajouter des offres tant que le RFQ n'est pas clôturé ou qu'une offre n'est pas déjà sélectionnée
        return ($user->isPurchase() || $user->isAdmin()) &&
               !in_array($rfq->status, [RFQ::STATUS_SELECTION_DONE, RFQ::STATUS_CLOSED]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Offer $offer): bool
    {
        // On peut modifier une offre tant que le RFQ n'est pas clôturé ou qu'une offre n'est pas déjà sélectionnée
        return ($user->isPurchase() || $user->isAdmin()) &&
               !in_array($offer->rfq->status, [RFQ::STATUS_SELECTION_DONE, RFQ::STATUS_CLOSED]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Offer $offer): bool
    {
        return ($user->isPurchase() || $user->isAdmin()) &&
               !in_array($offer->rfq->status, [RFQ::STATUS_SELECTION_DONE, RFQ::STATUS_CLOSED]) &&
               $offer->rfq->selected_offer_id !== $offer->id; // Ne pas supprimer l'offre sélectionnée
    }

    /**
     * Determine whether the user can select an offer for an RFQ.
     * Policy sur le RFQ, pas sur l'Offer directement pour cette action.
     */
    public function selectOffer(User $user, RFQ $rfq): bool
    {
        return ($user->isPurchase() || $user->isAdmin() || $user->isDirector()) && // Ou celui qui doit valider
               $rfq->offers()->exists() && // Il doit y avoir au moins une offre
               !in_array($rfq->status, [RFQ::STATUS_SELECTION_DONE, RFQ::STATUS_CLOSED]);
    }
}
