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

namespace app\admin\model;

use think\Model;

/**
 * 管理群组模型
 * @package app\admin\model
 */
class AuthGroup extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime         = 'create_time';
    protected $updateTime         = false;

    public function groups()
    {
        return $this->belongsToMany('Admin', 'auth_group_access', 'uid', 'group_id');
    }
}
