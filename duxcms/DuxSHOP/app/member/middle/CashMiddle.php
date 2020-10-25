<?php

/**
 * 账户提现
 */

namespace app\member\middle;


class CashMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'member/PayCash';


    protected function meta($title = '', $name = '', $url = '') {
        $this->setMeta($title);
        $this->setName($name);
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => '账户提现',
                'url' => url('member/Cash/index')
            ],
            [
                'name' => $name,
                'url' => $url
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }


    protected function data() {
        $type = intval($this->params['type']);
        $userId = intval($this->params['user_id']);
        $this->params['limit'] = intval($this->params['limit']);
        if ($type == 1) {
            $where['A.status'] = 1;
        }
        if ($type == 2) {
            $where['A.status'] = 2;
        }
        if ($type == 3) {
            $where['A.status'] = 0;
        }
        $where['A.user_id'] = $userId;
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'create_time desc');
        $list = $model->showList($list);

        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit
        ]);
    }

    protected function info() {
        $cashNo = html_clear($this->params['no']);
        $info = target('member/PayCash')->getWhereInfo([
            'A.cash_no' => $cashNo,
            'A.user_id' => intval($this->params['user_id'])
        ]);
        $info = target('member/PayCash')->showInfo($info);
        if (empty($info)) {
            return $this->stop('该提现单不存在!', 404);
        }
        return $this->run([
            'info' => $info,
        ]);
    }

    protected function apply() {
        $cardList = target('member/PayCard')->loadList(['A.user_id' => $this->params['user_id']]);
        $config = target('member/MemberConfig')->getConfig();
        return $this->run([
            'cashConfig' => [
                'clear_withdraw' => $config['clear_withdraw'],
                'clear_num' => $config['clear_num'],
                'clear_tax' => $config['clear_tax']
            ],
            'cardList' => $cardList
        ]);
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

    protected function applyPost() {
        $money = intval($this->params['money']);
        $userInfo = $this->params['user_info'];
        if(empty($money)) {
            return $this->stop('请输入提现金额！');
        }

        if(bccomp($money, 0, 2) !== 1) {
            return $this->stop('可提现金额不足!');
        }

        $config = target('member/MemberConfig')->getConfig();

        if(bccomp($money, $config['clear_withdraw'], 2) === -1) {
            return $this->stop('最少提现额度为'.$config['clear_withdraw'] . '元');
        }

        if($config['clear_num']) {
            $count = target('member/PayCash')->countList(['_sql' => 'A.status != 0 AND A.create_time >= ' .mktime(0,0,0,date('m'),1,date('Y')) . ' AND A.create_time <= ' . mktime(23,59,59,date('m'),date('t'),date('Y'))]);
            if($count >= $config['clear_num']) {
                return $this->stop('当月提现次已满，请于下月继续提现！');
            }
        }

        $cardInfo = target('member/PayCard')->getWhereInfo([
            'A.card_id' => $this->params['card_id'],
            'B.user_id' => $this->params['user_id']
        ]);
        if(empty($cardInfo)) {
            return $this->stop('银行卡选择不正确！');
        }

        $receive = $this->getReceive();
        if (empty($receive)) {
            return $this->stop('该验证方式未绑定，请使用其他验证方式！');
        }
        if (!target('member/Member', 'service')->checkVerify($receive, $this->params['val_code'], 2)) {
            return $this->stop(target('member/Member', 'service')->getError());
        }

        $cashNo = log_no();
        $data = [
            'cash_no' => $cashNo,
            'user_id' => $userInfo['user_id'],
            'money' => $money,
            'create_time' => time(),
            'status' => 1,
            'bank' => $cardInfo['bank'],
            'bank_label' => $cardInfo['label'],
            'bank_type' => $cardInfo['type'],
            'tax' => $config['clear_tax'],
            'account' => $cardInfo['account'],
            'account_name' => $cardInfo['account_name'],
        ];
        target('member/PayCash')->beginTransaction();
        if(!target('member/PayCash')->add($data)) {
            target('member/PayCash')->rollBack();
            return $this->stop('提现申请失败!');
        }
        $status = target('member/Finance', 'service')->account([
            'user_id' => $userInfo['user_id'],
            'money' => $money,
            'pay_no' => $cashNo,
            'pay_name' => '账户支付',
            'title' => '提现',
            'type' => 0,
            'deduct' => 1
        ]);
        if(!$status) {
            target('member/PayCash')->rollBack();
            return $this->stop(target('member/Finance', 'service')->getError());
        }
        target('member/PayCash')->commit();
        return $this->run([], '提现申请成功，请等待提现结果！');
    }


}