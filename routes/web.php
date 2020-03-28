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

use Illuminate\Support\Facades\URL;

URL::forceScheme('https');

Auth::routes();


Route::get('/', 'HomeController@index');



Route::get('/home', 'HomeController@index')->name('home');

Route::get('/getDomain','HomeController@domain');
Route::get('/logout','HomeController@logout');
Route::get('/selected','HomeController@selected');
Route::get('/filter','HomeController@filter');
Route::get('/user','HomeController@user');

//Route::get('/update','ApiTokenController@update');

Route::get('/upload', 'HomeController@upload');
Route::post('/upload', 'HomeController@postUpload');

Route::get('/proxy', 'ProxyController@listProxy');
Route::get('/addkey', 'ProxyController@addKey');