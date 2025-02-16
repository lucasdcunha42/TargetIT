<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\RestoreUserRequest;
use App\Models\User;

class AdminController extends Controller
{
    public function store(CreateUserRequest $request) {

        $user = User::create($request->validated());

        return response()->json([
            "status" => "success",
            "message" => "User created successfully!",
            "user" => $user,
        ], 200);

    }

    public function restoreUser(RestoreUserRequest $request)
    {
        

        // Buscar usuário pelo email incluindo os registros deletados
        $user = User::withTrashed()
            ->where('email', $request->email)
            ->first();

        // Verificar se usuário existe e está realmente deletado
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuário não encontrado!'
            ], 404);
        }

        if (!$user->trashed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Este usuário já está ativo!'
            ], 400);
        }

        try {
            // Restaurar usuário
            $user->restore();

            return response()->json([
                'status' => 'success',
                'message' => 'Usuário restaurado com sucesso!',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'restored_at' => now()
                    ]
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erro ao restaurar usuário!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
