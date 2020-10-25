<?php

/**
 * 评论
 */
namespace app\order\api;

class CommentApi extends \app\member\api\MemberApi {

    protected $_middle = 'order/Comment';

    /**
     * 评价商品
     */
    public function push() {
        target($this->_middle, 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
            'id' => $this->data['id'],
            'level' => $this->data['level'],
            'content' => $this->data['content'],
            'images' => $this->data['images'],
            'store' => $this->data['store'],
        ])->info()->post()->export(function ($data, $msg) {
            $this->success($msg, $data);
        }, function ($message, $code) {
            $this->error($message, $code);
        });

    }


}