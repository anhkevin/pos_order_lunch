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

// Route::middleware('throttle:30,1')->get('/', function () {
//     return view('dashboard');
// });

Route::get('/fire', function () {
    event(new OrderStatusChanged);

    return 'Fired';
});

Auth::routes();

Route::middleware('throttle:60,1')->get('/', 'App\Http\Controllers\HomeController@index')->name('dashboard');

// User Routes
Route::middleware('auth')->group(function () {
    Route::middleware('throttle:30,1')->get('/orders', 'App\Http\Controllers\UserOrdersController@index')->name('user.orders');
    // Route::middleware('throttle:30,1')->get('/home', 'App\Http\Controllers\UserOrdersController@index')->name('user.home');
    Route::middleware('throttle:30,1')->get('/orders/create', 'App\Http\Controllers\UserOrdersController@create')->name('user.orders.create');
    Route::middleware('throttle:60,1')->get('/orders/today', 'App\Http\Controllers\UserOrdersController@today')->name('user.orders.today');
    Route::middleware('throttle:30,1')->get('/orders/product', 'App\Http\Controllers\UserOrdersController@product')->name('user.orders.product');
    Route::middleware('throttle:30,1')->get('/orders/debt', 'App\Http\Controllers\UserOrdersController@debt')->name('user.orders.debt');
    Route::middleware('throttle:30,1')->patch('/orders/cancel/{order}', 'App\Http\Controllers\UserOrdersController@cancel')->name('user.orders.cancel');
    Route::middleware('throttle:30,1')->get('/orders/edit/{order}', 'App\Http\Controllers\UserOrdersController@edit')->name('user.orders.edit');
    Route::middleware('throttle:30,1')->post('/orders/update/{order}', 'App\Http\Controllers\UserOrdersController@update')->name('user.orders.update');
    Route::middleware('throttle:30,1')->post('/orders', 'App\Http\Controllers\UserOrdersController@store')->name('user.orders.store');
    Route::middleware('throttle:30,1')->get('/orders/{order}', 'App\Http\Controllers\UserOrdersController@show')->name('user.orders.show');

    Route::middleware('throttle:30,1')->get('/momo/redirect_url', 'App\Http\Controllers\MomoController@redirect_url')->name('user.momo.redirect_url');
    Route::middleware('throttle:30,1')->get('/momo/ipn_url', 'App\Http\Controllers\MomoController@ipn_url')->name('user.momo.ipn_url');
    Route::middleware('throttle:30,1')->get('/momo/create/{order}', 'App\Http\Controllers\MomoController@create')->name('user.momo.create');

    Route::middleware('throttle:30,1')->get('/my-wallet', 'App\Http\Controllers\WalletController@index')->name('wallet.index');
    Route::middleware('throttle:30,1')->post('/my-wallet/load-history', 'App\Http\Controllers\WalletController@history')->name('wallet.history');
    Route::middleware('throttle:30,1')->get('/my-wallet/show', 'App\Http\Controllers\WalletController@show')->name('wallet.show');
    Route::middleware('throttle:30,1')->post('/my-wallet/load-wallet', 'App\Http\Controllers\WalletController@load_wallet')->name('wallet.load_wallet');
    Route::middleware('throttle:30,1')->post('/my-wallet/deposit/{user}', 'App\Http\Controllers\WalletController@deposit')->name('wallet.deposit');
    Route::middleware('throttle:30,1')->post('/my-wallet/withdrawal/{user}', 'App\Http\Controllers\WalletController@withdrawal')->name('wallet.withdrawal');
    Route::middleware('throttle:30,1')->get('/my-wallet/{user}', 'App\Http\Controllers\WalletController@index')->name('wallet.index_user');

    Route::middleware('throttle:30,1')->get('/collection', 'App\Http\Controllers\CollectionController@index')->name('collect.index');

    Route::prefix('api')->group(function () {
        Route::middleware('throttle:30,1')->post('/order/add', 'App\Http\Controllers\Api\OrdersController@api_add_order');
        Route::middleware('throttle:30,1')->post('/order/pay_order_type', 'App\Http\Controllers\Api\OrdersController@pay_order_type');
        Route::middleware('throttle:30,1')->post('/order/admin_pay_order', 'App\Http\Controllers\Api\OrdersController@admin_pay_order');
        Route::middleware('throttle:30,1')->post('/order/get_stepper', 'App\Http\Controllers\Api\OrdersController@get_stepper_by_order');
        Route::middleware('throttle:30,1')->post('/order/update_status_order', 'App\Http\Controllers\Api\OrdersController@update_status_order');
        Route::middleware('throttle:30,1')->post('/layout/load_header', 'App\Http\Controllers\Api\LayoutController@load_header');
        Route::middleware('throttle:30,1')->post('/order/cancel_order', 'App\Http\Controllers\Api\OrdersController@cancel_order');
    });

    Route::middleware('throttle:60,1')->get('/poll/{type}', 'App\Http\Controllers\PollController@type')->name('user.poll.type');

    Route::prefix('api')->group(function () {
        Route::middleware('throttle:30,1')->post('/poll/add_order', 'App\Http\Controllers\Api\PollController@api_add_order');
        Route::middleware('throttle:30,1')->post('/poll/edit', 'App\Http\Controllers\Api\PollController@edit');
    });
});

// Admin Routes - Make sure you implement an auth layer here
Route::prefix('admin')->group(function () {
    Route::middleware('throttle:30,1')->get('/orders', 'App\Http\Controllers\AdminOrdersController@index')->name('admin.orders');
    Route::middleware('throttle:30,1')->patch('/orders/update_voucher', 'App\Http\Controllers\AdminOrdersController@update_voucher')->name('admin.orders.update_voucher');
    Route::middleware('throttle:30,1')->get('/orders/edit/{order}', 'App\Http\Controllers\AdminOrdersController@edit')->name('admin.orders.edit');
    Route::middleware('throttle:30,1')->patch('/orders/{order}', 'App\Http\Controllers\AdminOrdersController@update')->name('admin.orders.update');
});

Route::redirect('/admin', '/admin/orders');
