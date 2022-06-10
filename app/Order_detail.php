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
    protected $fillable = ['product_id', 'order_id', 'price'];
}
