<?php

namespace App\Handler;

use App\Model\WxUserSubscribeLog;
use Swoole;

/**
 * 微信用户资料同步.
 *
 * @property \EasyWeChat\Foundation\Application $easywechat
 */
class WxSaveUserSubLog implements Swoole\IFace\EventHandler
{
    public function trigger($type, $data)
    {
        if (!isset($data['openid']) || !$data['openid']) {
            return false;
        }
        $saveData = [
            'openid'    => $data['openid'],
            'subscribe' => isset($data['subscribe']) ? (int) $data['subscribe'] : -1,
            'createTime'    => time(),
        ];
        $model = model('WxUserSubscribeLog');
        return $model->put($saveData);
    }
}
