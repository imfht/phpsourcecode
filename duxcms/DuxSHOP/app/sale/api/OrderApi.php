<?php

/**
 * 订单列表
 */
namespace app\sale\api;

class OrderApi extends \app\member\api\MemberApi {

    public function index() {

        $pageLimit = $this->data['limit'] ? $this->data['limit'] : 10;

        $where = [];
        $where['A.user_id'] = $this->userId;

        $type = intval($this->data['type']);
        switch ($type) {
            case 1:
                $where['A.sale_status'] = 1;
                break;
            case 2:
                $where['A.sale_status'] = 2;
                break;
            case 3:
                $where['A.sale_status'] = 0;
                break;
        }

        $model = target('sale/SaleOrder');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'create_time desc, id desc');

        if ($list) {
            $this->success('ok', [
                'data' => $list,
                'pageData' => [
                    'limit' => $pageLimit,
                    'limit' => count($list),
                    'page' => $pageData['page'],
                    'totalPage' => $pageData['totalPage']
                ]
            ]);
        } else {
            $this->error('暂无更多记录', 404);
        }
    }


}