<?php
namespace Home\Model;

use Think\Model;
class SellerApplyModel extends Model
{
    /* 自动验证规则 */
    protected $_validate = array(
        array('contact', 'require', '联系人不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('contact_info', 'require', '联系方式不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('shop_url', 'require', '店铺/网站地址不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
        array('brief_description', 'require', '商家简介不能为空', self::MUST_VALIDATE , 'regex', self::MODEL_BOTH),
    );
    protected $_auto = array(     
        array('create_time', NOW_TIME, self::MODEL_INSERT), 
    );
}

?>