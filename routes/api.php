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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('users/register', 'UsersController@register');
Route::post('users/authenticate', 'LoginController@authenticate');
Route::post('forget-password', 'LoginController@forget_password');
Route::post('reset-password', 'LoginController@reset_password');


Route::group([
    'middleware' => ['auth:sanctum'],
], function () {
    Route::get('users', 'UsersController@users');
    Route::get('users/logout', 'UsersController@logout');
    Route::post('add-client', 'ClientController@add_client');
    Route::get('clients', 'ClientController@clients');
    Route::post('change-password', 'UsersController@change_password');
    Route::post('add-customer', 'CustomerController@add_customer');
    Route::get('customers', 'CustomerController@customers');
    Route::get('getclient/{id}', 'ClientController@getclient');
    Route::post('edit-client', 'ClientController@edit_client');
    Route::get('getuser/{id}', 'UsersController@getuser');
    Route::post('edit-user', 'UsersController@edit_user');
    Route::get('deleteClient/{id}', 'ClientController@deleteClient');
    Route::get('deleteUser/{id}', 'UsersController@deleteUser');
    Route::post('search-customer', 'CustomerController@search_customer');
    
});