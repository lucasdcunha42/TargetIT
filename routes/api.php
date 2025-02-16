<?php

use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {

    //Admin Routes
    Route::post("register",[AdminController::class, "store"])->middleware(['auth:api', 'role:admin']);
    Route::patch("user/restore", [UserController::class, "restoreUser"]);


    //Auth Routes
    Route::post("login",[AuthController::class, "login"]);

    Route::group(['middleware' => 'auth:api'], function(){
        Route::get("profile",[UserController::class, "profile"]);
        Route::get("refresh",[UserController::class, "refreshToken"]);
        Route::get("logout",[UserController::class, "logout"]);

        Route::put( "user/update",[UserController::class, "updateUser"]);
        Route::delete("user/delete", [UserController::class, "deleteUser"]);
    });

});



