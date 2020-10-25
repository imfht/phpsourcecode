<?php

/**
 * 实名认证
 */

namespace app\member\middle;


class RealMiddle extends \app\base\middle\BaseMiddle {


    private $info = [];
    private $config = [];
    private $userId = 0;


    private function getInfo() {
        if ($this->info) {
            return $this->info;
        }
        $this->userId = intval($this->params['user_id']);
        $this->info = target('member/MemberReal')->getWhereInfo([
            'A.user_id' => $this->userId
        ]);
        return $this->info;
    }

    private function getReceive() {
        $type = intval($this->params['val_type']);
        $userInfo = $this->params['user_info'];
        if (!$type) {
            $receive = $userInfo['tel'];
        } else {
            $receive = $userInfo['email'];
        }
        return $receive;
    }

    protected function meta() {
        $this->setMeta('实名认证');
        $this->setName('实名认证');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '实名认证',
                'url' => url()
            ]
        ]);
        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $info = $this->getInfo();
        if(empty($info) || !$info['status']) {
            $this->stop('请完成实名认证!', 302, url('info'));
        }
        return $this->run([
            'info' => $info
        ]);
    }

    protected function info() {
        $info = $this->getInfo();
        if($info['status'] == 1) {
            return $this->stop('您的申请正在审核中！', url('index'));
        }
        return $this->run([
            'info' => $info
        ]);

    }


    protected function post() {
        $info = $this->getInfo();
        if ($info['status'] == 1) {
            return $this->stop('您的申请已提交，请勿重复提交！');
        }
        if ($info['status'] == 2) {
            return $this->stop('您的实名制认证已成功，无法再次修改！');
        }
        $this->params['name'] = html_clear($this->params['name']);
        if (empty($this->params['name']) || empty($this->params['idcard']) || empty($this->params['card_image']) || empty($this->params['card_image_back']) || empty($this->params['card_image_hand'])) {
            return $this->stop('认证信息不完整！');
        }


        $model = target('member/MemberReal');

        $model->beginTransaction();

        if(!$model->real($this->params['user_id'], $this->params['name'], $this->params['idcard'], $this->params['card_image'], $this->params['card_image_back'], $this->params['card_image_hand'])) {
            return $this->stop($model->getError());
        }

        $receive = $this->getReceive();
        if (empty($receive)) {
            return $this->stop('该验证方式未绑定，请使用其他验证方式！');
        }

        if (!target('member/Member', 'service')->checkVerify($receive, $this->params['val_code'], 2)) {
            return $this->stop(target('member/Member', 'service')->getError());
        }
        $model->commit();

        return $this->run([], '认证资料提交成功！');
    }

    protected function getCode() {
        $receive = $this->getReceive();
        if (empty($receive)) {
            return $this->stop('该验证方式未绑定，请使用其他验证方式！');
        }
        if (!target('member/Member', 'service')->getVerify($receive, '', 0, 2)) {
            return $this->stop(target('member/Member', 'service')->getError());
        }
        return $this->run([], '验证码已发送,请注意查收!');
    }

}