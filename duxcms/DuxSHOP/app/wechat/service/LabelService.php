<?php

namespace app\wechat\service;

/**
 * 标签接口
 */
class LabelService {

    /**
     * 栏目列表
     */
    public function attention($data) {

        if (!isWechat()) {
            return false;
        }

        $where = array();

        $target = target('wechat/Wechat', 'service');
        $target->init();
        $wechat = $target->wechat();

        if (!$data['open_id']) {
            $openId = \dux\Dux::cookie()->get('wechat_openid');
        } else {
            $openId = $data['open_id'];
        }
        if (!empty($openId)) {
            $info = $wechat->user->get($openId);
        } else {
            if (empty(request('get', 'code')) && empty(request('get', 'state'))) {
                $wechat->oauth->scopes(['snsapi_base'])->redirect(DOMAIN . URL)->send();
                exit;
            } else {
                $oathUser = $wechat->oauth->user();
                \dux\Dux::cookie()->set('wechat_openid', $oathUser->getId(), 31536000);
                $info = $wechat->user->get($oathUser->getId());
            }
        }
        return $info['subscribe'];
    }


}
