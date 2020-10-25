<?php
namespace app\green\validate;
use think\Validate;

class GreenOperate extends Validate{

    protected $rule = [
        'id'                => 'require|integer',
        'member_miniapp_id' => 'require|integer',
        'uid'               => 'require|integer',
        'operate_name'      => 'require',
        'address'           => 'require',
        'tel'               => 'require',
        'company'           => 'require',
        'longitude'         => 'require|float',
        'latitude'          => 'require|float',
    ];

    protected $message = [
        'id'                => '配置ID丢失',
        'member_miniapp_id' => '应用ID丢失',
        'uid'               => '创始人必须选择',
        'operate_name'      => '运营商必须填写',
        'address'           => '地址必须填写',
        'tel'               => '电话必须填写',
        'company'           => '运营公司必须填写',
        'longitude'         => '经纬度必须选择',
        'latitude'          => '经纬度必须选择',
    ];

    protected $scene = [
        'edit'     => ['member_miniapp_id','uid','operate_name','address','tel','company','longitude','latitude']
    ];
}