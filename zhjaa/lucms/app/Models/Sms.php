<?php

namespace App\Models;

class Sms extends Model
{

    protected $fillable = [
        'type', 'phone', 'ip', 'code'
    ];

}
