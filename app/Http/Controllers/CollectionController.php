<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Product;
use App\Models\Shop;

class CollectionController extends Controller
{
    protected $headers_shoppe_food = [];
    
    protected $client;

    public function __construct() {

        $this->headers_shoppe_food = [         
            "X-Foody-App-Type" => "1004",
            "X-Foody-Api-Version" => "1",
            "X-Foody-Client-Type" => "1",
            "X-Foody-Client-Version" => "3.0.0",
            "X-Foody-Client-Id" => "00000000-50f2-2bb1-ffff-fffffae68467",
            // "X-Foody-Old-Client-Id" => "00000000-50f2-2bb1-ffff-fffffae68467",
            // "X-Foody-Client-Language" => "en",
            // "User-Agent" => "Foody/5.2.10 (Google Android SDK built for x86; Android_Emulator 8.1.0; density xxhdpi)",
            // "Authorization" => "signature 57db53e500bcba98615556e6b50efdccdcda18a63e1af72823e681e777eb105d",
            // "Host" => "gappapi.deliverynow.vn",
            // "Connection" => "Keep-Alive",
            // "Accept-Encoding" => "gzip",
            // "content-length" => 0
        ];

        $this->client = new Client([
            'base_uri'  => 'https://gappapi.deliverynow.vn/api/',
            'headers' => $this->headers_shoppe_food
        ]);
    }

    public function index()
    {
        return view('collection.index');
    }
    
    public function get_api_shoppe(Request $request)
    {
        $url_shoppe_food = $request->url;

        $param_domain = str_replace('https://shopeefood.vn/', '', $url_shoppe_food);

        $sub_url = 'delivery/get_from_url?url=' . $param_domain;

        $response_get_from_url = $this->get_from_url($sub_url);
    
        if(!empty($response_get_from_url)) {
            $response_dishes = $this->get_api_delivery_dishes($response_get_from_url['delivery_id']);
        }

        $response_detail = $this->get_detail($response_get_from_url['delivery_id']);

        return [
            'detail' => json_decode($response_detail, true),
            'dishes' => json_decode($response_dishes, true)
        ];
    }

    public function get_from_url($url) {

        $data = [];

        $response = $this->client->request('GET', $url)->getBody(); 

        $json = json_decode($response, true);

        if($json['result'] == 'success') {

            $data['restaurant_id'] = $json['reply']['restaurant_id'];

            $data['delivery_id'] = $json['reply']['delivery_id'];
        }

        return $data;
    }

    public function get_api_delivery_dishes($delivery_id) {
        
        $sub_url = 'dish/get_delivery_dishes?id_type=2&request_id=' . $delivery_id;

        $response = $this->client->request('GET', $sub_url)->getBody(); 

        return $response;
    }

    public function get_detail($delivery_id) {

        $url = 'https://gappapi.deliverynow.vn/api/delivery/get_detail?id_type=2&request_id=' . $delivery_id;

        $response = $this->client->request('GET', $url)->getBody(); 

        return $response;
    }

    public function check_exist_by_key($data, $rq, $keys) {
        
        $data_change = [];

        foreach($keys as $value) {

            if($data[$value] != $rq[$value]) {
                
                $data_change[$value] = $rq[$value];

            }

        }
        
        return $data_change;
    }

    public function create_or_update_shop(Request $request) {

        $data_shop = $request->shop_infor;
        // $data_dishes = $request->dishes;

        $shop_infor = Shop::where('delivery_id', '=', $data_shop['delivery_id'])->first();

        $shop = [];

        if(empty($shop_infor)) {

            $shop = Shop::create([
                'name' => $data_shop['name'],
                'address'   => $data_shop['address'],
                'ship'      => $data_shop['ship'],
                'voucher'   => $data_shop['voucher'],
                'delivery_id'  => $data_shop['delivery_id']
            ]);

        } else {

            $keys = ['name', 'address', 'ship', 'voucher', 'delivery_id'];

            $data_change = $this->check_exist_by_key($shop_infor->toArray(), $data_shop, $keys);

            if(!empty($data_change)) {

                $shop = $shop_infor->update($data_change);

            }

        }

        return response()->json([
            'status'    => 1,
            'result'    => $shop_infor ? $shop_infor : $shop
        ]);
    }

    public function create_dish(Request $request) {

        // $shop = Shop::find($shop_id);
        $data = $request->dishes;
        
        $delivery_id = $request->delivery_id;

        $shop = Shop::where('delivery_id', '=', $delivery_id)->first();

        $shop_id = $shop['id'];

        $products = Product::where('shop_id', '=', $shop_id)->get();

        $ids = [];
        
        foreach($products as $product) {
            array_push($ids, $product['id']);
        }

        Product::destroy($ids);

        foreach($data as $key => &$item) {

            $item['price'] = $item['discount_price'] == 0 ? $item['price'] : $item['discount_price'];
            $item['type'] = 1;
            $item['shop_id'] = $shop['id'];

            unset($item['price_text']);
            unset($item['discount_price']);
            unset($item['discount_price_text']);
            unset($item['description']);

        }

        $product_infor = Product::insert($data);

        if($product_infor) {
            return response()->json([
                'status'    => 1,
                'result'    => $product_infor
            ]);
        }

        return response()->json([
            'status'    => 0,
            'result'    => []
        ]);

    }
}
