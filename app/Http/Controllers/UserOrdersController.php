<?php

namespace App\Http\Controllers;

use App\Order;
use App\Models\Shop;
use App\Models\General;
use App\Models\Order_status;
use App\Models\Order_type;
use App\Order_detail;
use App\Product;
use App\Status;
use Illuminate\Http\Request;
use DB;

class UserOrdersController extends Controller
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
        $orders = Order::with('status')
        ->with('order_types')
        ->with('history_payments')
        ->where('orders.user_id', $user->id)
        ->limit(20)
        ->orderBy('orders.id', 'desc')->get();

        return view('index', compact('user', 'orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $shop_default = General::where('key', 'shop_default')->first();
        $shop_id = $shop_default->value;
        $title = 'Cơm trưa ngày: ' . date("Y-m-d");
        $shop_type_id = 0;
        $message_order = '';
        if(!empty($request->order_type)) {
            $order_type = base64_decode($request->order_type);
            if ($shop_type = Order_type::where('id', $order_type)->where('order_date', date("Y-m-d"))->first()) {
                $shop_id = $shop_type->shop_id;
                $title = $shop_type->order_name;
            } else {
                if ($shop_type = Order_type::where('order_date', date("Y-m-d"))->where('is_default', 1)->first()) {
                    $shop_id = $shop_type->shop_id;
                    $title = $shop_type->order_name;
                }
            }
        } else {
            if ($shop_type = Order_type::where('order_date', date("Y-m-d"))->where('is_default', 1)->first()) {
                $shop_id = $shop_type->shop_id;
                $title = $shop_type->order_name;
            } else {
                if ($coffee_default = General::where('key', 'coffee_default')->first()) {
                    $coffee_user = General::where('key', 'coffee_default_user')->first();
                    Order_type::create([
                        'order_date' => date("Y-m-d"),
                        'order_name' => 'Cà phê sáng | Huy Dancer',
                        'shop_id' => $coffee_default->value,
                        'status_id' => 1,
                        'pay_type' => 1,
                        'is_default' => 0,
                        'assign_user_id' => $coffee_user->value,
                    ]);
                }
                $shop_type = Order_type::create([
                    'order_date' => date("Y-m-d"),
                    'order_name' => $title,
                    'shop_id' => $shop_id,
                    'status_id' => 1,
                    'pay_type' => 0,
                    'is_default' => 1,
                ]);
            }
        }
        if (!empty($shop_type)) {
            $shop_id = $shop_type->shop_id;
            $title = $shop_type->order_name;
            $shop_type_id = $shop_type->id;
        }
        $shop = Shop::where('id', $shop_id)->first();

        $product_rice = array();
        $product_all = Product::where('shop_id', $shop_id)->orderBy('id')->get();
        if ($product_all) {
            foreach ($product_all as $key => $value) {
                $product_rice[$value->dish_type_name][] = $value;
            }
        }

        $product_first = Product::where('shop_id', $shop_id)->where('dish_type_name', 'like','cơm%')->orderBy('id')->first();

        if (!empty($shop_type_id)) {
            $order_status = Order_status::join('statuses', 'statuses.id', '=', 'order_statuses.status_id')
            ->whereIn('statuses.column_name', ['booked','unpaid'])
            ->where('order_type', $shop_type_id)
            ->where('order_date', date("Y-m-d"))->first();

            if ($order_status) {
                $message_order = 'đã đặt, không thể Order thêm !';
            }
        }

        $list_order_type = Order_type::where('order_date', date("Y-m-d"))->whereIn('pay_type', [0,1])->orderBy('id')->get();

        return view('order.create', compact('product_rice', 'product_first', 'shop', 'title', 'list_order_type', 'shop_type_id', 'message_order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $order_status = Order_status::join('statuses', 'statuses.id', '=', 'order_statuses.status_id')
        ->whereIn('statuses.column_name', ['booked','unpaid'])
        ->where('order_date', date("Y-m-d"))->first();

        if ($order_status) {
            return back()->with('status', 'Đơn hàng đã đặt, không thể Order thêm !');
        }

        if (empty($request->product_rice) && empty($request->toppings)) {
            return back()->with('status', 'Vui lòng chọn món !');
        }

        // $request->validate([
        //     'product_rice' => 'required',
        // ]);

        $product_id_array = array();
        $product_rice_name = "";
        $amount = 0;
        if (!empty($request->product_rice)) {
            $product_rice = Product::where('id', $request->product_rice)->first();
            $product_rice_name = $product_rice->name;
            $amount += $product_rice->price;
            array_push($product_id_array, $product_rice->id);
        }

        $toppings = array();
        if (!empty($request->toppings)) {
            foreach ($request->toppings as $key => $value) {
                $product_opt = Product::where('id', $value)->first();
                array_push($toppings, $product_opt->name);
                array_push($product_id_array, $product_opt->id);
                $amount += $product_opt->price;
            }
        } 

        $order = Order::create([
            'user_id' => auth()->user()->id,
            'address' => auth()->user()->name,
            'size' => $product_rice_name,
            'toppings' => !empty($toppings) ? implode(', ', $toppings) : '',
            'instructions' => $request->instructions,
            'order_type' => $request->shop_type_id,
            'amount' => $amount,
        ]);

        if(!empty($product_id_array)) {
            foreach ($product_id_array as $key => $value) {
                Order_detail::create([
                    'product_id' => $value,
                    'order_id' => $order->id,
                ]);
            }
        }

        return redirect()->route('user.orders.show', $order)->with('message', 'Order received!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        if ($order->user_id != auth()->user()->id) {
            return redirect()->route('user.orders', $order)->with('message', 'Access Denied');
        }
        $status = Status::where('id', $order->status_id)->first();
        $order->status_name = $status->name;
        $order->status_column_name = $status->column_name;
        return view('show', compact('order'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function today(Request $request)
    {
        $user = auth()->user();
        $value_shop = General::where('key', 'shop_default')->first();
        $shop_id = $value_shop->value;
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

            if (!empty($shop_type->assign_user_id)) {
                if ($user->id == $shop_type->assign_user_id) {
                    $user->is_admin = 1;
                }
            }
        }

        $orders = Order::with('status')
        ->with('history_payments')
        ->where(DB::raw('DATE(`created_at`)'), date("Y-m-d"))
        ->where('order_type', $shop_type_id)
        ->orderBy('id', 'asc')->get();

        $shop_info = Shop::where('id', $shop_id)->first();

        $order_status = Order_status::join('statuses', 'statuses.id', '=', 'order_statuses.status_id')
        ->where('order_type', $shop_type_id)
        ->where('order_date', date("Y-m-d"))->first();

        $list_order_type = Order_type::where('order_date', date("Y-m-d"))->whereIn('pay_type', [0,1])->orderBy('id')->get();

        return view('today_order', compact('user', 'orders', 'shop_info', 'order_status', 'list_order_type', 'title'));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function product(Request $request)
    {
        $user = auth()->user();

        $shop_type_id = 0;
        $title = '';
        if(!empty($request->order_type)) {
            $order_type = base64_decode($request->order_type);
            $shop_type = Order_type::where('id', $order_type)->first();
        } else {
            $shop_type = Order_type::where('order_date', date("Y-m-d"))->where('is_default', 1)->first();
        }
        if(!empty($shop_type)) {
            $shop_type_id = $shop_type->id;
            $title = $shop_type->order_name;
        }

        $product_all = Order_detail::select('order_details.product_id','order_details.product_name','order_details.price','order_details.dish_type_name', DB::raw('SUM(order_details.number) AS count_product'))
        ->join('orders', 'orders.id', '=', 'order_details.order_id')
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->whereNotIn('statuses.column_name', ['cancel'])
        ->where(DB::raw('DATE(order_details.`created_at`)'), date("Y-m-d"))
        ->where('orders.order_type', $shop_type_id)
        ->where('order_details.disabled', 0)
        ->orderBy('order_details.product_id', 'asc')
        ->groupBy('order_details.product_id','order_details.product_name','order_details.price','order_details.dish_type_name')
        ->get();

        $products_rice = array();
        if ($product_all) {
            foreach ($product_all as $key => $value) {
                $products_rice[$value->dish_type_name][] = $value;
            }
        }

        // Discount
        $discount = Order::select(DB::raw('SUM(orders.discount) AS total_discount'))
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->whereNotIn('statuses.column_name', ['cancel'])
        ->where(DB::raw('DATE(orders.`created_at`)'), date("Y-m-d"))
        ->where('orders.order_type', $shop_type_id)
        ->first();
        $total_discount = !empty($discount->total_discount) ? $discount->total_discount : 0;

        $list_order_type = Order_type::where('order_date', date("Y-m-d"))->whereIn('pay_type', [0,1])->orderBy('id')->get();

        return view('today_product', compact('user', 'products_rice', 'list_order_type', 'title', 'total_discount'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function debt()
    {
        $user = auth()->user();
        $orders = Order::with('status')
        ->select('orders.*', 'order_types.assign_user_id')
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->join('order_types', 'order_types.id', '=', 'orders.order_type')
        ->whereNotIn('statuses.column_name', ['paid','cancel'])
        ->whereIn('order_types.pay_type', [0,1])
        ->orderBy('orders.id', 'desc')->get();

        return view('debt', compact('user', 'orders'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cancel(Order $order)
    {
        $order_info = Order::select('orders.*')
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->where('orders.id', $order->id)
        ->where('orders.user_id', auth()->user()->id)
        ->whereIn('statuses.column_name', ['order'])
        ->first();
        if(!empty($order_info)) {
            $status_cancel = Status::where('column_name', 'cancel')->first();

            Order::where('id', $order->id)
            ->update(['status_id' => $status_cancel->id]);

            $message = 'Order ID:' . $order->id . ' đã cancel thành công!';
        } else {
            $message = 'Order ID:' . $order->id . ' không thể cancel. Vui lòng liên hệ Admin để biết thêm chi tiết';
        }

        return back()->with('message', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        if ($order->user_id != auth()->user()->id) {
            return redirect()->route('user.orders', $order)->with('message', 'Access Denied');
        }

        $status = Status::where('id', $order->status_id)->first();
        if ($status->column_name != 'order') {
            return redirect()->route('user.orders', $order)->with('message', 'Access Denied');
        }

        $shop = General::where('key', 'shop_default')->first();

        $product_rice = Product::type(1)->where('shop_id', $shop->value)->orderBy('created_at')->get();
        $product_option = Product::type(2)->where('shop_id', $shop->value)->orderBy('created_at')->get();

        $list_product_id = Order_detail::select('product_id')
        ->join('orders', 'orders.id', '=', 'order_details.order_id')
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->where('orders.id', $order->id)
        ->where('orders.user_id', auth()->user()->id)
        ->whereIn('statuses.column_name', ['order'])
        ->where('order_details.disabled', 0)
        ->orderBy('product_id', 'asc')
        ->groupBy('product_id')
        ->get();
        $arr_product_id = array();
        if(!empty($list_product_id)) {
            foreach ($list_product_id as $key => $value) {
                array_push($arr_product_id, $value['product_id']);
            }
        }

        return view('order.edit', compact('arr_product_id', 'product_rice', 'product_option', 'order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        if ($order->user_id != auth()->user()->id) {
            return redirect()->route('user.orders', $order)->with('message', 'Access Denied');
        }

        $status = Status::where('id', $order->status_id)->first();
        if ($status->column_name != 'order') {
            return redirect()->route('user.orders', $order)->with('message', 'Access Denied');
        }
        
        $request->validate([
            'product_rice' => 'required',
        ]);

        $product_id_array = array();
        $product_rice_name = "";
        $amount = 0;
        if (!empty($request->product_rice)) {
            $product_rice = Product::where('id', $request->product_rice)->first();
            $product_rice_name = $product_rice->name;
            $amount += $product_rice->price;
            array_push($product_id_array, $product_rice->id);
        }

        $toppings = array();
        if (!empty($request->toppings)) {
            foreach ($request->toppings as $key => $value) {
                $product_opt = Product::where('id', $value)->first();
                array_push($toppings, $product_opt->name);
                array_push($product_id_array, $product_opt->id);
                $amount += $product_opt->price;
            }
        }

        $order->size = $product_rice_name;
        $order->toppings = !empty($toppings) ? implode(', ', $toppings) : '';
        $order->instructions = $request->instructions;
        $order->amount = $amount;
        $order->save();

        Order_detail::join('orders', 'orders.id', '=', 'order_details.order_id')
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->where('orders.id', $order->id)
        ->where('orders.user_id', auth()->user()->id)
        ->whereIn('statuses.column_name', ['order'])
        ->where('order_details.disabled', 0)
        ->update(['order_details.disabled' => 1]);

        if(!empty($product_id_array)) {
            foreach ($product_id_array as $key => $value) {
                Order_detail::create([
                    'product_id' => $value,
                    'order_id' => $order->id,
                ]);
            }
        }

        return back()->with('message', 'Update Success!');
    }
}
