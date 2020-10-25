<?php
// 用户关联组模型       
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\model;

class AuthMemberGroupModel extends BaseModel
{
	protected $type       = [
        // 设置birthday为时间戳类型（整型）
        'auth_time' => 'timestamp:Y/m/d H:i:s',
        'group_id'    =>  'integer',
        'status'    =>  'integer',
    ];
    //定义时间戳字段名 
    //protected $createTime = 'create_time';
    //protected $updateTime = 'update_time';
}