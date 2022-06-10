<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id','name','price','type','disabled'];

    public function scopeType($query, $type)
    {
        $query->where('type', $type);
    }

    public function scopeOption($query)
    {
        $query->where('type', 2);
    }
}
