<?php

use Illuminate\Support\Facades\Route;
use App\Events\OrderStatusChanged;

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

Route::get('/fire', function () {
    event(new OrderStatusChanged);

    return 'Fired';
});

Auth::routes();

// User Routes
Route::middleware('auth')->group(function () {
    Route::get('/orders', 'App\Http\Controllers\UserOrdersController@index')->name('user.orders');
    Route::get('/home', 'App\Http\Controllers\UserOrdersController@index')->name('user.home');
    Route::get('/orders/create', 'App\Http\Controllers\UserOrdersController@create')->name('user.orders.create');
    Route::get('/orders/today', 'App\Http\Controllers\UserOrdersController@today')->name('user.orders.today');
    Route::get('/orders/product', 'App\Http\Controllers\UserOrdersController@product')->name('user.orders.product');
    Route::get('/orders/debt', 'App\Http\Controllers\UserOrdersController@debt')->name('user.orders.debt');
    Route::patch('/orders/cancel/{order}', 'App\Http\Controllers\UserOrdersController@cancel')->name('user.orders.cancel');
    Route::get('/orders/edit/{order}', 'App\Http\Controllers\UserOrdersController@edit')->name('user.orders.edit');
    Route::post('/orders/update/{order}', 'App\Http\Controllers\UserOrdersController@update')->name('user.orders.update');
    Route::post('/orders', 'App\Http\Controllers\UserOrdersController@store')->name('user.orders.store');
    Route::get('/orders/{order}', 'App\Http\Controllers\UserOrdersController@show')->name('user.orders.show');

    Route::get('/momo/redirect_url', 'App\Http\Controllers\MomoController@redirect_url')->name('user.momo.redirect_url');
    Route::get('/momo/ipn_url', 'App\Http\Controllers\MomoController@ipn_url')->name('user.momo.ipn_url');
    Route::get('/momo/create/{order}', 'App\Http\Controllers\MomoController@create')->name('user.momo.create');
});

// Admin Routes - Make sure you implement an auth layer here
Route::prefix('admin')->group(function () {
    Route::get('/orders', 'App\Http\Controllers\AdminOrdersController@index')->name('admin.orders');
    Route::patch('/orders/update_status_today', 'App\Http\Controllers\AdminOrdersController@update_status_today')->name('admin.orders.update_status_today');
    Route::patch('/orders/update_voucher', 'App\Http\Controllers\AdminOrdersController@update_voucher')->name('admin.orders.update_voucher');
    Route::get('/orders/edit/{order}', 'App\Http\Controllers\AdminOrdersController@edit')->name('admin.orders.edit');
    Route::patch('/orders/{order}', 'App\Http\Controllers\AdminOrdersController@update')->name('admin.orders.update');
});

Route::redirect('/admin', '/admin/orders');
