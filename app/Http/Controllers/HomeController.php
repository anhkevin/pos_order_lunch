<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Models\Shop;
use App\Models\General;
use App\Models\Order_type;
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
    public function index(Request $request)
    {
        $shop_type_id = 0;
        $title = '';
        if(!empty($request->order_type)) {
            $order_type = base64_decode($request->order_type);
            if ($shop_type = Order_type::where('id', $order_type)->first()) {
                $shop_id = $shop_type->shop_id;
            }
        } else {
            if ($shop_type = Order_type::where('order_date', date("Y-m-d"))->where('is_default', 1)->first()) {
                $shop_id = $shop_type->shop_id;
            }
        }
        if(!empty($shop_type)) {
            $shop_type_id = $shop_type->id;
            $title = $shop_type->order_name;
        }

        $orders = Order::with('status')
        ->where(DB::raw('DATE(`created_at`)'), date("Y-m-d"))
        ->where('order_type', $shop_type_id)
        ->orderBy('id', 'asc')->get();

        $value_shop = General::where('key', 'shop_default')->first();
        $shop_info = Shop::where('id', $value_shop->value)->first();

        $list_order_type = Order_type::where('order_date', date("Y-m-d"))->orderBy('id')->get();

        return view('dashboard', compact('orders', 'shop_info', 'list_order_type', 'title'));
    }
}
