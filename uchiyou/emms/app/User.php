<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    /**
     * 与模型关联的数据表
     *
     * @var string
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','job_type','tree_trunk_id','company_id','phone'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    // 3 用户表职位类型常量
    const USER_JOB_MANAGER = 1;
    const USER_JOB_NORMAL = 2;
    const USER_JOB_REPAIREMAN = 3;
    const USER_JOB_HOUSE_KEEPER = 4;
   
}
