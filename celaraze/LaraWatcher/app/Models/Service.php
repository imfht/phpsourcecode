<?php

namespace App\Models;

use DateTimeInterface;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    public function server()
    {
        return $this->hasOne(Server::class, 'id', 'server_id');
    }
}
