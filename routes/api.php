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

Route::prefix('v1')->namespace('api\v1')->group(function (){
    Route::get('/index', 'ArticleController@index');
    Route::get('/articles/{article}', 'ArticleController@show');

    Route::post('/login', 'UserController@login');
    Route::post('/register', 'UserController@register');

    // Authentication Routes...
    Route::middleware('auth:api')->group(function (){
        Route::post('/articles', 'ArticleController@store');
        Route::put('/articles/{article}', 'ArticleController@update');
        Route::delete('/articles/{article}', 'ArticleController@destroy');
        Route::post('/articles/{article}/comment', 'ArticleController@storeComment');

        Route::get('/logout', 'UserController@logout');
    });
});
