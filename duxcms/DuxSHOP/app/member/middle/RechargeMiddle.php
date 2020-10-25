<?php

/**
 * 会员充值
 */

namespace app\member\middle;


class RechargeMiddle extends \app\base\middle\BaseMiddle {

    protected function meta() {
        $this->setMeta('会员充值');
        $this->setName('会员充值');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '会员充值',
                'url' => url()
            ]
        ]);
        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $payList = target('member/PayConfig')->typeList(true, $this->params['platform'], false);
        $config = target('tools/Tools', 'service')->getConfig('member', 'recharge');
        $offline = $config['offline'] ? $config['offline_info'] : '';
        return $this->run([
            'payList' => $payList,
            'offline' => str_html($offline)
        ]);
    }

    protected function recharge() {
        $type = html_clear($this->params['type']);
        $money = intval($this->params['money']);

        if(empty($type)) {
            return $this->stop('请选择支付方式!');
        }
        if($money <= 0) {
            return $this->stop('充值金额错误，只能充值整数!');
        }
        $payList = target('member/PayConfig')->typeList(true, $this->params['platform'], false);
        $payTypeInfo = $payList[$type];
        if (empty($payTypeInfo)) {
            return $this->stop('该支付类型无法使用!');
        }

        $logNo = log_no($this->params['user_id']);
        $sign = data_sign($logNo);

        $model = target('member/PayRecharge');
        $model->beginTransaction();
        $data = [];
        $data['user_id'] = $this->params['user_id'];
        $data['money'] = $money;
        $data['recharge_no'] = $logNo;
        $data['status'] = 0;
        $data['create_time'] = time();
        if(!target('member/PayRecharge')->add($data)) {
            $model->rollBack();
            return $this->stop('充值订单创建失败!');
        }

        $data = target($payTypeInfo['target'], 'pay')->getData([
            'user_id' => $this->params['user_id'],
            'order_no' => $logNo,
            'money' => $money,
            'title' => '会员充值',
            'body' => '会员充值',
            'url' => url('member/Recharge/pay'),
            'app' => 'member',
        ], url('complete', ['pay_no' => $logNo, 'pay_sign' => $sign]));

        if (!$data) {
            $model->rollBack();
            return $this->stop(target($payTypeInfo['target'], 'pay')->getError());
        }
        $model->commit();
        return $this->run($data);
    }

    protected function complete() {
        $payNo = $this->params['pay_no'];
        $paySign = $this->params['pay_sign'];
        if (empty($payNo) || empty($paySign)) {
            return $this->stop('页面不存在', 404);
        }

        $name = '充值成功';
        $desc = '充值完成,请等待系统处理!';

        return $this->run([
            'status' => 1,
            'name' => $name,
            'desc' => $desc
        ]);
    }

    protected function log() {
        $type = intval($this->params['type']);
        $userId = intval($this->params['user_id']);
        $this->params['limit'] = intval($this->params['limit']);
        $where = [];
        $where['A.user_id'] = $userId;
        $where['A.status'] = 1;
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;
        $model = target('member/PayRecharge');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'recharge_id desc');
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
        $no = intval($this->params['no']);
        $userId = intval($this->params['user_id']);
        $model = target('member/PayRecharge');
        $info = $model->getWhereInfo([
            'B.user_id' => $userId,
            'A.recharge_no' => $no
        ]);
        $info = $model->showInfo($info);
        if (empty($info)) {
            return $this->stop('该记录不存在!', 404);
        }
        return $this->run([
            'info' => $info,
        ]);
    }

}