<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序统一接管
 */
namespace app\system\validate;
use think\Validate;

class Miniapp extends Validate{

    protected $rule = [
        'id'           => 'require|number',
        'title'        => 'require',
        'view_pic'     => 'require',
        'style_pic'    => 'require|array',
        'version'      => 'require',
        'expire_day'   => 'require|integer',
        'sell_price'   => 'require|float',
        'market_price' => 'require|float',
        'is_manage'    => 'require|integer',
        'miniapp_dir'  => 'require|alphaNum',
        'describe'     => 'require',
        'qrcode'       => 'require',
        'is_psp'       => 'require|number|between:0,1',
        //小程序登录
        'code'         => 'require|length:32',
        //添加用户小程序
        'member_id'    => 'require|integer',
        'miniapp_id'   => 'require|integer',
        'appname'      => 'require',
        'uid'          => 'require|integer',
        //管理添加小程序
        'order_id'       => 'require|integer',
        'miniapp_appid'  => 'require',
        'miniapp_secret' => 'require',
        'mp_appid'       => 'require',
        'mp_secret'      => 'require',
        'mp_token'       => 'require',
        'mp_aes_key'     => 'require',
    ];

    protected $message = [
        'id'           => 'ID丢失',
        'title'        => '小程序标题必须填写',
        'view_pic'     => '展示图没有选择默认图片',
        'style_pic'    => '必须选择展示图',
        'version'      => '更新版本号必须填写',
        'expire_day'   => '体验天数必须填写,且必须是整数',
        'sell_price'   => '销售价必须填写',
        'market_price' => '市场价必须填写',
        'is_manage'    => '请选择是否开启后台管理中心',
        'miniapp_dir'  => '小程序只能填写数字或字母',
        'describe'     => '描述内容必须填写',
        'qrcode'       => '展示二维码必须填写',
        'is_psp'       => '服务商支付模式必须选择',
        //小程序登录
        'code' => '登录认证失败',
        //后台管理用户应用
        'member_id'  => '用户验证失败,请重新登录',
        'miniapp_id' => '授权应用ID必须填写',
        'appname'    => '小程序名称必须填写',
        'uid'        => '应用管理员必须填写',
        //管理添加小程序
        'order_id'       => '授权应用ID必须填写',
        'miniapp_appid'  => 'AppID(小程序)必须填写',
        'miniapp_secret' => 'AppSecret(小程序)必须填写',
        'mp_appid'       => 'AppID(公众号)必须填写',
        'mp_secret'      => 'AppSecret(公众号)必须填写',
        'mp_token'       => 'Token(公众号)必须填写',
        'mp_aes_key'     => 'EncodingAESKey必须填写',
    ];
    
    protected $scene = [
        'edit' => ['id','title','describe','view_pic','style_pic','version','expire_day','sell_price','market_price','is_manage','miniapp_dir'],
        'add'  => ['title','describe','view_pic','style_pic','version','expire_day','sell_price','market_price','is_manage','miniapp_dir'],
        'addAuthorizar'  => ['member_id','miniapp_id','appname'],
        'editAuthorizar' => ['id','uid'],
        /**管理中心应用添加和编辑*/
        'editApp'     => ['id','member_id','appname','is_psp'],
        'editMiniapp' => ['id','member_id','appname','is_psp','miniapp_appid','miniapp_secret','mp_appid','mp_secret','mp_token','mp_aes_key'],
        'editOfficia' => ['id','member_id','appname','is_psp','mp_appid','mp_secret','mp_token','mp_aes_key'],
        'editProgram' => ['id','member_id','appname','is_psp','miniapp_appid','miniapp_secret'],
        'login'       => ['code'],
    ];
}