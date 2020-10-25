<?php

/**
 * 积分记录
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\member\admin;

class PointsLogAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'PointsLog';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '积分记录',
                'description' => '积分使用获得记录',
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
            'log_no' => 'A.log_no'
        ];
    }

    public function _indexOrder() {
        return 'log_id desc';
    }

    public function _indexWhere($whereMaps) {
        if($whereMaps['A.type'] > 1) {
            unset($whereMaps['A.type']);
        }
        return $whereMaps;
    }

    public function info() {
        $id = request('get', 'id');
        if(empty($id)) {
            $this->error('参数传递错误!');
        }
        $info = target($this->_model)->getInfo($id);
        if(empty($info)) {
            $this->error('暂无该记录!');
        }
        $this->assign('info', $info);
        $this->systemDisplay();
    }

}