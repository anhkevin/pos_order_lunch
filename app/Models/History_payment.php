<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History_payment extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','user_id','order_id','amount','note','disabled'];
}
