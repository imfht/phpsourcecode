<?php

/**
 * 银行卡管理
 */

namespace app\member\middle;


class CardMiddle extends \app\base\middle\BaseMiddle {


    private $info = [];
    private $userId = 0;

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

    protected function meta($title = '我的银行卡', $name = '我的银行卡') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '我的银行卡',
                'url' => url('index')
            ]
        ]);
        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $list = target('member/PayCard')->loadList(['A.user_id' => $this->params['user_id']]);
        $list = target('member/PayCard')->showList($list);
        return $this->run([
            'pageList' => $list
        ]);
    }

    protected function info() {
        $info = target('member/PayCard')->getWhereInfo([
            'A.user_id' => $this->params['user_id'],
            'A.card_id' => $this->params['card_id']
        ]);
        $info = target('member/PayCard')->showInfo($info);
        if(empty($info)) {
             return $this->stop('银行卡信息不存在!');
        }
        return $this->run([
            'info' => $info,
        ]);
    }

    protected function realInfo() {
        $realInfo = target('member/MemberReal')->getWhereInfo([
            'A.user_id' => $this->params['user_id']
        ]);

        if(!$realInfo['status']) {
            return $this->stop('请先通过实名信息认证!', 500, url('member/Real/index'));
        }
        $bankList = target('member/PayBank')->loadList();
        return $this->run([
            'realInfo' => $realInfo,
            'bankList' => $bankList
        ]);
    }

    protected function del() {
        $this->info();
        $info = $this->data['info'];
        if(!$this->status) {
            return $this;
        }
        $status = target('member/PayCard')->where([
            'card_id' => $info['card_id']
        ])->delete();
        if(!$status) {
            return $this->stop('删除银行卡失败');
        }
        return $this->run([], '银行卡删除成功', url('member/Card/index'));
    }

    protected function post() {
        $account = $this->params['account'];
        $cardId = intval($this->params['card_id']);
        if(empty($account)) {
            return $this->stop('请输入银行卡号！');
        }

        $bankInfo = target('member/PayBank')->bankInfo($account);
        if(!$bankInfo) {
            return $this->stop(target('member/PayBank')->getError());
        }

        $cardWhere = [
            'account' => $account,
            'A.user_id' => $this->params['user_id'],
        ];
        if($cardId) {
            $this->info();
            if(!$this->status) {
                return $this;
            }
            $cardWhere['_sql'] = "A.card_id <> " . $cardId;
        }
        $info = target('member/PayCard')->getWhereInfo($cardWhere);
        if($info) {
            return $this->stop('该卡已被添加，请更换其他卡片！');
        }

        $this->realInfo();
        $name = $this->data['realInfo']['name'];

        $receive = $this->getReceive();
        if (empty($receive)) {
            return $this->stop('该验证方式未绑定，请使用其他验证方式！');
        }
        if (!target('member/Member', 'service')->checkVerify($receive, $this->params['val_code'], 2)) {
            return $this->stop(target('member/Member', 'service')->getError());
        }

        $data = [
            'card_id' => $cardId,
            'user_id' => $this->params['user_id'],
            'account' => $account,
            'account_name' => $name,
            'label' => $bankInfo['label'],
            'bank' => $bankInfo['name'],
            'bank_color' => $bankInfo['color'],
            'type' => $bankInfo['type'],
        ];

        if(!target('member/PayCard')->saveData($cardId ? 'edit' : 'add', $data)) {
            return $this->stop(target('member/PayCard')->getError());
        }
        return $this->run([], '银行卡保存成功！');
    }


}