<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_detail extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['product_id','product_name', 'order_id','number', 'price', 'dish_type_name'];
}
