<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
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
