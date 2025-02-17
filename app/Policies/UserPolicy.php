<?php

namespace App\Policies;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\HasJWTAuthorization;

class UserPolicy
{
    use HasJWTAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $authenticatedUser, User $targetUser): bool
    {
        return $this->isAdmin() || $authenticatedUser->id === $targetUser->id;
    }

    public function store(User $authenticatedUser)
    {
        return $authenticatedUser->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authenticatedUser, User $targetUser): bool
    {
        return $this->isAdmin() || $authenticatedUser->id === $targetUser->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authenticatedUser, User $targetUser): bool
    {
        return $this->isAdmin() || $authenticatedUser->id === $targetUser->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(): bool
    {
        return $this->isAdmin();

    }
}
