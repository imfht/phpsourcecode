<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 支付配置
 */
namespace app\system\validate;
use think\Validate;

class Payment extends Validate{

    protected $rule = [
        'id'          => 'require|integer',
        'app_id'      => 'require',
        'public_key'  => 'require',
        'private_key' => 'require',
        'sort'        => 'require|integer',
        'appsecet'    => 'appsecet',
        //微信
        'mch_id'      => 'require',
        'key'         => 'require|length:32',
        'cert_path'   => 'require',
        'key_path'    => 'require',
    ];
    
    protected $message = [
        'id'              => '{%id_error}',
        'app_id'          => '支付应用ID必须选择',
        'public_key'      => '支付必须选择',
        'private_key'     => '支付密钥必须选择',
        'sort'            => '排序序列必须填写',
        //微信
        'mch_id'          => '商户号必须填写',
        'key.require'     => 'API密钥必须填写',
        'key.length'      => 'API密钥只能是32位的数字',
    ];
    protected $scene = [
        'alipay'   => ['id','app_id','public_key','private_key'],
        'wechat'   => ['id','mch_id','key'],
    ];
}