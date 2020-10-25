<?php

/**
 * 推广商申请
 */

namespace app\sale\middle;

class ApplyMiddle extends \app\base\middle\BaseMiddle {


    private $_model = 'order/OrderAddress';

    protected function meta() {
        $this->setMeta('推广商申请');
        $this->setName('推广商申请');
        $this->setCrumb([
            [
                'name' => '推广商申请',
                'url' => url()
            ],
        ]);

        return $this->run([
            'pageInfo' => $this->pageInfo
        ]);
    }

    private function getConfig() {
        return target('sale/saleConfig')->getConfig();
    }

    protected function data() {
        $userId = intval($this->params['user_id']);
        $config = $this->getConfig();
        
        $saleInfo = target('sale/SaleUser')->getWhereInfo([
            'A.user_id' => $userId,
            'agent' => 1
        ]);

        $applyInfo = [];
        if (empty($saleInfo)) {
            $applyInfo = target('sale/SaleUserApply')->getWhereInfo([
                'A.user_id' => $userId
            ]);
        }

        $html = '';
        if (empty($saleInfo) && empty($applyInfo)) {
            $applyWhere = unserialize($config['apply_where']);
            if ($config['apply_type'] == 2) {
                $where = [];
                $where['order_user_id'] = $userId;
                $where['order_status'] = 1;
                if ($applyWhere['type']) {
                    $where['pay_status'] = 1;
                } else {
                    $where['order_complete_status'] = 1;
                }
                $count = target('order/Order')->countList($where);

                $html = '本店累计消费次数达到' . $applyWhere['data'] . '次，才可成为本店推广商，您已消费' . $count . '次，';
                if ($count < $applyWhere['data']) {
                    $html .= '请继续努力！';
                } else {
                    $html .= '可以立即申请！';
                }
            }
            if ($config['apply_type'] == 3) {
                $where = [];
                $where['order_user_id'] = $userId;
                $where['order_status'] = 1;
                if ($applyWhere['type']) {
                    $where['pay_status'] = 1;
                } else {
                    $where['order_complete_status'] = 1;
                }
                $list = target('order/Order')->loadList($where);

                $count = 0;
                foreach ($list as $vo) {
                    $count += $vo['order_price'] + $vo['delivery_price'];
                }

                $html = '本店消费金额满' . $applyWhere['data'] . '元，才可成为本店推广商，您已消费' . $count . '元，';
                if ($count < $applyWhere['data']) {
                    $html .= '请继续努力！';
                } else {
                    $html .= '可以立即申请！';
                }
            }
            if ($config['apply_type'] == 4) {
                $shopInfo = target('shop/Shop')->getWhereInfo([
                    'goods_no' => $applyWhere['data']
                ]);
                $where = [];
                $where['B.order_user_id'] = $userId;
                $where['A.goods_id'] = $shopInfo['shop_id'];
                $orderGoods = target('order/OrderGoods')->loadHasList($where);
                $title = "<a href='" . url($shopInfo['app'] . '/info/index', ['id' => $shopInfo['shop_id']]) . "' target='_blank'>【 " . $shopInfo['title'] . "】</a>";
                $html = '本店购买指定商品 ' . $title . '，才可成为本店推广商，';
                if (empty($orderGoods)) {
                    $html .= '请购买该商品！';
                } else {
                    $html .= '您已经购买可以立即申请！';
                }
            }
        } else {
            if (!empty($saleInfo)) {
                $html = '恭喜您，您已经成功加入推广商，加入时间为【' . date('Y-m-d H:i', $saleInfo['agent_time']) . '】，您可以进行正常推广。';
            }else {
                switch ($applyInfo['status']) {
                    case 1:
                        $html = '您的推广商申请已提交成功，请耐心等待管理员的审核！';
                        break;
                    case 0:
                        $html = '很抱歉，您的推广商申请已被拒绝，拒绝原因【'.$applyInfo['remark'] ? $applyInfo['remark'] : '无'.'】';
                        break;
                }
            }
        }
        return $this->run([
            'html' => $html,
            'saleInfo' => $saleInfo,
            'applyInfo' => $applyInfo
        ]);
    }

    public function apply() {
        $userId = intval($this->params['user_id']);
        $config = $this->getConfig();
        if (!target('sale/Sale', 'service')->addAgent($userId, 0, $config['apply_check'])) {
            return $this->stop(target('sale/Sale', 'service')->getError());
        }
        if ($config['apply_check']) {
            return $this->run([], '提交申请成功，请等待审核!');
        }
        return $this->run([], '恭喜您成为本店推广商!');
    }

}