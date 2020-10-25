<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

/**
 * 分类模型
 */
class MemberExtendGroupModel extends Model{

    /* 自动验证规则 */
    protected $_validate = array(
        array('profile_name', 'require', '名称不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
    );

    /* 自动完成规则 */
    protected $_auto = array(
        array('createTime', NOW_TIME, self::MODEL_INSERT),
    );
}
