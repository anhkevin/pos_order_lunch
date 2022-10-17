<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\NotificationsService;
class WebNotificationController extends Controller
{
  
    protected $notificationsService;

    public function __construct(NotificationsService $notificationsService)
    {
        $this->middleware('auth');
        $this->notificationsService = $notificationsService;
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
        
        $this->notificationsService->sendNotifications($request->title, $request->body);     
    }
}