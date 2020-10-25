<?php

/**
 * 提交反馈
 */

namespace app\member\api;

use \app\member\api\MemberApi;

class FeedbackApi extends MemberApi {

    protected $_middle = 'member/Feedback';

    public function push() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'content' => $this->data['content'],
        ])->post()->export(function ($data, $msg) {
            $this->success($msg);
        }, function ($message, $code) {
            $this->error($message, $code);
        });

    }

}