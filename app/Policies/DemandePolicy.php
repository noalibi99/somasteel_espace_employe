<?php

namespace App\Policies;

use App\Models\User;

class DemandePolicy
{
    /**
     * Create a new policy instance.
     */
    
    // public function view(User $user, DemandeCg $demandeCg)
    // {
    //     // Check if the user is an admin and should see all
    //     if ($user->isResponsable() && $this->isAcceptedByResp($user, $demandeCg)) {
    //         return true;
    //     }

    //     if ($user->isDirecteur() && $this->isAcceptedByDir($user, $demandeCg)) {
    //         return true;
    //     }

    //     if ($user->isRH() && $this->isAcceptedByRH($user, $demandeCg)) {
    //         return true;
    //     }

    //     if ($this->isAcceptedByRH($user, $demandeCg)) {
    //         return true;
    //     }

    //     // Check if the user owns the demande
    //     if ($this->dmOwnerOuv($demandeCg->d_id)) {
    //         if (
    //             ($user->isResponsable() && $this->isAcceptedByResp($user, $demandeCg)) ||
    //             ($user->isDirecteur() && $this->isAcceptedByDir($user, $demandeCg)) ||
    //             ($user->isRH() && !$this->isAcceptedByDir($user, $demandeCg))
    //         ) {
    //             return false;
    //         }
    //     } elseif ($this->dmOwnerResp($demandeCg->d_id) && $user->isResponsable()) {
    //         return false;
    //     } elseif ($this->dmOwnerDir($demandeCg->d_id)) {
    //         if ($user->isDirecteur() || ($user->isRH() && $this->isAcceptedByRH($user, $demandeCg))) {
    //             return false;
    //         }
    //     }

    //     // Check if the demande is refused
    //     if ($this->areRefused($demandeCg->d_id)) {
    //         return false;
    //     }

    //     return true; // Enable by default if none of the conditions are met
    // }
}