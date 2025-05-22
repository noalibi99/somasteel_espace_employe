<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;
// Pas besoin de Response ou Auth ici si non utilisés directement dans les méthodes.

class SupplierPolicy
{
    /**
     * Determine whether the user can view any models.
     * Qui peut voir la liste des fournisseurs ?
     */
    public function viewAny(User $user): bool
    {
        // Par exemple, tous les utilisateurs du service achat, RH, admin, directeurs
        return $user->isPurchase() || $user->isRH() || $user->isAdmin() || $user->isDirector();
    }

    /**
     * Determine whether the user can view the model.
     * Qui peut voir les détails d'un fournisseur spécifique ?
     */
    public function view(User $user, Supplier $supplier): bool
    {
        // Généralement les mêmes que viewAny, ou tous les utilisateurs authentifiés si l'info n'est pas sensible.
        // Le "return true;" que vous aviez est très permissif.
        return $user->isPurchase() || $user->isRH() || $user->isAdmin() || $user->isDirector();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Service achat, RH ou Admin peuvent créer des fournisseurs
        return $user->isPurchase() || $user->isRH() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Supplier $supplier): bool
    {
        // Service achat, RH ou Admin peuvent mettre à jour
        return $user->isPurchase() || $user->isRH() || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Supplier $supplier): bool
    {
        // Généralement, seuls les admins ou des rôles très spécifiques avec des vérifications
        // (comme celle ajoutée dans le contrôleur pour empêcher la suppression si lié)
        return $user->isAdmin() || ($user->isRH() && !$supplier->rfqs()->exists() && !$supplier->offers()->exists() && !$supplier->purchaseOrders()->exists());
    }

    /**
     * Determine whether the user can restore the model.
     * (Si vous utilisez SoftDeletes sur le modèle Supplier)
     */
    // public function restore(User $user, Supplier $supplier): bool
    // {
    //     return $user->isAdmin();
    // }

    /**
     * Determine whether the user can permanently delete the model.
     * (Si vous utilisez SoftDeletes)
     */
    // public function forceDelete(User $user, Supplier $supplier): bool
    // {
    //     return $user->isAdmin(); // Soyez très prudent avec cette permission
    // }
}
