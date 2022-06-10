<?php

namespace App\Http\Controllers;

use App\Order;
use App\Status;
use Illuminate\Http\Request;
use App\Events\OrderStatusChanged;
use DB;

class AdminOrdersController extends Controller
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
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if($user->is_admin != 1) {
            return redirect('/');
        }
        $orders = Order::with(['customer', 'status'])->limit(100)->orderBy('id', 'desc')->get();

        return view('admin.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        $user = auth()->user();
        if($user->is_admin != 1) {
            return redirect('/');
        }
        return view('show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        $user = auth()->user();
        if($user->is_admin != 1) {
            return redirect('/');
        }
        $statuses = Status::all();
        $currentStatus = $order->status_id;

        return view('admin.edit', compact('order', 'statuses', 'currentStatus'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $user = auth()->user();
        if($user->is_admin != 1) {
            return redirect('/');
        }
        $request->validate([
            'status_id' => 'required|numeric',
        ]);

        $order->status_id = $request->status_id;
        $order->save();

        event(new OrderStatusChanged($order));

        return back()->with('message', 'Order Status updated successfully!');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update_status_today(Request $request)
    {
        $user = auth()->user();
        if($user->is_admin != 1) {
            return redirect('/');
        }

        $request->validate([
            'status_id' => 'required|numeric',
        ]);

        $status = Status::where('id', $request->status_id)->first();

        if($status->column_name != 'paid' && $status->column_name != 'cancel') {

            $orders = Order::join('statuses', 'statuses.id', '=', 'orders.status_id')
            ->whereNotIn('statuses.column_name', ['paid','cancel'])
            ->where(DB::raw('DATE(orders.`created_at`)'), date("Y-m-d"))
            ->update(['orders.status_id' => $request->status_id]);

        }

        return back()->with('message', 'Order Status updated successfully!');
    }

    public function update_voucher(Request $request) {
        $user = auth()->user();
        if($user->is_admin != 1) {
            return back()->with('message', 'Access Denied!');
        }

        $request->validate([
            'order_id' => 'required',
        ]);

        if(!empty($request->order_id)) {
            Order::whereIn('id', $request->order_id)
            ->update(['discount' => DB::raw('FLOOR(orders.amount*'.$request->discount.'/1000)*1000')]);
        }

        return back()->with('message', 'Voucher updated successfully!');
    }
}
