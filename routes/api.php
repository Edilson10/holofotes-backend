<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CultureController;
use App\Http\Controllers\FlavorsAirController;
use App\Http\Controllers\InnovationController;
use App\Http\Controllers\ScheduleController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    //Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::group(['middleware' => 'api'], function () {
    //user
    Route::resource('/users', UserController::class);
    //culture
    Route::resource('/culture', CultureController::class);
    //Business
    Route::resource('/business', BusinessController::class);
    //Innovation
    Route::resource('/innovation', InnovationController::class);
    //FlavorsAir
    Route::resource('/flavorsAir', FlavorsAirController::class);
    //Schedule
    Route::resource('/schedule', ScheduleController::class);
});
