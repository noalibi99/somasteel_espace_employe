<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PurchaseRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchaseRequestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, PurchaseRequest $request)
    {
        return $user->id === $request->user_id ||
               $user->isDirecteur($user->id);
    }

    public function approve(User $user, PurchaseRequest $request)
    {
        return $user->isDirecteur($user->id);
    }
    public function viewAny(User $user)
{
    return $user->isDirecteur();
}

}
