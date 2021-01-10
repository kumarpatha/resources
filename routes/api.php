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
//Login and Forget Pasword
Route::post('users/register', 'UsersController@register');
Route::post('users/authenticate', 'LoginController@authenticate');
Route::post('forget-password', 'LoginController@forget_password');
Route::post('reset-password', 'LoginController@reset_password');


Route::group([
    'middleware' => ['auth:sanctum'],
], function () {
    //Users
    Route::get('users', 'UsersController@users');
    Route::get('getuser/{id}', 'UsersController@getuser');
    Route::post('edit-user', 'UsersController@edit_user');
    Route::get('deleteUser/{id}', 'UsersController@deleteUser');
    Route::get('users/logout', 'UsersController@logout');
    Route::post('change-password', 'UsersController@change_password');

    //Client
    Route::get('clients', 'ClientController@clients');
    Route::post('add-client', 'ClientController@add_client');
    Route::get('getclient/{id}', 'ClientController@getclient');
    Route::post('edit-client', 'ClientController@edit_client');
    Route::get('deleteClient/{id}', 'ClientController@deleteClient');

    //Customers
    Route::get('customers', 'CustomerController@customers');
    Route::post('add-customer', 'CustomerController@add_customer');  
    Route::get('get-customer-info/{id}', 'CustomerController@get_customer_info');
    Route::post('search-customer', 'CustomerController@search_customer');
    Route::post('edit-customer', 'CustomerController@edit_customer');
    Route::get('deleteCustomer/{id}', 'CustomerController@deleteCustomer');
    
    //Project
    Route::get('projects', 'ProjectController@projects');
    Route::post('add-project', 'ProjectController@add_project');
    Route::post('search-project', 'ProjectController@search_project');
    Route::get('get-project-info/{id}', 'ProjectController@get_project_info');
    Route::post('edit-project', 'ProjectController@edit_project');
    Route::get('deleteProject/{id}', 'ProjectController@deleteProject');

    //Product
    Route::get('products', 'ProductController@products');
    Route::post('add-product', 'ProductController@add_product');
    Route::post('search-product', 'ProductController@search_product');
    Route::get('get-product-info/{id}', 'ProductController@get_product_info');
    Route::post('edit-product', 'ProductController@edit_product');
    Route::get('deleteProduct/{id}', 'ProductController@deleteProduct');
    
});