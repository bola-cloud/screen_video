<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Illuminate\Support\Facades\Artisan;
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

Route::group([
    'prefix' => LaravelLocalization::setLocale(), // Set the language prefix
    'middleware' => [
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
        'admin', // Custom admin middleware
    ]
], function () {

    // Admin dashboard route
    Route::get('/', [\App\Http\Controllers\Admin\Dashboard::class, 'index'])->name('dashboard');

    // Resources for TVs, Ads, and Display Times
    Route::resource('tvs', \App\Http\Controllers\Admin\TvController::class);
    Route::resource('ads', \App\Http\Controllers\Admin\AdController::class);
    Route::resource('tv_display_times', \App\Http\Controllers\Admin\TvDisplayTimeController::class);

    // Additional routes for activating ads and TVs
    Route::post('/ads/activate/{id}', [\App\Http\Controllers\Admin\AdController::class, 'activateAd'])->name('ads.activate');
    Route::post('/ads/updatetvs/{id}', [\App\Http\Controllers\Admin\AdController::class, 'updatescheduleads'])->name('ads.updatetvs');

    Route::post('/tvs/activate/{id}', [\App\Http\Controllers\Admin\TvController::class, 'activateTv'])->name('tvs.activate');

    // Routes for handling ad order management
    Route::get('/tv/{tv_id}/ad-order', [\App\Http\Controllers\Admin\AdOrderController::class, 'show'])->name('tv.ad-order');
    Route::post('/admin/tvs/ads/update-order', [\App\Http\Controllers\Admin\AdOrderController::class, 'updateOrder'])->name('admin.ads.updateOrder');

    // Routes for assigning TVs to ads
    Route::get('ads/{ad}/choose-tvs', [\App\Http\Controllers\Admin\AdController::class, 'chooseTvs'])->name('ads.chooseTvs');
    Route::post('ads/{ad}/store-tvs', [\App\Http\Controllers\Admin\AdController::class, 'storeTvs'])->name('ads.storeTvs');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    //videos
    Route::get('/video', [\App\Http\Controllers\Admin\VideoController::class, 'showForm'])->name('video.index');
    Route::post('/video/process', [\App\Http\Controllers\Admin\VideoController::class, 'processUpload'])->name('processUpload');
});

// Language switch route
Route::get('lang/{lang}', function ($lang) {
    session(['locale' => $lang]); // Store the selected language in session
    return redirect()->back();    // Redirect back to the previous page
})->name('lang.switch');

Route::get('/don_not_have_permission', function () {
    return view('admin.don_not_have_per');
})->name('don_not_have_permission');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
});
