<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\api as Api;

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

Route::middleware('json')->group(function () {

    Route::get('/', [Api::class, 'home']);
    Route::post('/contact', [Api::class, 'contact']);
    Route::get('/properties', [Api::class, 'properties']);
    Route::get('/property/{id}', [Api::class, 'property']);
    Route::get('/users', [Api::class, 'users']);
    Route::get('/user/{id}', [Api::class, 'user']);
    Route::post('/login', [Api::class, 'login']);
    Route::post('/register', [Api::class, 'register']);
    Route::post('verify/{id}/{hash}', [Api::class, 'verifyNotification'])->name('verification.verify');
    Route::post('/forgot', [Api::class, 'forgot']);
    Route::post('/password-reset', [Api::class, 'passwordReset'])->name('password.reset');

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::post('/logout', [Api::class, 'logout']);
        Route::post('email-verify-resent', [Api::class, 'resendVerificationEmail'])->name('verification.send');
    });
});
