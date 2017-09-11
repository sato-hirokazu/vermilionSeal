<?php

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

Route::get('/', function () {
    return view('top');
});

Route::group(['prefix' => 'api/'. env('API_VERSION', 'v0')], function ($app) {
    $app->get('posts', 'PostController@index');
    $app->get('posts/{id: \d+}', 'PostController@show'); // {param: 正規表現} とすることで正規表現に一致するパラメータのみ許可する。
    $app->post('posts', 'PostController@create');
    $app->put('posts/{id: \d+}', 'PostController@update');
    $app->get('categories', 'CategoryController@index');
});