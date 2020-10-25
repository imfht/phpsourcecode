<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceTrack extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'service_tracks';

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }
}
