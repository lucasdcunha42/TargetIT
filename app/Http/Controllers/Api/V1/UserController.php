<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

/**
 * @group Gerenciamento de Usuários
 *
 * APIs para gerenciar usuários no sistema
 */
class UserController extends Controller
{
    /**
     * Exibir Usuário
     *
     * Retorna os dados de um usuário específico.
     *
     * @urlParam user integer required ID do usuário. Example: 1
     *
     * @response 200 {
     *    "status": "success",
     *    "message": "Profile Data",
     *    "user": {
     *        "id": 1,
     *        "name": "John Doe",
     *        "email": "john@example.com",
     *        "phone": "11999999999",
     *        "cpf": "12345678900",
     *        "created_at": "2024-02-17T10:00:00.000000Z",
     *        "updated_at": "2024-02-17T10:00:00.000000Z"
     *    }
     * }
     * 
     * @response 404 {
     *    "status": "error",
     *    "message": "Usuário não encontrado!"
     * }
     */
    public function show(User $user)
    {
        return response()->json([
            "status" => "success",
            "message" => "Profile Data",
            "user" => $user
        ], 200);
    }

    /**
     * Criar Usuário
     *
     * Cria um novo usuário no sistema.
     *
     * @bodyParam name string required Nome do usuário. Example: John Doe
     * @bodyParam email string required Email do usuário. Example: john@example.com
     * @bodyParam password string required Senha do usuário (mínimo 8 caracteres). Example: password123
     * @bodyParam phone string required Telefone do usuário. Example: 11999999999
     * @bodyParam cpf string required CPF do usuário. Example: 12345678900
     *
     * @response 201 {
     *    "status": "success",
     *    "message": "User Created Successfully!",
     *    "user": {
     *        "id": 1,
     *        "name": "John Doe",
     *        "email": "john@example.com",
     *        "phone": "11999999999",
     *        "cpf": "12345678900",
     *        "created_at": "2024-02-17T10:00:00.000000Z",
     *        "updated_at": "2024-02-17T10:00:00.000000Z"
     *    }
     * }
     * 
     * @response 422 {
     *    "message": "The given data was invalid.",
     *    "errors": {
     *        "email": [
     *            "O campo email já está sendo utilizado."
     *        ]
     *    }
     * }
     * 
     * @response 403 {
     *    "message": "This action is unauthorized."
     * }
     */
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

    /**
     * Atualizar Usuário
     *
     * Atualiza os dados de um usuário existente.
     *
     * @urlParam user integer required ID do usuário. Example: 1
     * @bodyParam name string optional Nome do usuário. Example: John Doe
     * @bodyParam email string optional Email do usuário. Example: john@example.com
     * @bodyParam phone string optional Telefone do usuário. Example: 11999999999
     * @bodyParam cpf string optional CPF do usuário. Example: 12345678900
     *
     * @response 200 {
     *    "status": "success",
     *    "data": {
     *        "id": 1,
     *        "name": "John Doe",
     *        "email": "john@example.com",
     *        "phone": "11999999999",
     *        "cpf": "12345678900",
     *        "created_at": "2024-02-17T10:00:00.000000Z",
     *        "updated_at": "2024-02-17T10:00:00.000000Z",
     *        "address": {
     *            "id": 1,
     *            "user_id": 1,
     *            "street": "Rua Exemplo",
     *            "number": "123",
     *            "complement": "Apto 45",
     *            "neighborhood": "Centro",
     *            "city": "São Paulo",
     *            "state": "SP",
     *            "zipcode": "01001000"
     *        }
     *    },
     *    "message": "Usuário atualizado com sucesso!"
     * }
     * 
     * @response 403 {
     *    "message": "This action is unauthorized."
     * }
     * 
     * @response 404 {
     *    "status": "error",
     *    "message": "Usuário não encontrado!"
     * }
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update($request->validated());

        return response()->json([
            "status"  => "success",
            "data"    => $user->load('address'),
            "message" => "Usuário atualizado com sucesso!"
        ], 200);
    }

    /**
     * Remover Usuário
     *
     * Remove um usuário do sistema (soft delete).
     *
     * @urlParam user integer required ID do usuário. Example: 1
     *
     * @response 200 {
     *    "status": "success",
     *    "message": "Usuário deletado com sucesso!"
     * }
     * 
     * @response 403 {
     *    "message": "This action is unauthorized."
     * }
     * 
     * @response 404 {
     *    "status": "error",
     *    "message": "Usuário não encontrado!"
     * }
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "Usuário não encontrado!"
            ], 404);
        }

        $user->delete();

        return response()->json([
            "status" => "success",
            "message" => "Usuário deletado com sucesso!"
        ], 200);
    }
}