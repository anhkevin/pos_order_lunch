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

// Route::get('/', function () {
//     return view('dashboard');
// });

Route::get('/fire', function () {
    event(new OrderStatusChanged);

    return 'Fired';
});

Auth::routes();

Route::get('/', 'App\Http\Controllers\HomeController@index')->name('dashboard');

// User Routes
Route::middleware('auth')->group(function () {
    Route::get('/orders', 'App\Http\Controllers\UserOrdersController@index')->name('user.orders');
    // Route::get('/home', 'App\Http\Controllers\UserOrdersController@index')->name('user.home');
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

    Route::get('/my-wallet', 'App\Http\Controllers\WalletController@index')->name('wallet.index');
    Route::post('/my-wallet/load-history', 'App\Http\Controllers\WalletController@history')->name('wallet.history');
    Route::get('/my-wallet/show', 'App\Http\Controllers\WalletController@show')->name('wallet.show');
    Route::post('/my-wallet/load-wallet', 'App\Http\Controllers\WalletController@load_wallet')->name('wallet.load_wallet');
    Route::post('/my-wallet/deposit/{user}', 'App\Http\Controllers\WalletController@deposit')->name('wallet.deposit');
    Route::post('/my-wallet/withdrawal/{user}', 'App\Http\Controllers\WalletController@withdrawal')->name('wallet.withdrawal');
    Route::get('/my-wallet/{user}', 'App\Http\Controllers\WalletController@index')->name('wallet.index_user');

    Route::get('/collection', 'App\Http\Controllers\CollectionController@index')->name('collect.index');

    Route::prefix('api')->group(function () {
        Route::post('/order/add', 'App\Http\Controllers\Api\OrdersController@api_add_order');
        Route::post('/order/pay_order_type', 'App\Http\Controllers\Api\OrdersController@pay_order_type');
        Route::post('/order/get_stepper', 'App\Http\Controllers\Api\OrdersController@get_stepper_by_order');
        Route::post('/order/update_status_order', 'App\Http\Controllers\Api\OrdersController@update_status_order');
        Route::post('/layout/load_header', 'App\Http\Controllers\Api\LayoutController@load_header');
    });
});

// Admin Routes - Make sure you implement an auth layer here
Route::prefix('admin')->group(function () {
    Route::get('/orders', 'App\Http\Controllers\AdminOrdersController@index')->name('admin.orders');
    Route::patch('/orders/update_voucher', 'App\Http\Controllers\AdminOrdersController@update_voucher')->name('admin.orders.update_voucher');
    Route::get('/orders/edit/{order}', 'App\Http\Controllers\AdminOrdersController@edit')->name('admin.orders.edit');
    Route::patch('/orders/{order}', 'App\Http\Controllers\AdminOrdersController@update')->name('admin.orders.update');
});

Route::redirect('/admin', '/admin/orders');
