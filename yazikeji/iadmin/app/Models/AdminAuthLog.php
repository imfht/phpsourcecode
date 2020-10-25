<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAuthLog extends Model
{
    protected $table = 'sys_admins_auth_logs';

    public $timestamps = false;

    protected $fillable = ['admins_id', 'platform_info', 'browser_info', 'ip_address', 'login_time'];
}
