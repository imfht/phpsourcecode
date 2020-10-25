<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Star extends Model
{
    protected $fillable = ["user_id","stars"];
}
