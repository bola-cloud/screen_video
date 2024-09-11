<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdController;

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
Route::get('/tvs/{tv_id}/publish-next-ad', [AdController::class, 'triggerAdScheduling'])->name('publish-next-ad');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
