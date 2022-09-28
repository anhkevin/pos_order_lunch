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

        // $order_by_user = Order::join('statuses', 'statuses.id', '=', 'orders.status_id')
        //     ->whereNotIn('statuses.column_name', ['cancel'])
        //     ->where('orders.user_id', auth()->user()->id)
        //     ->where('orders.order_type', $poll_info->id)
        //     ->first();
        // if($order_by_user) {
        //     return response()->json([
        //         'status'    => 0,
        //         'message'    => 'Bạn đã tham gia rồi !'
        //     ]);
        // }

        // Add order
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'address' => !empty($request->user_name) ? $request->user_name : auth()->user()->name,
            'size' => 'đá banh',
            'toppings' => '',
            'instructions' => !empty($request->user_name) ? $request->user_name : '',
            'amount' => $poll_info->price_every_order,
            'order_type' => $poll_info->id,
        ]);

        return response()->json([
            'status'    => 1,
            'message'    => 'Tham gia thành công !'
        ]);
    }

    public function edit(Request $request)
    {
        if(empty($request->poll_id)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !'
            ]);
        }

        if(empty($request->poll_name)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Name không thể bỏ trống !'
            ]);
        }

        if(!empty($request->poll_money) && $request->poll_money < 0) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Số tiền thanh toán không hợp lệ !'
            ]);
        }

        if (!$poll_info = Order_type::with('status_type')->where('id', $request->poll_id)->where('pay_type', 2)->first()) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !!'
            ]);
        }

        // if (isset($poll_info->status_type) && ($poll_info->status_type->column_name != 'order')) {
        //     return response()->json([
        //         'status'    => 0,
        //         'message'    => 'Trạng thái không thể update !!'
        //     ]);
        // }

        $user = auth()->user();

        if ($user->is_admin != 1) {
            if ($user->id != $poll_info->assign_user_id) {
                return response()->json([
                    'status'    => 0,
                    'message'    => 'Bạn không có quyền Update !'
                ]);
            }
        }

        $status_order = Status::where('column_name', 'order')->first();
        $status_unpaid = Status::where('column_name', 'unpaid')->first();
        $status_paid = Status::where('column_name', 'paid')->first();
        $status_cancel = Status::where('column_name', 'cancel')->first();

        DB::beginTransaction();

        try {

            // update status orders
            Order_type::where('id', $request->poll_id)
            ->update(['order_name' => $request->poll_name, 'description' => $request->poll_description, 'price_every_order' => $request->poll_money]);

            // update money
            if (((float)$poll_info->price_every_order) != ((float)$request->poll_money)) {
                Order::where('order_type', $request->poll_id)
                ->whereNotIn('status_id', [$status_paid->id,$status_cancel->id])
                ->update(['amount' => $request->poll_money]);
            }

            // update status
            if(!empty($request->poll_money) && $request->poll_money > 0) {
                Order_type::where('id', $request->poll_id)
                ->update(['status_id' => $status_unpaid->id]);

                Order::where('order_type', $request->poll_id)
                ->whereNotIn('status_id', [$status_paid->id,$status_cancel->id])
                ->update(['status_id' => $status_unpaid->id]);
            } else {
                Order_type::where('id', $request->poll_id)
                ->update(['status_id' => $status_order->id]);

                Order::where('order_type', $request->poll_id)
                ->whereNotIn('status_id', [$status_paid->id,$status_cancel->id])
                ->update(['status_id' => $status_order->id]);
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
            'message'    => 'Update thành công !'
        ]);
    }

    public function get_cancel(Request $request)
    {
        if(empty($request->order_type)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !'
            ]);
        }

        $order_type_id = base64_decode($request->order_type);

        if (!$poll_info = Order_type::with('status_type')->where('id', $order_type_id)->where('pay_type', 2)->first()) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !!'
            ]);
        }

        $status_cancel = Status::where('column_name', 'cancel')->first();

        $list_user_ok = array();

        if ($request->is_join == 1) {
            $order_ok = Order::where('order_type', $order_type_id)
            ->where('status_id', '<>', $status_cancel->id)
            ->orderBy('id', 'asc')->get();
            if ($order_ok) {
                foreach ($order_ok as $key => $value) {
                    $list_user_ok[] = $value->address;
                }
            }
        }

        $orders = Order::where('order_type', $order_type_id)
        ->where('status_id', $status_cancel->id)
        ->where('is_join', $request->is_join)
        ->whereNotIn('address', $list_user_ok)
        ->orderBy('id', 'asc')->get();
        
        if ($orders && $orders->count() > 0) {
            return response()->json([
                'status'    => 1,
                'message'    => 'Get thành công !',
                'orders'    => $orders
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'    => 'Không có dữ liệu !'
            ]);
        }
    }
}
