<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    protected $table = 'directory';

    protected $fillable = ['userId', 'content','version'];
}
