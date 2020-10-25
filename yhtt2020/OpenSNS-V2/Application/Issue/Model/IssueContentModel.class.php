<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Issue\Model;
use Think\Model;
use Think\Page;

/**
 * 文档基础模型
 */
class IssueContentModel extends Model{
    protected $_validate = array(
            array('title', '1,100', '标题长度不合法', self::EXISTS_VALIDATE, 'length'),
        array('content', '1,40000', '内容长度不合法', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('status', '1', self::MODEL_INSERT),
        array('uid', 'is_login',3, 'function'),
    );

}
