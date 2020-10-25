<?php
// 权限模型       
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\model;

use think\Model;

class AdminAuthGroupAccessModel extends Model
{
	// 设置完整的数据表（包含前缀）
    // protected $table = 'think_access';

    // 设置数据表（不含前缀）
    // protected $name = 'auth_rule';

	// 设置birthday为时间戳类型（整型）
    // protected $type       = [
    //     'birthday' => 'timestamp',
    // ];
    // public function AuthGroupAccess()
    // {
    //  //   return $this->belongsTo('AuthGroupAccess','uid');
    //      return $this->hasMany('AuthGroupAccess','art_id');
    // }

    public function groups()
    {
        return $this->hasOne('AdminAuthGroupModel', 'id', 'group_id');
    }
}