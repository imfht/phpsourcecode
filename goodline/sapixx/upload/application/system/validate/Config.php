<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 配置数据验证
 */
namespace app\system\validate;
use think\Validate;

class Config extends Validate{

    protected $rule = [
        'price'       => 'require',
        //站点ID
        'name'        => 'require',
        'title'       => 'require',
        'url'         => 'require',
        'logo'        => 'require',
        'keywords'    => 'require',
        'description' => 'require',
        'icp'         => 'require',
        'contacts'    => 'require',
        'address'     => 'require',
        //微信
        'app_id'      => 'require',
        'mch_id'      => 'require',
        'key'         => 'require',
        'cert_path'   => 'require',
        'key_path'    => 'require',
        //短信
        'aes_key'     => 'require',
        'secret'      => 'require',
        'sign_name'   => 'require',
        'tpl_id'      => 'require',
        //阿里云市场
        'appcode'     => 'require|alphaNum',
        //云市场
        'app_id'     => 'require',
        'secret_id'  => 'require',
        'secret_key' => 'require',
        'encry_key'  => 'require',
        'token'      => 'require',
        //云市场项目关联
        'miniapp_id' => 'require',
        'product_id' => 'require',
        //服务号
        'qrcode'    => 'require',
    ];

    protected $message = [
        'price'       => '价格必须填写',
        //站点ID
        'name'        => '站点名称必须填写',
        'title'       => '站点标题必须填写',
        'url'         => '访问域名必须填写',
        'logo'        => '站点LOGO必须填写',
        'keywords'    => 'SEO关键词必须填写',
        'description' => 'SEO描述必须填写',
        'icp'         => 'ICP备案号必须填写',
        'contacts'    => '联系方式必须填写',
        'address'     => '公司地址必须填写',
        //微信
        'key'         => 'API密钥必须填写',
        'app_id'      => '应用ID必须填写',
        'mch_id'      => '商户ID必须填写',
        'cert_path'   => '支付证书必须填写',
        'key_path'    => '证书密钥必须填写',
        //短信
        'aes_key'     => 'AccessKeyId必须填写',
        'secret'      => 'AccessKeySecret/AppSecret必须填写',
        'sign_name'   => '短信签名必须填写',
        'tpl_id'      => '模板ID必须填写',
        //阿里云市场
        'appcode'     => '只能输入数字或字母',
        //云市场
        'app_id'     => '应用ID必须填写',
        'secret_id'  => 'APIID必须填写',
        'secret_key' => 'API密钥必须填写',
        'encry_key'  => 'EncryKey必须填写',
        'token'      => 'Token必须填写',
        //云市场项目关联
        'miniapp_id' => '应用ID必须填写',
        'product_id' => '云市场项目ID必须填写',
    ];

    protected $scene = [
        'web'              => ['name', 'title', 'url', 'logo', 'keywords', 'description', 'icp', 'contacts', 'address'],
        'wepay'            => ['app_id', 'mch_id', 'key', 'cert_path', 'key_path'], //微信支付
        'wechatopen'       => ['app_id', 'secret', 'token', 'aes_key'],      //微信开放平台
        'alisms'           => ['aes_key', 'secret', 'sign_name', 'tpl_id', 'price'],  //阿里云短信
        'aliapi'           => ['appcode', 'price'], //阿里云市场
        'wechatcloud'      => ['app_id', 'secret_id', 'secret_key', 'encry_key', 'token'], //腾讯云市场
        'cloud'            => ['miniapp_id', 'product_id'], //腾讯云市场项目关联
        'wechataccount'    => ['app_id', 'secret', 'token', 'aes_key'],   //腾讯云市场项目关联
    ];
}