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

    public function api_add_order(Request $request)
    {
        if(empty($request->poll_id)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !'
            ]);
        }

        $poll_id = base64_decode($request->poll_id);

        if (!$poll_info = Order_type::with('status_type')->where('id', $poll_id)->where('pay_type', 2)->first()) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !!'
            ]);
        }

        if (isset($poll_info->status_type) && ($poll_info->status_type->column_name != 'order')) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Đã đóng, không thể join !!'
            ]);
        }

        $order_by_user = Order::join('statuses', 'statuses.id', '=', 'orders.status_id')
            ->whereNotIn('statuses.column_name', ['cancel'])
            ->where('orders.user_id', auth()->user()->id)
            ->where('orders.order_type', $poll_info->id)
            ->first();
        if($order_by_user) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Bạn đã tham gia rồi !'
            ]);
        }

        // Add order
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'address' => auth()->user()->name,
            'size' => 'đá banh',
            'toppings' => '',
            'instructions' => '',
            'amount' => $poll_info->price_every_order,
            'order_type' => $poll_info->id,
        ]);

        return response()->json([
            'status'    => 1,
            'message'    => 'Tham gia thành công !'
        ]);
    }

    public function pay_order_type(Request $request)
    {
        if(empty($request->order_id)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Order_id không được để trống !'
            ]);
        }

        $user = auth()->user();

        $order_status = Order::select('orders.*',
        'order_types.order_name', 'order_types.assign_user_id')
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->join('order_types', 'order_types.id', '=', 'orders.order_type')
        ->where('orders.user_id', $user->id)
        ->where('orders.id', $request->order_id)
        ->whereIn('statuses.column_name', ['booked','unpaid'])
        ->whereIn('order_types.pay_type', [1,2])
        ->first();

        if (empty($order_status)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !'
            ]);
        }

        // check wallet
        $money_order = $order_status->amount - $order_status->discount;
        if ($user->total_money < $money_order) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Tiền trong Ví không đủ !'
            ]);
        }

        $status_paid = Status::where('column_name', 'paid')->first();

        DB::beginTransaction();

        try {

            // update status orders
            Order::join('statuses', 'statuses.id', '=', 'orders.status_id')
            ->whereIn('statuses.column_name', ['booked','unpaid'])
            ->where('orders.id', $request->order_id)
            ->update(['orders.status_id' => $status_paid->id]);

            // withdraw money
            History_payment::create([
                'user_id' => $user->id,
                'order_id' => $request->order_id,
                'amount' => '-'.($money_order),
                'note' => 'Order: ' . $order_status->size . ', Date:' . date_format($order_status->created_at ,"Y/m/d"),
                'disabled' => 0,
            ]);

            // deposit money
            History_payment::create([
                'user_id' => $order_status->assign_user_id,
                'order_id' => $request->order_id,
                'amount' => $money_order,
                'note' => 'User: ' . $user->name . ', Order:' . $order_status->size,
                'disabled' => 0,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !!'
            ]);
        }

        return response()->json([
            'status'    => 1,
            'message'    => 'Thanh toán thành công !',
            'html'      => html_order_status($status_paid->column_name, $status_paid->name)
        ]);
    }

    public function get_stepper_by_order(Request $request)
    {
        $shop_type_id = 0;
        if(!empty($request->order_type)) {
            $order_type = base64_decode($request->order_type);
            if (!$shop_type = Order_type::where('id', $order_type)->where('order_date', date("Y-m-d"))->first()) {
                $shop_type = Order_type::where('order_date', date("Y-m-d"))->where('is_default', 1)->first();
            }
        } else {
            $shop_type = Order_type::where('order_date', date("Y-m-d"))->where('is_default', 1)->first();
        }
        if (!empty($shop_type)) {
            $shop_type_id = $shop_type->id;
        }

        $order_status = Order_status::join('statuses', 'statuses.id', '=', 'order_statuses.status_id')
        ->where('order_type', $shop_type_id)
        ->where('order_date', date("Y-m-d"))->first();

        $is_select_product = true;
        $is_ordered = false;
        $is_request_pay = false;
        $is_auto_pay = false;

        if (isset($order_status->column_name)) {
            if ($order_status->column_name == 'booked') {
                $is_ordered = true;
            } elseif ($order_status->column_name == 'unpaid') {
                $is_ordered = true;
                $is_request_pay = true;
            } elseif ($order_status->column_name == 'paid') {
                $is_ordered = true;
                $is_request_pay = true;
                $is_auto_pay = true;
            }
        }

        return response()->json([
            'status'                => 1,
            'is_select_product'     => $is_select_product,
            'is_ordered'            => $is_ordered,
            'is_request_pay'        => $is_request_pay,
            'is_auto_pay'           => $is_auto_pay,
        ]);
    }

    public function update_status_order(Request $request)
    {
        $user = auth()->user();

        $is_access = false;

        if(empty($request->status_order)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Dữ liệu không hợp lệ !!'
            ]);
        }

        if($user->is_admin == 1) {
            $is_access = true;
        }

        $shop_type_id = 0;
        if(!empty($request->order_type)) {
            $order_type = base64_decode($request->order_type);
            if (!$shop_type = Order_type::where('id', $order_type)->where('order_date', date("Y-m-d"))->first()) {
                $shop_type = Order_type::where('order_date', date("Y-m-d"))->where('is_default', 1)->first();
            }
        } else {
            $shop_type = Order_type::where('order_date', date("Y-m-d"))->where('is_default', 1)->first();
        }
        if (!empty($shop_type)) {
            $shop_type_id = $shop_type->id;

            if (!empty($shop_type->assign_user_id)) {
                if ($user->id == $shop_type->assign_user_id) {
                    $is_access = true;
                }
            }
        }

        if (!$is_access) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Access Denied !!'
            ]);
        }

        if ($user->is_admin != 1 && $request->status_order == 'auto_pay') {
            return response()->json([
                'status'    => 0,
                'message'    => 'Access Denied !!'
            ]);
        }

        $status = Status::where('column_name', 'order')->first();
        if($request->status_order == 'ordered') {
            $status = Status::where('column_name', 'booked')->first();
        }
        if($request->status_order == 'request_pay') {
            $status = Status::where('column_name', 'unpaid')->first();
        }
        if($request->status_order == 'auto_pay') {
            $status = Status::where('column_name', 'paid')->first();
        }
        $status_paid = Status::where('column_name', 'paid')->first();

        DB::beginTransaction();

        try {

            $order_status = Order_status::join('statuses', 'statuses.id', '=', 'order_statuses.status_id')
            ->where('order_date', date("Y-m-d"))
            ->where('order_type', $shop_type_id)
            ->first();
            if($order_status) {
                Order_status::where('order_date', date("Y-m-d"))
                ->where('order_type', $shop_type_id)
                ->update(['status_id' => $status->id]);
            } else {
                Order_status::create([
                    'order_date' => date("Y-m-d"),
                    'status_id' => $status->id,
                    'order_type' => $shop_type_id,
                ]);
            }

            if($status->column_name != 'paid' && $status->column_name != 'cancel') {

                Order::join('statuses', 'statuses.id', '=', 'orders.status_id')
                ->whereNotIn('statuses.column_name', ['paid','cancel'])
                ->where(DB::raw('DATE(orders.`created_at`)'), date("Y-m-d"))
                ->where('order_type', $shop_type_id)
                ->update(['orders.status_id' => $status->id]);
            }

            if ($request->status_order == 'auto_pay') {
                $list_orders = Order::select('orders.*',
                    'statuses.column_name', 
                    'users.total_money', 'users.total_deposit', 'users.total_paid')
                    ->join('statuses', 'statuses.id', '=', 'orders.status_id')
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->whereNotIn('statuses.column_name', ['paid','cancel'])
                    ->where(DB::raw('DATE(orders.`created_at`)'), date("Y-m-d"))
                    ->where('users.total_money','>', 0)
                    ->where('order_type', $shop_type_id)
                    ->get();
                if($list_orders) {
                    foreach ($list_orders as $key => $value) {
                        $order_user = Order::select(DB::raw('SUM(orders.amount) AS total_amount'),DB::raw('SUM(orders.discount) AS total_discount'),'order_types.pay_type')
                        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
                        ->leftJoin('order_types', 'order_types.id', '=', 'orders.order_type')
                        ->whereNotIn('statuses.column_name', ['paid','cancel'])
                        ->where('orders.user_id', $value->user_id)
                        ->where(DB::raw('DATE(orders.`created_at`)'), date("Y-m-d"))
                        ->where('order_type', $shop_type_id)
                        ->groupBy('orders.user_id','order_types.pay_type')
                        ->first();

                        if(!empty($order_user->pay_type)) {
                            Order::join('statuses', 'statuses.id', '=', 'orders.status_id')
                            ->whereNotIn('statuses.column_name', ['paid','cancel'])
                            ->where('orders.id', $value->id)
                            ->where('order_type', $shop_type_id)
                            ->update(['orders.status_id' => $status_paid->id]);
                        } else {
                            if ($order_user && $value->total_money >= ($order_user->total_amount - $order_user->total_discount)) {
                            
                                History_payment::create([
                                    'user_id' => $value->user_id,
                                    'order_id' => $value->id,
                                    'amount' => '-'.($value->amount - $value->discount),
                                    'note' => 'Order ID: ' . $value->id . ', Order Date:' . date_format($value->created_at ,"Y/m/d"),
                                    'disabled' => 0,
                                ]);
        
                                Order::join('statuses', 'statuses.id', '=', 'orders.status_id')
                                ->whereNotIn('statuses.column_name', ['paid','cancel'])
                                ->where('orders.id', $value->id)
                                ->where('order_type', $shop_type_id)
                                ->update(['orders.status_id' => $status_paid->id]);
                            }
                        }
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !!'
            ]);
        }

        return response()->json([
            'status'    => 1,
            'message'    => 'Cập nhật trạng thái Order thành công !!',
        ]);
    }
}
