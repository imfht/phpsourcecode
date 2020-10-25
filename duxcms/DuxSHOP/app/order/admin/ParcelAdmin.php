<?php

/**
 * 发货单管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class ParcelAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderParcel';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '配货管理',
                'description' => '管理订单商品配货',
            ],
            'fun' => [
                'index' => true,
                'del' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'B.order_no',
            'type' => 'A.status',
        ];
    }

    public function _indexOrder() {
        return 'A.parcel_id desc';
    }

    public function _indexWhere($whereMaps) {
        if ($whereMaps['A.status'] > 3) {
            unset($whereMaps['A.status']);
        }
        return $whereMaps;
    }

    public function status() {
        $id = request('', 'id', 0);
        if (empty($id)) {
            if (!isPost()) {
                $this->systemDialogError('参数获取错误!');
            } else {
                $this->error('参数获取错误!');
            }
        }
        $info = target($this->_model)->getInfo($id);
        if (empty($info)) {
            if (!isPost()) {
                $this->systemDialogError('配货信息获取错误!');
            } else {
                $this->error('配货信息获取错误!');
            }
        }
        if ($info['parcel_status'] <> 1 && $info['parcel_status'] <> 2) {
            if (!isPost()) {
                $this->systemDialogError('该配送状态无法更改!');
            } else {
                $this->error('该配送状态无法更改!');
            }
        }

        if (!isPost()) {
            $this->assign('info', $info);
            $this->dialogDisplay();
        } else {
            $status = request('post', 'status', 0, 'intval');
            $status = ($status < 4) ? $status : 0;
            $remark = request('post', 'remark', '', 'html_clear');
            if ($status == $info['parcel_status']) {
                $this->error('发货状态未改变!');
            }
            switch($status) {
                case 1:
                    $text = '生成发货单,待工作人员配货';
                    break;
                case 2:
                    $text = '工作人员正在进行配货中';
                    break;
                case 3:
                    $text = '配货打包成功,等待送货';
                    break;
                case 0:
                    $text = '配货进行失败';
                    break;
                default:
                    $text = '配货准备中';
                    break;
            }
            target($this->_model)->beginTransaction();
            $data = [];
            $data['parcel_id'] = $id;
            $data['status'] = $status;
            $data['log'] = target($this->_model)->addLog($info['parcel_log'], $text, $remark, time());
            if (!target($this->_model)->edit($data)) {
                target($this->_model)->rollBack();
                $this->error('状态更改失败!');
            }
            if(!$info['delivery_status']) {
                if (!target('order/Order', 'service')->addLog($info['order_id'], 1, $text)) {
                    target($this->_model)->rollBack();
                    $this->error('订单日志记录失败!');
                }
            }
            if (!target('order/Order', 'service')->addLog($info['order_id'], 0, $text.',操作人员【'.$this->userInfo['username'].'】')) {
                target($this->_model)->rollBack();
                $this->error('订单日志记录失败!');
            }
            target($this->_model)->commit();

            $this->success('状态更改成功!');
        }
    }

    public function printInfo() {
        $id = request('', 'id', 0);
        if (empty($id)) {

            $this->error('参数获取错误!');
        }
        $info = target($this->_model)->getInfo($id);
        if (empty($info)) {
            $this->error('配货信息获取错误!');
        }
        $info['order_items'] = target('order/OrderGoods')->loadList([
            'order_id' => $info['order_id'],
            'service_status' => 0
        ]);
        $this->assign('info', $info);
        $this->systemDisplay();
    }


}