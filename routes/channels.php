<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     die(var_dump($id));
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('order_lunch_huyteam.{id}', function ($user, $id) {
    return (int) $user->id === (int) Order::find($id)->user_id;
});
