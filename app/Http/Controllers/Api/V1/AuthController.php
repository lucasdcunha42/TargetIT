<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @group Authentication
 *
 * APIs for user authentication
 */

class AuthController extends Controller
{
    /**
     * User Login
     *
     * Returns a JWT token for authentication in other routes
     *
     * @unauthenticated
     *
     * @bodyParam email string required User's email. Example: user@example.com
     * @bodyParam password string required User's password. Example: password123
     *
     * @response 200 {
     *   "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
     *   "token_type": "bearer",
     *   "expires_in": 3600
     * }
     *
     * @response 401 {
     *   "message": "Invalid credentials"
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
                "message" => "Invalid credentials!"
            ], 401);
        }
    }
}
