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

class AdminAuthGroupModel extends Model
{
    public function setRulesAttr($data)
    {
        if (is_array($data)) {
            return implode(',', $data);
        } else {
            return $data;
        }
    }
}
