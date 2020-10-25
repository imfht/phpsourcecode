<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ip extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id','ips'];
}
