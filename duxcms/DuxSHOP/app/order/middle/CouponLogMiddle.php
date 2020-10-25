<?php

/**
 * 卡券记录
 */

namespace app\order\middle;


class CouponLogMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'order/OrderCouponLog';

    protected function meta() {
        $this->setMeta('我的优惠券');
        $this->setName('我的优惠券');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '我的优惠券',
                'url' => URL
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $type = intval($this->params['type']);
        $userId = intval($this->params['user_id']);
        $where['A.status'] = 0;
        $where['A.del'] = 0;
        if ($type == 1) {
            $where['_sql'] = 'A.end_time >= ' . time();
        }
        if ($type == 2) {
            $where['_sql'] = 'A.end_time < ' . time();
        }
        $where['A.user_id'] = $userId;
        $pageLimit = $this->params['limit'] ? $this->params['limit'] : 20;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'log_id desc');

        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit
        ]);
    }

    protected function del() {
        $userId = intval($this->params['user_id']);
        $logId = intval($this->params['log_id']);
        $info = target($this->_model)->getInfo($logId);
        if (empty($info)) {
            return $this->stop('该优惠券不存在!!');
        }
        if($info['user_id'] <> $userId) {
            return $this->stop('无权删除该优惠券!');
        }
        target($this->_model)->edit([
            'log_id' => $logId,
            'del' => 1
        ]);
        return $this->run([], '删除该优惠券成功!');
    }

}