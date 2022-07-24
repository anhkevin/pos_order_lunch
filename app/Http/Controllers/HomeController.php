<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Models\Shop;
use App\Models\General;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = Order::with('status')
        ->where(DB::raw('DATE(`created_at`)'), date("Y-m-d"))
        ->orderBy('id', 'asc')->get();

        $value_shop = General::where('key', 'shop_default')->first();
        $shop_info = Shop::where('id', $value_shop->value)->first();

        return view('dashboard', compact('orders', 'shop_info'));
    }
}
