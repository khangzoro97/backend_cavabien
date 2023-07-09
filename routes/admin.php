<?php

use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Resources\AdminResource;
use Illuminate\Http\Request;
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

Route::middleware(['auth:sanctum', 'auth.admin', 'permission.admin'])->group(function () {
    Route::get('/', function (Request $request) {
        return new AdminResource($request->user());
    });

    Route::delete('logout', [AdminController::class, 'logout']);
});

Route::post('register', [AdminController::class, 'register']);
Route::post('login', [AdminController::class, 'login']);
