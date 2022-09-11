<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use App\Models\Shop;
use App\Models\General;
use App\Models\Order_status;
use App\Models\Order_type;
use App\Models\History_payment;
use App\Order_detail;
use App\Product;
use App\Status;
use Illuminate\Http\Request;
use DB;

class LayoutController extends Controller
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

    public function load_header()
    {
        $user = auth()->user();

        $order_debt = Order::select(DB::raw('SUM(orders.amount) AS total_amount'),
        DB::raw('SUM(orders.discount) AS total_discount'))
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->join('order_types', 'order_types.id', '=', 'orders.order_type')
        ->whereNotIn('statuses.column_name', ['paid','cancel'])
        ->where('orders.user_id', $user->id)
        ->groupBy('orders.user_id')
        ->first();

        return response()->json([
            'status'    => 1,
            'total_money'      => $user->total_money,
            'total_money_debt'      => !empty($order_debt) ? ($order_debt->total_amount - $order_debt->total_discount) : 0,
        ]);
    }
}