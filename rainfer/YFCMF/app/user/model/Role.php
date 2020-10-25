<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\user\model;

use think\Model;

/**
 * 角色模型
 * @package app\admin\model
 */
class Role extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'create_time';
    protected $updateTime         = false;

    public function roles()
    {
        return $this->belongsToMany('User', 'role_access', 'uid', 'role_id');
    }
}
