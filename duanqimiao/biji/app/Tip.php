<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tip extends Model
{
    /**
     * @var array
     */
    protected $fillable = ["biji_id","biji_title","reporter_name","reported_id","reported_name"];
}
