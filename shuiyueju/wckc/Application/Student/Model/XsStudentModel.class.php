<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Student\Model;
use Think\Model;
use Think\Page;

/**
 * 学生基础模型
 */
class XsStudentModel extends Model{
    protected $_validate = array(
        array('st_name', '1,12', '姓名长度不合法', self::EXISTS_VALIDATE, 'length'),
        //array('content', '1,40000', '内容长度不合法', self::EXISTS_VALIDATE, 'length'),
    );

    protected $_auto = array(
        array('update_time', NOW_TIME, self::MODEL_BOTH),
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_INSERT),
        array('uid', 'is_login',3, 'function'),
    );

}
