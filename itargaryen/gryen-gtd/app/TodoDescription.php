<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class TodoDescription extends Eloquent
{
    protected $fillable = [
        'todo_id',
        'content',
    ];
}
