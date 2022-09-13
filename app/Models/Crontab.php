<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crontab extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','set_day', 'set_hour', 'set_group_laka', 'set_content', 'disabled'];
}
