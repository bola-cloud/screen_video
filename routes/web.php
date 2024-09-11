<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [\App\Http\Controllers\Admin\Dashboard::class, 'index'])->name('dashboard');

Route::resource('tvs', \App\Http\Controllers\Admin\TvController::class);
Route::resource('ads', \App\Http\Controllers\Admin\AdController::class);

// Route to assign TVs after creating an ad
Route::get('ads/{ad}/choose-tvs', [\App\Http\Controllers\Admin\AdController::class, 'chooseTvs'])->name('ads.chooseTvs');
Route::post('ads/{ad}/store-tvs', [\App\Http\Controllers\Admin\AdController::class, 'storeTvs'])->name('ads.storeTvs');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
});
