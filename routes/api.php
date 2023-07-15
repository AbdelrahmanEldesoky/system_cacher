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

Route::get('traceorder/{id}',  'ApiController@traceid');

Route::get('traceorder',  'ApiController@traceorder');

Route::post('traceupdate/{id}',  'ApiController@traceupdate');


Route::get('table',  'ApiController@table');

Route::get('category',  'ApiController@category');

Route::get('product/{id}',  'ApiController@product');

Route::get('orderget/{client}',  'ApiController@get');

Route::post('orderstore/{client}',  'ApiController@store');

Route::get('orderedit/{client}',  'ApiController@edit');

Route::post('orderupdate/{client}/{order}',  'ApiController@update');





