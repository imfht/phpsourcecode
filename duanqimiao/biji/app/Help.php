<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Help extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id','article_id','isHelp'];
}
