<?php

/**
 * 评论详情
 */

namespace app\sale\middle;

class OrderMiddle extends \app\base\middle\BaseMiddle {


    protected function meta() {
        return parent::meta('推广订单', '推广订单');
    }

    protected function data() {
        $userId = intval($this->params['user_id']);
        $type = intval($this->params['type']);
        $where = [];
        $pageLimit = 20;
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
        $where['A.user_id'] = $userId;

        $model = target('sale/SaleOrder');
        $count = $model->countList($where);
        $pageData = $this->pageData($count, $pageLimit);
        $list = $model->loadList($where, $pageData['limit'], 'create_time desc, id desc');
        return $this->run([
            'type' => $type,
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
        ]);
    }


}