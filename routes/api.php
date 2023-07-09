<?php

use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum', 'auth.user', 'permission.user'])->group(function () {
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    Route::delete('logout', [UserController::class, 'logout']);
});

Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::post('register',  'register');
    Route::post('login', 'login');
    Route::post('forgot-password', 'sendMailResetPassword');
    Route::get('reset-password/{token}', 'verifyTokenResetPassword')
        ->name('password.reset')
        ->middleware('signed');
    Route::post('reset-password', 'resetPassword');
});
