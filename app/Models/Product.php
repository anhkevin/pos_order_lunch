<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    // use HasFactory;

    protected $fillable = ['id','name','price', 'type', 'shop_id', 'dish_type_name', 'dish_photo'];
}
