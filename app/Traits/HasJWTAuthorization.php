<?php

namespace App\Traits;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

trait HasJWTAuthorization
{
    /**
     * Verifica se o usuário tem role admin no JWT
     *
     * @return bool
     */
    protected function isAdmin(): bool
    {
        try {
            return $this->getUserRole() === 'admin';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtém a role do usuário do JWT
     *
     * @return string|null
     */
    protected function getUserRole(): ?string
    {
        try {
            $token = JWTAuth::parseToken();
            $payload = $token->getPayload();
            return $payload->get('role');
        } catch (TokenExpiredException $e) {
            throw new \Exception('Token expirado');
        } catch (TokenInvalidException $e) {
            throw new \Exception('Token inválido');
        } catch (JWTException $e) {
            throw new \Exception('Token não encontrado');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Verifica se o usuário tem uma role específica
     *
     * @param string $role
     * @return bool
     */
    protected function hasRole(string $role): bool
    {
        try {
            return $this->getUserRole() === $role;
        } catch (\Exception $e) {
            return false;
        }
    }
}
