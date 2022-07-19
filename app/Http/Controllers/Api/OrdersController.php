<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use App\Models\Shop;
use App\Models\General;
use App\Models\Order_status;
use App\Order_detail;
use App\Product;
use App\Status;
use Illuminate\Http\Request;
use DB;

class OrdersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function api_add_order(Request $request)
    {
        // die(Var_dump($request->token));
        $order_status = Order_status::join('statuses', 'statuses.id', '=', 'order_statuses.status_id')
        ->whereIn('statuses.column_name', ['booked','unpaid'])
        ->where('order_date', date("Y-m-d"))->first();

        if ($order_status) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Đơn hàng đã đặt, không thể Order thêm !'
            ]);
        }

        if (empty($request->products)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Vui lòng chọn món !'
            ]);
        }

        // Add order
        $product_rice_name = "";
        $amount = 0;
        foreach ($request->products as $key => $value) {
            $value = (object)$value;
            $product_rice_name .= $value->number . ' x ' . $value->name . '\r\n';

            $price_product = $value->number * $value->price;
            if(!empty($value->discount_price)) {
                $price_product = $value->number * $value->discount_price;
            }
            $amount += $price_product;
        }

        $order = Order::create([
            'user_id' => auth()->user()->id,
            'address' => auth()->user()->name,
            'size' => rtrim($product_rice_name, '\r\n'),
            'toppings' => '',
            'instructions' => $request->comment,
            'amount' => $amount,
        ]);

        foreach ($request->products as $key => $value) {
            $value = (object)$value;
            $price_product = $value->price;
            if(!empty($value->discount_price)) {
                $price_product = $value->discount_price;
            }

            Order_detail::create([
                'product_id' => $value->id,
                'product_name' => $value->name,
                'order_id' => $order->id,
                'price' => $price_product,
                'number' => $value->number,
                'dish_type_name' => $value->dish_type_name
            ]);
        }

        return response()->json([
            'status'    => 1,
            'message'    => 'Đặt món thành công !'
        ]);
    }
}
