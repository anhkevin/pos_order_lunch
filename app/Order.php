<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'address', 'size', 'toppings', 'instructions', 'amount', 'discount', 'status_id','order_type','disabled', 'is_join'];

    /**
     * Get the customer that placed the order.
     */
    public function customer()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    /**
     * Get the status of the order.
     */
    public function status()
    {
        return $this->belongsTo('App\Status');
    }

    public function order_types()
    {
        return $this->belongsTo('App\Models\Order_type', 'order_type');
    }

    public function history_payments()
    {
        return $this->belongsTo('App\Models\History_payment', 'id', 'order_id');
    }

    public function user_info()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
