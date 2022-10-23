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
        if(empty($request->shop_type_id)) {
            if ($shop_type = Order_type::where('order_date', '>=', date("Y-m-d"))->where('is_default', 1)->first()) {
                $request->shop_type_id = $shop_type->id;
            }
        }

        $order_status = Order_type::with('status_type')->where('id', $request->shop_type_id)->first();

        if (isset($order_status->status_type->column_name) && $order_status->status_type->column_name != 'order') {
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
            'order_type' => $request->shop_type_id,
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

    public function admin_pay_order(Request $request)
    {
        if(empty($request->order_id)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Order_id không được để trống !'
            ]);
        }

        $user = auth()->user();

        $order_status = Order::select('orders.*',
        'order_types.order_name', 'order_types.assign_user_id', 'statuses.column_name AS status_column')
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->join('order_types', 'order_types.id', '=', 'orders.order_type')
        //->where('orders.user_id', $user->id)
        ->where('orders.id', $request->order_id)
        //->whereIn('statuses.column_name', ['booked','unpaid'])
        ->whereIn('order_types.pay_type', [1,2])
        ->first();

        if (empty($order_status)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !'
            ]);
        }

        $is_admin = 0;

        if ($user->is_admin == 1) {
            $is_admin = 1;
        }
        if (!empty($order_status->assign_user_id)) {
            if ($user->id == $order_status->assign_user_id) {
                $is_admin = 1;
            }
        }

        if($is_admin != 1) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Bạn không có quyền truy cập !'
            ]);
        }

        if ($order_status->status_column == 'paid') {
            return response()->json([
                'status'    => 0,
                'message'    => 'Đơn hàng này đã thanh toán !'
            ]);
        }

        if ($order_status->status_column == 'cancel') {
            return response()->json([
                'status'    => 0,
                'message'    => 'Đơn hàng này đã hủy !'
            ]);
        }

        $status_paid = Status::where('column_name', 'paid')->first();

        DB::beginTransaction();

        try {

            // update status orders
            Order::join('statuses', 'statuses.id', '=', 'orders.status_id')
            //->whereIn('statuses.column_name', ['booked','unpaid'])
            ->whereNotIn('statuses.column_name', ['paid','cancel'])
            ->where('orders.id', $request->order_id)
            ->update(['orders.status_id' => $status_paid->id]);

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
            'message'    => 'Cập nhật thanh toán OK !',
            'html'      => html_order_status($status_paid->column_name, $status_paid->name)
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

    public function cancel_order(Request $request)
    {
        if(empty($request->order_id)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Order_id không được để trống !'
            ]);
        }

        $user = auth()->user();

        if (isset($request->is_not_join)) {
            $order_status = Order::select('orders.*',
            'order_types.order_name', 'order_types.assign_user_id')
            ->join('statuses', 'statuses.id', '=', 'orders.status_id')
            ->join('order_types', 'order_types.id', '=', 'orders.order_type')
            ->where('order_types.pay_type', 2)
            ->where('orders.id', $request->order_id)
            ->whereNotIn('statuses.column_name', ['paid'])
            ->first();
        } else {
            $order_status = Order::select('orders.*',
            'order_types.order_name', 'order_types.assign_user_id')
            ->join('statuses', 'statuses.id', '=', 'orders.status_id')
            ->join('order_types', 'order_types.id', '=', 'orders.order_type')
            ->where('orders.id', $request->order_id)
            ->whereIn('statuses.column_name', ['order'])
            ->first();
        }

        if (empty($order_status)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Trạng thái hiện tại không thể Hủy !'
            ]);
        }

        // check user
        if ($user->is_admin != 1) {
            if ($user->id != $order_status->assign_user_id && $user->id != $order_status->user_id) {
                return response()->json([
                    'status'    => 0,
                    'message'    => 'Bạn không có quyền Hủy !'
                ]);
            }
        }

        $status_cancel = Status::where('column_name', 'cancel')->first();

        DB::beginTransaction();

        try {

            // update status orders
            Order::where('id', $request->order_id)
            ->update(['status_id' => $status_cancel->id]);

            if (isset($request->is_not_join)) {
                Order::where('id', $request->order_id)
                ->update(['is_join' => 0]);
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
            'message'    => 'Đã Hủy thành công !'
        ]);
    }

    public function get_stepper_by_order(Request $request)
    {
        $shop_type_id = 0;
        if(!empty($request->order_type)) {
            $order_type = base64_decode($request->order_type);
            if (!$shop_type = Order_type::where('id', $order_type)->where('order_date', '>=', date("Y-m-d"))->first()) {
                $shop_type = Order_type::where('order_date', '>=', date("Y-m-d"))->where('is_default', 1)->first();
            }
        } else {
            $shop_type = Order_type::where('order_date', '>=', date("Y-m-d"))->where('is_default', 1)->first();
        }
        if (!empty($shop_type)) {
            $shop_type_id = $shop_type->id;
        }


        $order_status = Order_type::with('status_type')->where('id', $shop_type_id)->first();

        $is_select_product = true;
        $is_ordered = false;
        $is_request_pay = false;
        $is_auto_pay = false;

        if (isset($order_status->status_type->column_name)) {
            if ($order_status->status_type->column_name == 'booked') {
                $is_ordered = true;
            } elseif ($order_status->status_type->column_name == 'unpaid') {
                $is_ordered = true;
                $is_request_pay = true;
            } elseif ($order_status->status_type->column_name == 'paid') {
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
            if (!$shop_type = Order_type::where('id', $order_type)->where('order_date', '>=', date("Y-m-d"))->first()) {
                $shop_type = Order_type::where('order_date', '>=', date("Y-m-d"))->where('is_default', 1)->first();
            }
        } else {
            $shop_type = Order_type::where('order_date', '>=', date("Y-m-d"))->where('is_default', 1)->first();
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

            Order_type::where('id', $shop_type_id)
                ->update(['status_id' => $status->id]);

            if($status->column_name != 'paid' && $status->column_name != 'cancel') {

                Order::join('statuses', 'statuses.id', '=', 'orders.status_id')
                ->whereNotIn('statuses.column_name', ['paid','cancel'])
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
