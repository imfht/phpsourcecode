<?php

/**
 * 积分记录
 */

namespace app\member\middle;


class PointsMiddle extends \app\base\middle\BaseMiddle {

    private $_model = 'member/PointsLog';

    protected function meta() {
        $this->setMeta('积分记录');
        $this->setName('积分记录');
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')

            ],
            [
                'name' => '积分记录',
                'url' => url()
            ]
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function account() {
        $userId = intval($this->params['user_id']);
        $info = target('member/PointsAccount')->getWhereInfo([
            'A.user_id' => $userId
        ]);
        if (empty($info)) {
            target('member/PointsAccount')->add([
                'user_id' => $userId
            ]);
        }
        $info['overdue'] = target('member/PointsCharge')->where([
            'user_id' => $userId,
            'status' => 1,
            '_sql' => 'start_time >= ' . time() . ' AND stop_time <= ' . (time() + 259200)
        ])->sum('money');

        return $this->run([
            'accountInfo' => $info
        ]);

    }

    protected function data() {
        $type = intval($this->params['type']);
        $userId = intval($this->params['user_id']);
        if ($type == 1) {
            $where['A.type'] = 1;
        }
        if ($type == 2) {
            $where['A.type'] = 0;
        }
        $where['A.user_id'] = $userId;
        $pageLimit = 20;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'log_id desc');

        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
        ]);
    }

    protected function info() {
        $no = $this->params['no'];
        $userId = intval($this->params['user_id']);
        $info = target($this->_model)->getWhereInfo([
            'A.user_id' => $userId,
            'A.log_no' => $no
        ]);
        if (empty($info)) {
            return $this->stop('该记录不存在!', 404);
        }
        return $this->run([
            'info' => $info,
        ]);
    }

    protected function charge() {
        $userId = intval($this->params['user_id']);
        $where['user_id'] = $userId;
        $pageLimit = 20;

        $model = target('member/PointsCharge');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'id desc');

        return $this->run([
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
        ]);
    }

}