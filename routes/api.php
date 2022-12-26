<?php

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

Route::apiResources([
    'TKDFVBNODEEEOIYH' => App\Http\Controllers\Api\Cfcontest\CpraController::class,
]);

Route::prefix('v1')->name('v1.')->group(function() {
    Route::prefix('audit')->name('audit.')->group(function() {
        Route::apiResources([
            'performance' => App\Http\Controllers\Api\v1\Audits\PerformanceScoreController::class,
        ]);

        Route::get('/audit/pagespeed', [ App\Http\Controllers\Api\v1\Audits\PageSpeedController::class, 'index' ]);
    });
    Route::get('/audit/pagespeed', [ App\Http\Controllers\Api\v1\Audits\PageSpeedController::class, 'index' ]);
});

// Route::get('/pagespeed', function () {

//     $webhook = \App\Models\WebhookData::findOrFail(8);

//     dd(json_decode($webhook->body), true);


//     \App\Services\GooglePagespeedInsights::run('https://tcecleaning.com/', 'PERFORMANCE');

//     return;
// });

// Route::get('/gtmetrix', function () {
//     set_time_limit(0);

//     $arr = \App\Services\Gtmetrix::run('https://orbcord.com/');

//     return response()->json($arr);
// });


// Route::get('/foo', function() {
//     // dd(request()->header('HTTP_REFERER'));
//     return view('emails.performance-score.send-score');
// });


Route::get('/', function() {
    return 'in the api';
});
