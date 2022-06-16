<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Http\Models\Product;

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
}
