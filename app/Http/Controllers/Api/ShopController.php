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

class ShopController extends Controller
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

    public function update_status(Request $request)
    {
        if(empty($request->shop_type_id)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'shop_type_id không được để trống !'
            ]);
        }

        if(!isset($request->is_close)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'is_close không được để trống !'
            ]);
        }

        $order_type = Order_type::where('order_date', '>=', date("Y-m-d"))->where('id', $request->shop_type_id)->first();

        if (empty($order_type)) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Có lỗi xảy ra, vui lòng thử lại !'
            ]);
        }

        $user = auth()->user();

        $is_admin = 0;

        if ($user->is_admin == 1) {
            $is_admin = 1;
        }
        if (!empty($order_type->assign_user_id)) {
            if ($user->id == $order_type->assign_user_id) {
                $is_admin = 1;
            }
        }

        if($is_admin != 1) {
            return response()->json([
                'status'    => 0,
                'message'    => 'Bạn không có quyền truy cập !'
            ]);
        }

        DB::beginTransaction();

        try {
            $is_close = 0;

            if ($request->is_close == true || $request->is_close == 1) {
                $is_close = 1;
            }

            Order_type::where('id', $request->shop_type_id)
            ->update(['is_close' => $is_close]);

            if ($order_type->column_name == 'coffee') {
                General::where('key', 'coffee_default_is_close')
                ->update(['value' => $is_close]);
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
            'message'    => 'Cập nhật thành công !',
        ]);

    }

    public function list_shops(Request $request)
    {
        $shops = array();

        $list_shops = Shop::where('delivery_id', $request->delivery_id)->get();

        if (!empty($list_shops)) {
            foreach ($list_shops as $key => $value) {
                $shops[] = array(
                    'id' => $value['id'],
                    'name' => $value['name'],
                );
            }
        }

        return response()->json([
            'status'    => 1,
            'shops'    => $shops,
        ]);
    }
}