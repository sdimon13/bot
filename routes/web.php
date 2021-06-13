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
    return view('welcome');
});

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/botman/tinker', 'BotManController@tinker');
//Route::get('/events', 'EventController@list');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/user/list', 'UserController@list')->name('users');
Route::post('/user/add', 'UserController@add')->name('user.add');
Route::get('user/get/{id}', 'UserController@get')->name('user.get');
Route::get('user/delete/{id}', 'UserController@delete')->name('user.delete');
Route::get('user/upgrade/{id}', 'UserController@upgrade')->name('user.upgrade');

Route::post('/user/object/add', 'ObjectUserController@add')->name('user.object.add');
Route::get('user/object/delete/{id}', 'ObjectUserController@delete')->name('user.object.delete');
