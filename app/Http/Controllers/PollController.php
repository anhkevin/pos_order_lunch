<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\Models\Shop;
use App\Models\General;
use App\Models\Order_type;
use DB;

class PollController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function type(Request $request)
    {

        if(empty($request->type)) {
            return redirect('poll');
        }

        if (!$poll_info = Order_type::where('column_name', $request->type)->where('pay_type', 2)->first()) {
            return redirect('poll');
        }

        $list_staff = Order::select('orders.*', 'order_types.assign_user_id')
        ->with('status')
        ->with('history_payments')
        ->with('user_info')
        ->join('order_types', 'orders.order_type', '=', 'order_types.id')
        ->where('order_types.column_name', $request->type)
        ->where('order_types.pay_type', 2)
        ->get();

        return view('poll.type', compact('poll_info', 'list_staff'));
    }
}
