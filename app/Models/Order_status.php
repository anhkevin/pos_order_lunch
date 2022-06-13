<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order_status extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_date','status_id'];
}
