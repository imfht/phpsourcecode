<?php

/**
 * 微信回复
 */
namespace app\wechat\model;

use app\system\model\SystemModel;

class WechatReplyModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'reply_id',
    ];

}