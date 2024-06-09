<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => 'auth:api'], function() {
    Route::group(['prefix' => 'market-indexes'], function() {
        Route::get('/', 'MarketIndexController@index');
        Route::post('/', 'MarketIndexController@store');
        Route::put('/{index}', 'MarketIndexController@update')->where(['index' => '[0-9]+']);
        Route::post('upload', 'MarketIndexController@uploadMarketIndexesFile');
        Route::get('/bulkValues', 'MarketIndexController@bulkIndexValues');
        Route::post('/{index}/values', 'MarketIndexController@storeValues')->where(['index' => '[0-9]+']);
        Route::get('/{index}/values', 'MarketIndexController@indexValues')->where(['index' => '[0-9]+']);
    });

    Route::apiResource('/cargoes', 'CargoController');
    Route::apiResource('/countries', 'CountryController');
    Route::get('/organizations', 'OrganizationController@index');
    Route::apiResource('/ports', 'PortController');
});
