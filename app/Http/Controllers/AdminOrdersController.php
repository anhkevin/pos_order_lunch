<?php

namespace App\Http\Controllers;

use App\Order;
use App\Status;
use App\Models\History_payment;
use App\Models\Order_status;
use App\Models\Order_type;
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
        $status = Status::where('id', $order->status_id)->first();
        if ($status->column_name == 'paid' || $status->column_name == 'cancel') {
            $statuses = Status::whereIn('statuses.column_name', [$status->column_name])->get();
        } else {
            $statuses = Status::all();
        }
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
        $user_login = auth()->user();
        
        $request->validate([
            'status_id' => 'required|numeric',
        ]);

        $user = auth()->user()->find($order->user_id);

        $order_status_id = $order->status_id;
        $order->status_id = $request->status_id;
        $status = Status::where('id', $request->status_id)->first();
        $status_order = Status::where('id', $order_status_id)->first();

        if($status_order->column_name == 'paid' && $status->column_name != 'cancel') {
            $order->status_id = $order_status_id;
        }

        $shop_type = Order_type::where('id', $order->order_type)->first();

        if (!empty($shop_type->assign_user_id)) {
            if ($user_login->id == $shop_type->assign_user_id) {
                $user_login->is_admin = 1;
            }
        }

        if($user_login->is_admin != 1) {
            return redirect('/');
        }

        if (empty($shop_type) || empty($shop_type->pay_type)) {
            if($status_order->column_name != 'paid'
                && ($status->column_name == 'unpaid' || $status->column_name == 'paid') 
                && $user->total_money > 0) {
                    if ($user->total_money >= ($request->order->amount - $request->order->discount)) {
                        $status_paid = Status::where('column_name', 'paid')->first();
                        $order->status_id = $status_paid->id;

                        History_payment::create([
                            'user_id' => $request->order->user_id,
                            'order_id' => $request->order->id,
                            'amount' => '-'.($request->order->amount - $request->order->discount),
                            'note' => 'Order ID: ' . $request->order->id . ', Order Date:' . date_format($request->order->created_at ,"Y/m/d"),
                            'disabled' => 0,
                        ]);
                    } else {
                        return back()->with('message', 'Số tiền còn lại trên Ví không đủ!');
                    }
            }
            if($status_order->column_name != 'paid' && $status->column_name == 'paid' && $user->total_money <= 0) {
                History_payment::create([
                    'user_id' => $request->order->user_id,
                    'order_id' => $request->order->id,
                    'amount' => ($request->order->amount - $request->order->discount),
                    'note' => 'Transfer money',
                    'disabled' => 0,
                ]);
                History_payment::create([
                    'user_id' => $request->order->user_id,
                    'order_id' => $request->order->id,
                    'amount' => '-'.($request->order->amount - $request->order->discount),
                    'note' => 'Order ID: ' . $request->order->id . ', Order Date:' . date_format($request->order->created_at ,"Y/m/d"),
                    'disabled' => 0,
                ]);
            }
        }
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
            ->update(['discount' => DB::raw('IF(orders.amount*'.$request->discount.'/1000 > 1,ROUND(orders.amount*'.$request->discount.'/1000)*1000,0)')]);
        }

        return back()->with('message', 'Voucher updated successfully!');
    }
}
