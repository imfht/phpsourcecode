<?php

/**
 * 微信素材
 */
namespace app\wechat\model;

use app\system\model\SystemModel;

class WechatMaterialImageModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'material_id',
        'into' => '',
        'out' => '',
    ];


}