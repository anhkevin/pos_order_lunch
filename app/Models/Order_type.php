<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_type extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_date','order_name','shop_id','column_name','description','price_every_order','status_id','pay_type','is_default', 'assign_user_id'];

    /**
     * Get the status of the order.
     */
    public function status_type()
    {
        return $this->belongsTo('App\Status', 'status_id', 'id');
    }
}
