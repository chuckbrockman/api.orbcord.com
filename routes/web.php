<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// App (Customer) Routes
// Route::domain(config('app.url_app'))->name('app.')->group(function() {

    Route::middleware('auth')->group(function() {

    });

    Route::get('/', function() {
        return 'app route';
    });
// });



Route::get('/', function () {
    return "base";
    return view('welcome');
});




Route::get('/pagespeed', function () {
    \App\Services\GooglePagespeedInsights::run('https://tcecleaning.com/', 'PERFORMANCE');

    return;
});

Route::get('/gtmetrix', function () {
    \App\Services\Gtmetrix::test('https://tcecleaning.com/');

    return;
});

Route::get('/foo', function() {
    return view('layouts.partials.pagespeed');
});
