<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\RestoreUserRequest;
use App\Http\Resources\UserRestoreResource;
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
        $user = User::onlyTrashed()
            ->where('email', $request->email)
            ->firstOrFail();

        $user->restore();

        return response()->json([
            'message' => 'User restored successfully',
            'data' => new UserRestoreResource($user)
        ], 200);
    }


}
