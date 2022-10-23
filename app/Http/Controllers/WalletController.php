<?php

namespace App\Http\Controllers;

use App\Order;
use App\Models\Shop;
use App\Models\General;
use App\Models\History_payment;
use App\User;
use App\Order_detail;
use App\Product;
use App\Status;
use Illuminate\Http\Request;
use DB;

class WalletController extends Controller
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
    public function index(Request $request)
    {
        $user = auth()->user();
        if(!empty($request->user) && $user->is_admin == 1) {
            $user = auth()->user()->find($request->user);

            if(empty($user)) {
                $user = new User;
            }
        }

        return view('wallet.index', compact('user'));
    }

    public function show()
    {
        return view('wallet.show');
    }

    public function wallets()
    {
        return view('wallet.wallets');
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        $row = $request->row;
        $rowperpage = $request->rowperpage;
        if ($row < 0) {
            $row = 0;
        }
        if ($rowperpage >= 100) {
            $rowperpage = 1;
        }

        $list_history = History_payment::where('user_id', $request->user_id)
        ->offset($row)
        ->limit($rowperpage)
        ->orderBy('id', 'desc')
        ->get();

        $response_arr = array();
        if ($list_history) {
            foreach ($list_history as $key => $value) {
                $response_arr[] = array(
                    'id'        =>  $value->id,
                    'date'      =>  !empty($value->created_at) ? date_format($value->created_at ,"Y/m/d") : '',
                    'type'      => ($value->amount > 0) ? 'Nạp tiền' : 'Trừ tiền',
                    'note'      => str_replace(array("\n", "\r\n", "\\r\\n"), " - ", "$value->note"),
                    'amount'    =>number_format($value->amount, 0, ".", ",") . "đ"
                );
            }
        }

        return json_encode($response_arr);
    }

    public function load_wallet(Request $request)
    {
        $row = $request->row;
        $rowperpage = $request->rowperpage;
        if ($row < 0) {
            $row = 0;
        }
        if ($rowperpage >= 100) {
            $rowperpage = 1;
        }
        
        $compare = '>';
        if (auth()->user()->is_admin) {
        	$compare = '>=';
        }

        $list_wallet = User::where('total_money',$compare, 0)
        ->offset($row)
        ->limit($rowperpage)
        ->orderBy('total_money', 'desc')
        ->orderBy('total_deposit', 'desc')
        ->orderBy('total_paid', 'desc')
        ->get();

        $response_arr = array();
        if ($list_wallet) {
            foreach ($list_wallet as $key => $value) {
                $response_arr[] = array(
                    'id'                =>  $value->id,
                    'user'              =>  $value->name,
                    'total_money'       => number_format($value->total_money, 0, ".", ",") . "đ",
                    'total_deposit'     => number_format($value->total_deposit, 0, ".", ",") . "đ",
                    'total_paid'        =>number_format($value->total_paid, 0, ".", ",") . "đ"
                );
            }
        }

        return json_encode($response_arr);
    }

    public function deposit(Request $request, User $user) {
        $user_login = auth()->user();
        if($user_login->is_admin != 1) {
            return back()->with('message', 'Access Denied!');
        }

        $request->validate([
            'money_deposit' => 'required',
        ]);

        if(!empty($request->money_deposit) && $request->money_deposit > 0) {
            History_payment::create([
                'user_id' => $user->id,
                'order_id' => 0,
                'amount' => $request->money_deposit,
                'note' => 'Transfer money',
                'disabled' => 0,
            ]);
        }

        return back()->with('message', 'Deposit successfully!');
    }

    public function withdrawal(Request $request, User $user) {
        $user_login = auth()->user();
        if($user_login->is_admin != 1) {
            return back()->with('message', 'Access Denied!');
        }

        $request->validate([
            'money_paid' => 'required',
        ]);

        if ($user->total_money < $request->money_paid) {
            return back()->with('message', 'Số tiền không đủ!');
        }

        if(!empty($request->money_paid) && $request->money_paid > 0) {
            History_payment::create([
                'user_id' => $user->id,
                'order_id' => 0,
                'amount' => '-'.($request->money_paid),
                'note' => (!empty($request->note)) ? $request->note : 'Withdrawal',
                'disabled' => 0,
            ]);
        }

        return back()->with('message', 'Withdrawal successfully!');
    }
}