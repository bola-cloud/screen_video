<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\TriggerVideo;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TvController;
 
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
Route::get('/tvs/{tv_id}/{order}', [AdController::class, 'publishNextAd']);
Route::get('/trigger-video/{tv_id}/{order}', [TriggerVideo::class, 'triggerVideo']);
Route::get('/get_client/ads', [ClientController::class, 'getClientAds']);
Route::get('/fetch-ads/{tv_id}/{advertisement_id}/{date}', [AdController::class, 'getAds']);


Route::get('/screenstv', [TvController::class, 'index']);
Route::get('/time_tv/{tv_id}', [TvController::class, 'tvs_time']);
Route::get('/end_time_tv/{tv_id}', [TvController::class, 'tv_end_time']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/ads/client/{client_id}', [ClientController::class, 'getAdsByClient']);

Route::get('/tvs/{ad_id}', [ClientController::class, 'getTvsByAd']);
Route::get('/fetch-ads/{tv_id}/{advertisement_id}/{date}', [ClientController::class, 'getAds']);
Route::post('/tv-status/{id}', [TvController::class, 'updateStatus']);

// Protected route (requires authentication via Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});