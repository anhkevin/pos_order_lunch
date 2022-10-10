<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
class WebNotificationController extends Controller
{
  
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $user = auth()->user();
        if($user->is_admin != 1) {
            return redirect('/');
        }
        return view('push.push-notificaiton');
    }
  
    public function storeToken(Request $request)
    {
        auth()->user()->update(['device_key'=>$request->token]);
        return response()->json(['Token successfully stored.']);
    }
  
    public function sendWebNotification(Request $request)
    {
        $user = auth()->user();
        if($user->is_admin != 1) {
            return redirect('/');
        }
        
        $url = 'https://fcm.googleapis.com/fcm/send';
        $FcmToken = User::whereNotNull('device_key')->pluck('device_key')->all();
          
        $serverKey = 'AAAAhzDo5no:APA91bHeoCIHvGYYijhjb58IkwHF3Db6i4jHR0nCg3lsxi2RPTALAh_LkNKMI5VfNwxe1EYdbWoY-HcDKhdqb422z3t3AsLJRoclIuD5wYLngtU43iYfE9lFKlikEbCtwm16e5Bi-3Ql';
  
        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
        // die(Var_dump($headers));
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }        
        // Close connection
        curl_close($ch);
        // FCM response
        dd($result);        
    }
}