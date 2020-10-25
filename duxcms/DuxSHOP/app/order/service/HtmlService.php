<?php
namespace app\order\service;
/**
 * Html接口
 */
class HtmlService extends \app\base\service\BaseService {

    private $userInfo = [];


    public function getMemberIndexBodyHtml($userInfo) {
        $this->userInfo = $userInfo;
        $orderList = target('order/Order')->loadList([
            'order_user_id' => $userInfo['user_id']
        ], 5);

        $orderCount = [
            'pay' => $this->countOrder(1),
            'delivery' => $this->countOrder(3),
            'complete' => $this->countOrder(4),
        ];

        $html = \dux\Dux::view()->fetch('app/order/view/service/member/indexbody', [
            'orderList' => $orderList,
            'orderCount' => $orderCount
        ]);

        return [
            [
                'name' => '商城模块',
                'order' => 1,
                'html' => $html
            ]
        ];
    }

    public function getMemberIndexMobileHtml($userInfo) {
        $this->userInfo = $userInfo;
        $orderCount = [
            'pay' => $this->countOrder(1),
            'delivery' => $this->countOrder(3),
            'complete' => $this->countOrder(4),
        ];
        $html = \dux\Dux::view()->fetch('app/order/view/service/member/indexmobile', [
            'orderCount' => $orderCount
        ]);
        return [
            [
                'name' => '商城模块',
                'order' => 5,
                'html' => $html
            ]
        ];
    }


    public function countOrder($type) {

        $where = [];
        $where['order_status'] = 1;
        $where['order_user_id'] = $this->userInfo['user_id'];


        switch ($type) {
            case 1:
                $where['pay_type'] = 1;
                $where['pay_status'] = 0;
                $where['delivery_status'] = 0;
                break;
            case 2:
                $where['_sql'][] = '(pay_type = 0 OR pay_status = 1)';
                $where['delivery_status'] = 0;
                break;
            case 3:
                $where['delivery_status'] = 1;
                $where['order_complete_status'] = 0;
                break;
            case 4:
                $where['order_complete_status'] = 1;
                $where['comment_status'] = 0;
                break;
        }

        $model = target('order/Order');

        return $model->countList($where);

    }


}

