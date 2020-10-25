<?php

/**
 * 收款管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class ReceiptAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderReceipt';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '收款管理',
                'description' => '管理货到付款收款订单',
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
        ];
    }

    public function _indexOrder() {
        return 'A.receipt_id desc';
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
                $this->error('收款信息获取错误!');
            }
        }
        if ($info['receipt_status']) {
            if (!isPost()) {
                $this->systemDialogError('该订单已收款!');
            } else {
                $this->error('该订单已收款!');
            }
        }

        if (!isPost()) {
            $this->assign('info', $info);
            $this->dialogDisplay();
        } else {
            $money = request('post', 'money', 0, 'price_format');
            $remark = request('post', 'remark', '', 'html_clear');

            $model = target($this->_model);
            $orderPay = target('order/OrderPay')->getWhereInfo([
                'user_id' => $info['order_user_id'],
                'order_ids' => $info['order_id']
            ]);

            if (empty($orderPay)) {
                $orderPayNo = target('order/Order', 'service')->addPay($this->userInfo['user_id'], $info['order_id']);
                if (!$orderPayNo) {
                    $model->rollBack();
                    $this->error(target('order/Order', 'service')->getError());
                }
            } else {
                $orderPayNo = $orderPay['pay_no'];
            }

            $model->beginTransaction();
            if (!target('order/Order', 'service')->payOrder($orderPayNo, $money, '线下付款', $info['order_no'], 0)) {
                $model->rollBack();
                $this->error(target('order/Order', 'service')->getError());
                return false;
            }

            if (!target('order/Order', 'service')->addLog($info['order_id'], 0, '订单已确认收款,操作人员【'.$this->userInfo['username'].'】', $remark)) {
                $model->rollBack();
                $this->error('订单日志记录失败!');
            }

            $data = [];
            $data['receipt_id'] = $id;
            $data['money'] = $money;
            $data['remark'] = $remark;
            $data['receipt_time'] = time();
            $data['status'] = 1;
            if (!target($this->_model)->edit($data)) {
                $this->error('收款状态更改失败!');
            }

            $model->commit();

            $this->success('收款状态更改成功!');
        }
    }


}