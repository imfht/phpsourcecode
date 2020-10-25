<?php
namespace app\green\validate;
use think\Validate;

class GreenDevice extends Validate{

    protected $rule = [
        'id'                => 'require|integer',
        'member_miniapp_id' => 'require|integer',
        'device_id'         => 'require|integer',
        'operate_id'        => 'require|integer',
        'manage_uid'        => 'require|integer',
        'title'             => 'require',
        'address'           => 'require',
        'longitude'         => 'require|float',
        'latitude'          => 'require|float',
    ];

    protected $message = [
        'id'                => '配置ID丢失',
        'member_miniapp_id' => '应用ID丢失',
        'device_id'         => '设备编号必须填写',
        'operate_id'        => '运营商必须选择',
        'manage_uid'        => '管理员必须选择',
        'title'             => '设备名称必须填写',
        'address'           => '设备地址必须填写',
        'longitude'         => '设备经纬度必须选择',
        'latitude'          => '设备经纬度必须选择',
    ];

    protected $scene = [
        'edit'     => ['member_miniapp_id','device_id','operate_id','manage_uid','title','address','longitude','latitude']
    ];
}