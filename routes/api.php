<?php

use App\Http\Controllers\Api\ChefController;
use App\Http\Controllers\Api\TabController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function () {

    Route::group(['prefix' => '/chef'], function () {
        Route::get('/todo-list', [ChefController::class, 'getTodoList']);
        Route::post('/prepare', [ChefController::class, 'markFoodPrepared']);
    });

    Route::group(['prefix' => '/tab'], function () {
        Route::get('/status/{table}', [TabController::class, 'getTabFotTable']);
        Route::get('/invoice/{table}', [TabController::class, 'getInvoiceForTable']);
        Route::post('/open', [TabController::class, 'openTab']);
        Route::post('/order', [TabController::class, 'placeOrder']);
        Route::post('/serve', [TabController::class, 'markServed']);
        Route::post('/close', [TabController::class, 'closeTab']);
    });

});
