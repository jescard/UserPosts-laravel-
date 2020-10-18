<?php

use App\Http\Controllers\UserController;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
    'namespace' => 'App\Http\Controllers'

], function () {
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/signup', [UserController::class, 'signup']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::post('/logout', [UserController::class, 'logout']);
});


Route::group([
    'middleware' => 'api',
    'prefix' => 'post',
    'namespace' => 'App\Http\Controllers'

], function () {
    Route::post('/createpost', [UserController::class, 'create']);
    Route::get('/view/{id}', [UserController::class, 'view']);
    Route::get('/view-all', [UserController::class, 'viewAll']);
    Route::post('/delete/{id}', [UserController::class, 'delete']);
});




Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
