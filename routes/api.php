<?php

use App\Http\Controllers\user\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\user\PasswordResetController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

// public routes

Route::post('/user/register', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/send-reset-password-email', [PasswordResetController::class, 'send_reset_password_email']);

Route::post('/user/reset-password/{token}', [PasswordResetController::class, 'reset']);


//protected routes

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/logout', [UserController::class, 'logOut']);
    Route::get('/user/loggedUser', [UserController::class, 'loggedUser']);
    Route::post('/user/changepassword', [UserController::class, 'changepassword']);
    Route::post('/user/updaterofile', [UserController::class, 'profileUpdate']);
});
