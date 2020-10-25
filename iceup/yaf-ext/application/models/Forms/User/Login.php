<?php

namespace Forms\User;

/**
 * 用户登录表单
 */
class LoginModel extends \Forms\AbstractModel {

    /**
     * 表单字段
     * 
     * @var array
     */
    protected $_fields = array(
        'mobile'   => array(
            'label'    => '手机号码',
            'name'     => 'mobile',
            "validate" => array(
                array("type" => "string", "min" => "11", "max" => "11", "msg" => "手机号码输入格式不对")
            ),
        ),
        'password' => array(
            'label'    => '密码',
            'name'     => 'password',
            "validate" => array(
                array("type" => "string", "min" => "6", "max" => "18", "msg" => "密码长度6到18位")
            ),
        )
    );

}
