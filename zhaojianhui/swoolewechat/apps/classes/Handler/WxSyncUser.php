<?php

namespace App\Handler;

use Swoole;

/**
 * 微信用户资料同步.
 *
 * @property \EasyWeChat\Foundation\Application $easywechat
 */
class WxSyncUser implements Swoole\IFace\EventHandler
{
    protected $easywechat;

    public function trigger($type, $data)
    {
        $openId = $data['openid'] ?? '';
        if (!$openId) {
            return false;
        }
        $wxUserSer = new \App\Service\WxUser();
        $wxUserSer->syncUser($openId);
    }
}
