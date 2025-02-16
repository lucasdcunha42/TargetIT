<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    //API de Perfil (GET)
    public function profile()
    {
        $userData = auth()->user()->load('address');

        return response()->json([
            "status" => "success",
            "message" => "Profile Data",
            "user" => $userData
        ], 200);
    }

    //API de Atualização de Token (GET)
    public function refreshToken()
    {
        $newToken = auth()->refresh();

        return response()->json([
            "status" => "success",
            "message" => "New access token",
            "token" => $newToken
        ], 200);

    }

    //API de Logout (GET)
    public function logout()
    {
        auth()->logout();

        return response()->json([
            "status" => "success",
            "message" => "Logout realizado com sucesso!"
        ], 200);
    }

    public function updateUser(UpdateUserRequest $request)
    {
        // Obtendo o usuário autenticado
        $user = auth()->user();

        // Atualiza apenas os campos enviados
        $user->update($request->validated());

        // Atualizando ou criando o endereço
        $address = $user->address;
        if ($address) {
            // Atualiza o endereço existente
            $address->update([
                'logradouro'  => $request->has('logradouro') ? $request->logradouro : $address->logradouro,
                'numero'      => $request->has('numero') ? $request->numero : $address->numero,
                'bairro'      => $request->has('bairro') ? $request->bairro : $address->bairro,
                'complemento' => $request->has('complemento') ? $request->complemento : $address->complemento,
                'cep'         => $request->has('cep') ? $request->cep : $address->cep,
            ]);
        } else {
            // Se o endereço não existir, cria um novo
            $address = $user->address()->create([
                'logradouro'  => $request->logradouro,
                'numero'      => $request->numero,
                'bairro'      => $request->bairro,
                'complemento' => $request->complemento,
                'cep'         => $request->cep,
            ]);
        }

        return response()->json([
            "status"  => "success",
            "data"    => $user->load('address'),
            "message" => "Usuário e endereço atualizados com sucesso!"
        ], 200);
    }

    public function deleteUser()
    {
        $user = auth()->user(); // Obtém o usuário autenticado

        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "Usuário não encontrado!"
            ], 404);
        }

        $user->delete(); // Soft delete

        return response()->json([
            "status" => "success",
            "message" => "Usuário deletado com sucesso!"
        ], 200);
    }
}
