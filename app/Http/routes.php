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

Route::group(['middleware' => ['web']], function ()
{
    Route::get('auth/login', 'Auth\AuthController@getLogin')->name('auth.login');
    Route::post('auth/login', 'Auth\AuthController@postLogin')->name('auth.login_check');
    Route::get('auth/logout', 'Auth\AuthController@getLogout')->name('auth.logout');

    Route::get('auth/register', 'Auth\AuthController@getRegister')->name('auth.register');
    Route::post('auth/register', 'Auth\AuthController@postRegister')->name('auth.register_check');

    Route::get('password/email', 'Auth\PasswordController@getEmail')->name('auth.reset_password.request');
    Route::post('password/email', 'Auth\PasswordController@postEmail')->name('auth.reset_password.request_check');
    Route::get('password/reset/{email}/{token}', 'Auth\PasswordController@getReset')->name('auth.reset_password.update');
    Route::post('password/reset', 'Auth\PasswordController@postReset')->name('auth.reset_password.update_check');

    Route::get('/', 'Http\PostController@index')->name('post.list');
    Route::match(['get', 'post'], '/create', 'Http\PostController@createOrEdit')->name('post.create');
    Route::match(['get', 'post'], '/edit/{id}', 'Http\PostController@createOrEdit')->name('post.edit');
    Route::get('/remove/{id}', 'Http\PostController@remove')->name('post.remove');
});

Route::group(['middleware' => ['api']], function ()
{
    Route::get('/api/posts', 'Api\PostController@getAll');
    Route::post('/api/posts', 'Api\PostController@createOrUpdate');
    Route::get('/api/posts/{id}', 'Api\PostController@getById');
    Route::delete('/api/posts/{id}', 'Api\PostController@removeById');

    Route::get('/api/tags', 'Api\TagController@getAll');
    Route::post('/api/tags', 'Api\TagController@createOrUpdate');
    Route::get('/api/tags/{id}', 'Api\TagController@getById');
    Route::delete('/api/tags/{id}', 'Api\TagController@removeById');
});