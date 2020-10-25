<?php

/**
 * oath登录
 */

namespace app\member\api;

use \app\base\api\BaseApi;

class OathLoginApi extends BaseApi {

    /**
     * 回调授权
     */
    public function index() {
        $data = target('member/Member', 'service')->oauthUser($this->data['type'], $this->data['open_id'], $this->data['pay_id'], $this->data['nickname'], $this->data['avatar']);
        if(!$data) {
            $this->error(target('member/Member', 'service')->getError());
        }
        $this->success('ok', $data);

    }

}