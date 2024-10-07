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


Route::group([
    'prefix' => LaravelLocalization::setLocale(), // Set the language prefix
    'middleware' => [
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
        'admin', // Custom admin middleware
    ]
], function () {
  
  	Route::get('/', [\App\Http\Controllers\Admin\Dashboard::class, 'index'])->name('dashboard');

    Route::resource('tvs', \App\Http\Controllers\Admin\TvController::class);
    Route::resource('institutions', \App\Http\Controllers\Admin\InstitutionController::class);
    Route::resource('ads', \App\Http\Controllers\Admin\AdController::class);
    Route::get('/client-reports', [\App\Http\Controllers\Admin\ClientReportController::class, 'index'])->name('client.reports');
    // TV Display Times Management
    Route::resource('tv_display_times', \App\Http\Controllers\Admin\TvDisplayTimeController::class);

    // Routes for Ads
    Route::post('/ads/activate/{id}', [\App\Http\Controllers\Admin\AdController::class, 'activateAd'])->name('ads.activate');
    Route::post('/ads/updatetvs/{id}', [\App\Http\Controllers\Admin\AdController::class, 'updatescheduleads'])->name('ads.updatetvs');

    // Routes for TVs
    Route::post('/tvs/activate/{id}', [\App\Http\Controllers\Admin\TvController::class, 'activateTv'])->name('tvs.activate');
    Route::get('/tv/{tv_id}/ad-order', [\App\Http\Controllers\Admin\AdOrderController::class,'show'])->name('tv.ad-order');
    Route::post('/admin/tvs/ads/update-order', [\App\Http\Controllers\Admin\AdOrderController::class, 'updateOrder'])->name('admin.ads.updateOrder');

    // Route to assign TVs after creating an ad
    Route::get('ads/{ad}/choose-tvs', [\App\Http\Controllers\Admin\AdController::class, 'chooseTvs'])->name('ads.chooseTvs');
    Route::post('ads/{ad}/store-tvs', [\App\Http\Controllers\Admin\AdController::class, 'storeTvs'])->name('ads.storeTvs');

  	Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
  
      //videos
    Route::get('/video', [\App\Http\Controllers\Admin\VideoController::class, 'showForm'])->name('video.index');
    Route::post('/video/process', [\App\Http\Controllers\Admin\VideoController::class, 'processUpload'])->name('processUpload');
  
    // Route::delete('/ads/delete-schedule-day/{scheduleId}', [\App\Http\Controllers\Admin\AdController::class, 'deleteScheduleDay'])->name('ads.deleteScheduleDay');
    Route::delete('/{ad}/delete-schedule/{schedule}', [\App\Http\Controllers\Admin\AdController::class, 'deleteSchedule'])->name('ads.deleteschedule');

    // Add a single day for an ad on a TV
    Route::post('/{ad}/add-single-day', [\App\Http\Controllers\Admin\AdController::class, 'addSingleDay'])->name('ads.addsingleday');
    // Route::get('/dashboard', function () {
    //     return view('dashboard');
    // })->name('dashboard');
});

Route::get('/status', function () {
    // Simulate a successful response (TV is online)
    return response()->json(['status' => 'online'], 200);
});

// Language switch route
Route::get('lang/{lang}', function ($lang) {
    session(['locale' => $lang]); // Store the selected language in session
    return redirect()->back();    // Redirect back to the previous page
})->name('lang.switch');

Route::get('/don_not_have_permission', function () {
    return view('admin.don_not_have_per');
})->name('don_not_have_permission');
