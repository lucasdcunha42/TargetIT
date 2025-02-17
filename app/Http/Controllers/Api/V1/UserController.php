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
    public function show(User $user)
    {
        return response()->json([
            "status" => "success",
            "message" => "Profile Data",
            "user" => $user
        ], 200);
    }

    public function store(CreateUserRequest $request, User $user)
    {
        $this->authorize('store', $user);

        $user = User::create($request->validated());

        return response()->json([
            "status" => "success",
            "message" => "User Created Successfully!",
            "user" => $user
        ], 201);
    }


    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        // Atualiza apenas os campos enviados
        $user->update($request->validated());

        return response()->json([
            "status"  => "success",
            "data"    => $user->load('address'),
            "message" => "Usuário atualizado com sucesso!"
        ], 200);
    }

    public function destroy(User $user)
    {

        $this->authorize('delete', $user);

        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "Usuário não encontrado!"
            ], 404);
        }

        $user->delete(); // Softdelete

        return response()->json([
            "status" => "success",
            "message" => "Usuário deletado com sucesso!"
        ], 200);
    }
}
