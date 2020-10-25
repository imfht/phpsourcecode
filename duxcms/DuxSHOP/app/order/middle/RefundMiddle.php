<?php

/**
 * 退款操作
 */

namespace app\order\middle;

class RefundMiddle extends \app\base\middle\BaseMiddle {


    private $_model = 'order/OrderRefund';

    protected function meta($title = '', $name = '', $url = '') {
        $this->setMeta($title);
        $this->setName($name);
        $crumb = [];
        if ($url) {
            $crumb = [
                'name' => $name,
                'url' => $url
            ];
        }
        $this->setCrumb([
            [
                'name' => '会员中心',
                'url' => url('member/index/index')
            ],
            [
                'name' => '退款管理',
                'url' => url('index')
            ],
            $crumb
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    protected function data() {
        $userId = intval($this->params['user_id']);
        $type = intval($this->params['type']);
        $pageLimit = intval($this->params['limit']);
        $where = [];
        switch ($type) {
            case 1:
                $where['A.status'] = 1;
                break;
            case 2:
                $where['A.status'] = 2;
                break;
            case 3:
                $where['A.status'] = 3;
                break;
            case 4:
                $where['A.status'] = 0;
                break;
        }
        $where['A.user_id'] = $userId;
        $pageLimit = $pageLimit ? $pageLimit : 20;

        $model = target($this->_model);
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'refund_id desc');

        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'pageLimit' => $pageLimit
        ]);
    }

    protected function info() {
        $refundNo = $this->params['refund_no'];
        $userId = intval($this->params['user_id']);
        $info = target($this->_model)->getWhereInfo([
            'A.refund_no' => $refundNo,
            'A.user_id' => $userId
        ]);
        if (empty($info)) {
            return $this->stop('退款单不存在!', 404);
        }
        $orderInfo = target('order/Order')->getInfo($info['order_id']);
        $orderConfig = target('order/OrderConfig')->getConfig();
        $receiveInfo = [
            'name' => $orderConfig['contact_name'],
            'tel' => $orderConfig['contact_tel'],
            'address' => $orderConfig['contact_province'].$orderConfig['contact_city'].$orderConfig['contact_region'].$orderConfig['contact_address'],
        ];
        return $this->run([
            'info' => $info,
            'orderInfo' => $orderInfo,
            'receiveInfo' => $receiveInfo
        ]);
    }


    protected function orderInfo() {
        $id = intval($this->params['id']);
        $userId = intval($this->params['user_id']);

        $info = target('order/OrderGoods')->getWhereInfo([
            'id' => $id
        ]);

        if (empty($info)) {
            $this->stop('该订单商品不存在');
        }
        if ($info['service_status']) {
            $this->stop('该商品已进行退款申请!');
        }

        $target = target('order/Order');
        $orderInfo = $target->getInfo($info['order_id']);

        if ($orderInfo['order_user_id'] <> $userId) {
            return $this->stop('订单不存在！', 404);
        }

        if (!$orderInfo['order_status']) {
            return $this->stop('该订单已关闭!');
        }

        if ($orderInfo['status_data']['action'] == 'close' || $orderInfo['status_data']['action'] == 'pay' || $orderInfo['status_data']['action'] == 'parcel') {
            return $this->stop('暂时无法进行退款操作!');
        }

        if($orderInfo['order_complete_time']) {
            $orderConfig = target('tools/Tools', 'service')->getConfig('order', 'service');
            $maxTime = $orderInfo['order_complete_time'] + (86400 * $orderConfig['day']);
            if(time() > $maxTime) {
                return $this->stop('您的订单已过售后期!');
            }
        }

        $goodsCount = target('order/OrderGoods')->countList([
            'order_id' => $info['order_id'],
            '_sql' => 'service_status <> 0'
        ]);

        $orderRefund = true;
        if ($goodsCount) {
            $orderRefund = false;
        }

        $deliveryPrice = $orderInfo['delivery_price'];
        $refundList = target($this->_model)->loadList([
            'A.order_id' => $orderInfo['order_id']
        ]);
        foreach ($refundList as $vo) {
            $deliveryPrice = price_calculate($deliveryPrice, '-', $vo['delivery_price']);
            if ($vo['type'] == 2) {
                return $this->stop('您已申请整单退款，无法再次申请退款!');
            }
        }

        return $this->run([
            'id' => $id,
            'info' => $info,
            'orderInfo' => $orderInfo,
            'orderPrice' => $orderInfo['pay_price'],
            'deliveryPrice' => $deliveryPrice,
            'causeList' => target($this->_model)->causeList(),
            'orderRefund' => $orderRefund,
        ]);
    }

    protected function push() {
        $type = intval($this->params['type']);
        $cause = str_len(html_clear($this->params['cause']), 250);
        $content = str_len(html_clear($this->params['content']), 200);
        $price = price_format($this->params['price']);
        $deliveryPrice = price_format($this->params['delivery_price']);
        $images = $this->params['images'];
        $userId = $this->params['user_id'];

        $info = $this->data['info'];
        $orderInfo = $this->data['orderInfo'];

        if($type < 0 && $type > 2) {
            return $this->stop('退款类型不正确!');
        }
        if ($type == 2 && !$this->data['orderRefund']) {
            return $this->stop('该订单有退款商品，暂不支持整单退货!');
        }
        if (empty($cause)) {
            return $this->stop('请选择退款原因!');
        }
        if ($type <> 2) {
            if (bccomp($price, 0, 2) === -1) {
                return $this->stop('退款金额错误!');
            }
            if (bccomp($price, $info['price_total'], 2) === 1) {
                return $this->stop('退款金额不能大于付款金额!');
            }

            if (bccomp($deliveryPrice, 0, 2) === -1) {
                return $this->stop('运费退款金额错误!');
            }
            if (bccomp($deliveryPrice, $this->data['deliveryPrice'], 2) === 1) {
                return $this->stop('退款金额不能大于付款金额!');
            }
        } else {
            $price = $this->data['orderPrice'];
            $deliveryPrice = $this->data['deliveryPrice'];
        }
        if (!empty($images) && is_array($images)) {
            $httpHost = DOMAIN_HTTP;
            foreach ($images as $image) {
                if (substr($image, 0, 1) <> '/' && strpos($image, $httpHost, 0) === false) {
                    return $this->stop('您上传的图片有误,请重新上传!');
                }
            }
        } else {
            $images = [];
        }
        $images = $images ? $images : [];
        target($this->_model)->beginTransaction();
        $status = target($this->_model)->add([
            'order_goods_id' => $info['id'],
            'user_id' => $userId,
            'order_id' => $orderInfo['order_id'],
            'type' => $type,
            'price' => $price,
            'delivery_price' => $deliveryPrice,
            'cause' => $cause,
            'content' => $content,
            'images' => serialize($images),
            'status' => 1,
            'create_time' => time(),
            'refund_no' => log_no()
        ]);
        if (!$status) {
            target($this->_model)->rollBack();
            return $this->stop('申请提交失败,请稍后再试!');
        }
        if ($type <> 2) {
            $status = target('order/OrderGoods')->edit([
                'id' => $info['id'],
                'service_status' => 1
            ]);
        } else {
            $status = target('order/OrderGoods')->where([
                'order_id' => $info['order_id']
            ])->data([
                'service_status' => 1
            ])->update();
        }
        if (!$status) {
            target($this->_model)->rollBack();
            return $this->stop('申请提交失败,请稍后再试!');
        }
        if (!target('order/Order', 'service')->addLog($orderInfo['order_id'], 0, '您已发起退款申请,请等待卖家审核！')) {
            target($this->_model)->rollBack();
            return $this->stop('订单日志记录失败!');
        }
        target($this->_model)->commit();
        return $this->run([], '退款申请提交成功,等待卖家审核!');
    }

    protected function delivery() {
        $refundNo = $this->params['refund_no'];
        $userId = intval($this->params['user_id']);
        $deliveryName = html_clear($this->params['delivery_name']);
        $deliveryNo = html_clear($this->params['delivery_no']);
        $info = target('order/OrderRefund')->getWhereInfo([
            'A.refund_no' => $refundNo,
            'A.user_id' => $userId
        ]);
        if (empty($info)) {
            return $this->stop('该退货单不存在!');
        }
        if($info['status'] <> 2) {
            return $this->stop('该退货单无法保存!');
        }
        if($info['delivery_name'] || $info['delivery_no'] ) {
            return $this->stop('您已经提交退货快递信息!');
        }
        if(empty($deliveryName) || empty($deliveryNo)) {
            return $this->stop('快递信息未填写完整!');
        }
        $status = target('order/OrderRefund')->edit([
            'refund_id' => $info['refund_id'],
            'delivery_name' => $deliveryName,
            'delivery_no' => $deliveryNo
        ]);
        if (!$status) {
            target('order/OrderRefund')->rollBack();
            return $this->stop('退货信息保存失败!');
        }
        return $this->run([], '退货信息保存成功!');
    }

    protected function cancel() {
        $refundNo = $this->params['refund_no'];
        $userId = intval($this->params['user_id']);
        $info = target($this->_model)->getWhereInfo([
            'A.refund_no' => $refundNo,
            'A.user_id' => $userId
        ]);
		
        if (empty($info)) {
            return $this->stop('该退款单不存在!');
        }
        if ($info['status'] <> 1) {
            return $this->stop('该退款申请无法取消!');
        }
        target($this->_model)->beginTransaction();
        $status = target($this->_model)->edit([
            'refund_id' => $info['refund_id'],
            'status' => 0,
            'process_time' => time()
        ]);
        if (!$status) {
            target($this->_model)->rollBack();
            return $this->stop('申请取消失败,请稍后再试!');
        }
        $info = target('order/OrderGoods')->getWhereInfo([
            'id' => $info['order_goods_id']
        ]);
        $status = target('order/OrderGoods')->edit([
            'id' => $info['id'],
            'service_status' => 0
        ]);
        if (!$status) {
            target($this->_model)->rollBack();
            return $this->stop('申请取消失败,请稍后再试!');
        }
        if (!target('order/Order', 'service')->addLog($info['order_id'], 0, '您已取消退款申请已取消！')) {
            target($this->_model)->rollBack();
            return $this->stop('订单日志记录失败!');
        }
        target($this->_model)->commit();
        return $this->run([], '退款申请取消成功!');
    }

}