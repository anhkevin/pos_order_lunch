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
    protected $fillable = ['order_date','order_name','shop_id','status_id','pay_type','is_default', 'assign_user_id'];
}
