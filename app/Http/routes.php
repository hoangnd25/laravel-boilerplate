<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'PostController@index');

Route::get('/api/posts', 'Api\PostController@getAll');
Route::post('/api/posts', 'Api\PostController@createOrUpdate');
Route::get('/api/posts/{id}', 'Api\PostController@getById');
Route::delete('/api/posts/{id}', 'Api\PostController@removeById');

Route::get('/api/tags', 'Api\TagController@getAll');
Route::post('/api/tags', 'Api\TagController@createOrUpdate');
Route::get('/api/tags/{id}', 'Api\TagController@getById');
Route::delete('/api/tags/{id}', 'Api\TagController@removeById');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
