<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;

/**
 * @group User Management
 *
 * APIs for managing users in the system
 */
class UserController extends Controller
{
    /**
     * Show User
     *
     * Returns the data of a specific user.
     *
     * @urlParam user integer required User ID. Example: 1
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
     *    "message": "User not found!"
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
     * Create User
     *
     * Creates a new user in the system.
     *
     * @bodyParam name string required User's name. Example: John Doe
     * @bodyParam email string required User's email. Example: john@example.com
     * @bodyParam password string required User's password (minimum 8 characters). Example: password123
     * @bodyParam phone string required User's phone. Example: 11999999999
     * @bodyParam cpf string required User's CPF. Example: 12345678900
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
     *            "The email field is already in use."
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
     * Update User
     *
     * Updates the data of an existing user.
     *
     * @urlParam user integer required User ID. Example: 1
     * @bodyParam name string optional User's name. Example: John Doe
     * @bodyParam email string optional User's email. Example: john@example.com
     * @bodyParam phone string optional User's phone. Example: 11999999999
     * @bodyParam cpf string optional User's CPF. Example: 12345678900
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
     *            "street": "Example Street",
     *            "number": "123",
     *            "complement": "Apt 45",
     *            "neighborhood": "Downtown",
     *            "city": "São Paulo",
     *            "state": "SP",
     *            "zipcode": "01001000"
     *        }
     *    },
     *    "message": "User updated successfully!"
     * }
     *
     * @response 403 {
     *    "message": "This action is unauthorized."
     * }
     *
     * @response 404 {
     *    "status": "error",
     *    "message": "User not found!"
     * }
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update($request->validated());

        return response()->json([
            "status"  => "success",
            "data"    => $user->load('address'),
            "message" => "User updated successfully!"
        ], 200);
    }

    /**
     * Remove User
     *
     * Removes a user from the system (soft delete).
     *
     * @urlParam user integer required User ID. Example: 1
     *
     * @response 200 {
     *    "status": "success",
     *    "message": "User deleted successfully!"
     * }
     *
     * @response 403 {
     *    "message": "This action is unauthorized."
     * }
     *
     * @response 404 {
     *    "status": "error",
     *    "message": "User not found!"
     * }
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if (!$user) {
            return response()->json([
                "status" => "error",
                "message" => "User not found!"
            ], 404);
        }

        $user->delete();

        return response()->json([
            "status" => "success",
            "message" => "User deleted successfully!"
        ], 200);
    }
}
