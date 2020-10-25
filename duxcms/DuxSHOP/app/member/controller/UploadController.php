<?php

/**
 * 会员上传
 */

namespace app\member\controller;

class UploadController extends \app\member\controller\MemberController {

    public function index() {
        $return = array('status' => 1, 'info' => '上传成功', 'data' => []);
        target('member/Upload', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->post()->export(function ($data) use ($return) {
            $data = reset($data);
            $return['data'] = $data;
            $this->json($return);
        }, function ($message) use ($return) {
            $return['status'] = 0;
            $return['info'] = $message;
            $this->json($return);
        });
    }

    public function editor() {
        target('member/Upload', 'middle')->setParams([
            'user_id' => $this->userInfo['user_id'],
        ])->post()->export(function ($data, $msg) {
            $info = reset($data);
            $return = $info;
            $return['error'] = 0;
            $this->json($return);
        }, function ($msg) {
            $return = [];
            $return['error'] = 1;
            $return['message'] = $msg;
            $this->json($return);
        });
    }

}