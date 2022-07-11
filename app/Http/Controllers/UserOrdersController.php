<?php

namespace App\Http\Controllers;

use App\Order;
use App\Models\Shop;
use App\Models\General;
use App\Models\Order_status;
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
        $orders = Order::with('status')->where('user_id', $user->id)->limit(10)->orderBy('id', 'desc')->get();

        return view('index', compact('user', 'orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $shop_default = General::where('key', 'shop_default')->first();
        $shop = Shop::where('id', $shop_default->value)->first();

        $product_rice = array();
        $product_all = Product::where('shop_id', $shop_default->value)->orderBy('id')->get();
        if ($product_all) {
            foreach ($product_all as $key => $value) {
                $product_rice[$value->dish_type_name][] = $value;
            }
        }

        $product_first = Product::where('shop_id', $shop_default->value)->where('dish_type_name', 'like','cơm%')->orderBy('id')->first();

        $order_status = Order_status::join('statuses', 'statuses.id', '=', 'order_statuses.status_id')
        ->whereIn('statuses.column_name', ['booked','unpaid'])
        ->where('order_date', date("Y-m-d"))->first();

        $message_order = '';
        if ($order_status) {
            $message_order = 'Đơn hàng đã đặt, không thể Order thêm !';
        }

        return view('create', compact('product_rice', 'product_first', 'shop', 'message_order'));
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
    public function today()
    {
        $user = auth()->user();
        $orders = Order::with('status')
        ->where(DB::raw('DATE(`created_at`)'), date("Y-m-d"))
        ->orderBy('id', 'asc')->get();

        $value_shop = General::where('key', 'shop_default')->first();
        $shop_info = Shop::where('id', $value_shop->value)->first();

        $order_status = Order_status::join('statuses', 'statuses.id', '=', 'order_statuses.status_id')
        ->where('order_date', date("Y-m-d"))->first();

        return view('today_order', compact('user', 'orders', 'shop_info', 'order_status'));
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function product()
    {
        $user = auth()->user();
        $product_all = Order_detail::select('products.id','products.name','products.price','products.dish_type_name', DB::raw('count(*) AS count_product'))
        ->join('products', 'products.id', '=', 'order_details.product_id')
        ->join('orders', 'orders.id', '=', 'order_details.order_id')
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->whereNotIn('statuses.column_name', ['cancel'])
        ->where(DB::raw('DATE(order_details.`created_at`)'), date("Y-m-d"))
        // ->where('products.type', 1)
        ->where('order_details.disabled', 0)
        ->orderBy('products.id', 'asc')
        ->groupBy('products.id','products.name','products.price','products.dish_type_name')
        ->get();

        $products_rice = array();
        if ($product_all) {
            foreach ($product_all as $key => $value) {
                $products_rice[$value->dish_type_name][] = $value;
            }
        }

        // $products_option = Order_detail::select('products.id','products.name','products.price', DB::raw('count(*) AS count_product'))
        // ->join('products', 'products.id', '=', 'order_details.product_id')
        // ->join('orders', 'orders.id', '=', 'order_details.order_id')
        // ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        // ->whereNotIn('statuses.column_name', ['cancel'])
        // ->where(DB::raw('DATE(order_details.`created_at`)'), date("Y-m-d"))
        // ->where('products.type', 2)
        // ->where('order_details.disabled', 0)
        // ->orderBy('products.id', 'asc')
        // ->groupBy('products.id','products.name','products.price')
        // ->get();

        return view('today_product', compact('user', 'products_rice'));
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
        ->select('orders.*')
        ->join('statuses', 'statuses.id', '=', 'orders.status_id')
        ->whereNotIn('statuses.column_name', ['paid','cancel'])
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
