<?php

/**
 * 退款管理
 * @author  Mr.L <349865361@qq.com>
 */

namespace app\order\admin;

class RefundAdmin extends \app\system\admin\SystemExtendAdmin {

    protected $_model = 'OrderRefund';

    /**
     * 模块信息
     */
    protected function _infoModule() {
        return [
            'info' => [
                'name' => '退款管理',
                'description' => '处理订单产品退货',
            ],
            'fun' => [
                'index' => true,
                'status' => true,
            ]
        ];
    }

    public function _indexParam() {
        return [
            'keyword' => 'A.refund_no',
            'type' => 'A.status',
        ];
    }

    public function _indexOrder() {
        return 'A.refund_id desc';
    }

    public function info() {
        $id = request('', 'id', 0);
        if (empty($id)) {
            $this->error('参数获取错误!');
        }
        $info = target($this->_model)->getInfo($id);
        $orderInfo = target('order/Order')->getWhereInfo([
            'order_id' => $info['order_id']
        ]);
        if (!isPost()) {
            $orderGoods = target('order/OrderGoods')->loadList([
                'order_id' => $info['order_id']
            ]);
            $this->assign('info', $info);
            $this->assign('orderInfo', $orderInfo);
            $this->assign('orderGoods', $orderGoods);
            $this->systemDisplay();
        } else {
            if (!$info['status']) {
                $this->error('该退款单无法处理!');
            }
            $status = request('', 'status', 0, 'intval');
            $remark = request('', 'remark', '', 'html_clear');
            if (!$info['status']) {
                $this->error('该退款已处理，无法重复操作!');
            }
            target($this->_model)->beginTransaction();
            if(!$info['type']) {
                //退款
                if($info['status'] == 1) {
                    $status = $status ? 3 : 0;
                }else {
                    $this->error('该退款已处理，无法重复操作!');
                }

            }else {
                //退货
                if($info['status'] == 1) {
                    $status = $status ? 2 : 0;
                }elseif($info['status'] == 2) {
                    $status = $status ? 3 : 0;
                }else {
                    $this->error('该退款已处理，无法重复操作!');
                }
            }

            $save = target($this->_model)->edit([
                'refund_id' => $id,
                'status' => $status,
                'process_remark' => $remark,
                'process_time' => time()
            ]);
            if (!$save) {
                target($this->_model)->rollBack();
                $this->error('退款处理失败!');
            }

            if ($status == 2) {
                $userText = '卖家已同意退货申请，请将商品进行退货!';
                $adminText = '退货申请审核通过';
            }

            if ($status == 3) {
                $userText = '卖家已同意退款申请，款项将会退回账户余额!';
                $adminText = '退款申请审核通过';

                if($info['type'] == 2) {
                    if (!target('order/Order', 'service')->refundOrder($info['order_id'], 1)) {
                        target($this->_model)->rollBack();
                        $this->error(target('order/Order', 'service')->getError());
                    }
                }else {
                    if (!target('order/Order', 'service')->refundOrder($info['order_goods_id'], 0, $info['price'])) {
                        target($this->_model)->rollBack();
                        $this->error(target('order/Order', 'service')->getError());
                    }
                }
            }

            if(!$status) {
                $userText = '卖家已拒绝您的退款申请!';
                $adminText = '退款申请已拒绝';
                $save = target('order/OrderGoods')->edit([
                    'id' => $info['order_goods_id'],
                    'service_status' => 0
                ]);
                if (!$save) {
                    target($this->_model)->rollBack();
                    $this->error('退款处理失败!');
                }
            }
            if (!target('order/Order', 'service')->addLog($orderInfo['order_id'], 0, $adminText . ',操作人员【' . $this->userInfo['username'] . '】')) {
                target($this->_model)->rollBack();
                $this->error('订单日志记录失败!');
            }
            if (!target('order/Order', 'service')->addLog($orderInfo['order_id'], 1, $userText)) {
                $this->error('订单日志记录失败!');
            }
            target($this->_model)->commit();
            $this->success('退款处理成功!', url('index'));
        }
    }


}