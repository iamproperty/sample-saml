<?php

use Illuminate\Support\Facades\Route;

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

Route::view('/', 'home');
// Store and remove a fake user for the Identity Provider
Route::post('/user', 'UserController@store');
Route::delete('/user', 'UserController@destroy');

Route::get('/idp/respond', 'IdentityProviderController@respond');
Route::get('/idp/initiate', 'IdentityProviderController@initiate');

Route::get('/sp/initiate', 'ServiceProviderController@initiate');
Route::get('/sp/consumer', 'ServiceProviderController@consumer');
Route::post('/sp/consumer', 'ServiceProviderController@consumer');
