<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use DB;

class AdminUser extends Authenticatable
{
    use  HasApiTokens;

}
