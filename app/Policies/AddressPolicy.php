<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use App\Traits\HasJWTAuthorization;

class AddressPolicy
{
    use HasJWTAuthorization;
    
    /**
     * Determine whether the user can create models.
     */
    public function store(User $authenticatedUser, User $targetUser): bool
    {
        return $this->isAdmin() || $authenticatedUser->id === $targetUser->id;
    }
}
