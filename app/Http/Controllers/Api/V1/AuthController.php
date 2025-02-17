<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group Autenticação
 *
 * APIs para autenticação de usuários
 */

class AuthController extends Controller
{
    /**
     * Login do usuário
     * 
     * Retorna um token JWT para autenticação nas demais rotas
     * 
     * @unauthenticated
     * 
     * @bodyParam email string required Email do usuário. Example: user@example.com
     * @bodyParam password string required Senha do usuário. Example: password123
     * 
     * @response 200 {
     *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *   "token_type": "bearer",
     *   "expires_in": 3600
     * }
     * 
     * @response 401 {
     *   "message": "Credenciais inválidas"
     * }
     */
    public function login(LoginRequest $request)
    {
        //JWT Auth
        $token = JWTAuth::attempt($request->validated());

        //Response
        if($token){
            return response()->json([
                "token" => $token
            ], 200);
        }else{
            return response()->json([
                "message" => "Credenciais inválidas!"
            ], 401);
        }
    }
}
