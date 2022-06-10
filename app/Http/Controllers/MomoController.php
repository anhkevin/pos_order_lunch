<?php

namespace App\Http\Controllers;

use App\Order;
use App\Order_detail;
use App\Product;
use App\Status;
use Illuminate\Http\Request;
use DB;

class MomoController extends Controller
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
    public function create(Order $order)
    {
        // die(\var_dump($order->id));
        $endpoint = env('MOMO_ENDPOINT_API');
        $partnerCode = env('MOMO_PARTNER_CODE');
        $accessKey = env('MOMO_ACCESS_KEY');
        $serectkey = env('MOMO_SECRET_KEY');
        $orderInfo = "Thanh toán qua MoMo";
        $amount = "2000";
        $orderId = time() . "-" .$order->id;
        $redirectUrl = env('MOMO_REDIRECT_URL');
        $ipnUrl = env('MOMO_SUCCESS_URL');
        $extraData = "";

        $requestId = time() . "-" .$order->id;
        $requestType = "captureWallet";

        //before sign HMAC SHA256 signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $serectkey);
        $data = array('partnerCode' => $partnerCode,
            'partnerName' => "Phan Tiến Ánh",
            "storeId" => "MomoDatComStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature);
            // die(var_dump($signature));
        $result = execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json

        if (empty($jsonResult['payUrl'])) {
            $jsonResult = array();
            $jsonResult['payUrl'] = "/";
        }
        return redirect($jsonResult['payUrl']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirect_url(Request $request)
    {
        $accessKey = env('MOMO_ACCESS_KEY');
        $secretKey = env('MOMO_SECRET_KEY');
        $redirectUrl = env('MOMO_REDIRECT_URL');
        $ipnUrl = env('MOMO_SUCCESS_URL');
        $requestType = "captureWallet";

        $partnerCode = $request->input('partnerCode');
        $orderId = $request->input('orderId');
        $requestId = $request->input('requestId');
        $amount = $request->input('amount');
        $orderInfo = $request->input('orderInfo');
        $orderType = $request->input('orderType');
        $transId = $request->input('transId');
        $resultCode = $request->input('resultCode');
        $message = $request->input('message');
        $payType = $request->input('payType');
        $responseTime = $request->input('responseTime');
        $extraData = $request->input('extraData');
        $signature = $request->input('signature');

        //Checksum
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&message=" . $message . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
			"&orderType=" . $orderType . "&partnerCode=" . $partnerCode . "&payType=" . $payType . "&requestId=" . $requestId . "&responseTime=" . $responseTime .
			"&resultCode=" . $resultCode . "&transId=" . $transId;

        $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);
        if ($signature == $partnerSignature) {
            if ($resultCode == '0') {
                $result = '<div class="alert alert-success"><strong>Payment status: </strong>Success</div>';
            } else {
                $result = '<div class="alert alert-danger"><strong>Payment status: </strong>' . $message .'</div>';
            }
        } else {
            $result = '<div class="alert alert-danger">This transaction could be hacked, please check your signature and returned signature</div>';
        }

        return view('momo_redirect', compact('result'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function ipn_url(Request $request)
    {
        die(\var_dump($request));
    }
}