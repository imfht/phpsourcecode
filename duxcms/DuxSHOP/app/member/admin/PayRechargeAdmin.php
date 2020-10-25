<?php

/**
 * 支付记录
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PayRechargeAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'PayRecharge';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '充值记录',
                'description' => '资金充值记录信息',
            ],
            'fun' => [
                'index' => true,
                'status' => true
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'B.tel',
            'type' => 'A.type',
            'recharge_no' => 'A.recharge_no',
            'start_time' => 'start_time',
            'stop_time' => 'stop_time'
        ];
    }

    public function _indexOrder() {
        return 'A.recharge_id desc';
    }

    public function _indexWhere($whereMaps) {
        if($whereMaps['A.type'] > 1) {
            unset($whereMaps['A.type']);
        }
        $startTime = 0;
        if ($whereMaps['start_time']) {
            $startTime = strtotime($whereMaps['start_time']);
        }
        $stopTime = 0;
        if ($whereMaps['stop_time']) {
            $stopTime = strtotime($whereMaps['stop_time'] . ' 23:59:59');
        }

        if ($startTime) {
            $whereMaps['_sql'][] = 'A.time >= ' . $startTime;
        }
        if ($stopTime) {
            $whereMaps['_sql'][] = 'A.time <= ' . $stopTime;
        }

        unset($whereMaps['start_time']);
        unset($whereMaps['stop_time']);
        $whereMaps['A.status'] = 1;
        return $whereMaps;
    }

    public function info() {
        $id = request('get', 'id');
        if(empty($id)) {
            $this->error('参数传递错误!');
        }
        $info = target('member/PayRecharge')->getInfo($id);
        if(empty($info)) {
            $this->error('暂无该记录!');
        }
        $this->assign('info', $info);
        $this->systemDisplay();
    }

}