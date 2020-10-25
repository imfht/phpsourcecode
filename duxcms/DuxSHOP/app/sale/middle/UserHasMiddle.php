<?php

/**
 * 我的推荐
 */

namespace app\sale\middle;

class UserHasMiddle extends \app\base\middle\BaseMiddle {


    protected function meta() {
        return parent::meta('我的推荐', '我的推荐');
    }


    protected function data() {
        $userId = intval($this->params['user_id']);
        $type = intval($this->params['type']);
        $type  = $type ? $type : 1;

        $config = target('sale/SaleConfig')->getConfig();

        $max = $type;
        if($max > $config['sale_level']) {
            $max = 1;
        }

        $userList = target('sale/SaleUser')->levelList($userId, $config['sale_level']);
        $saleList = [];
        $saleNames = explode(',', $config['sale_level_name']);

        for ($i = 1; $i <= $config['sale_level']; $i++) {
            $saleList[] = [
                'type' => $i,
                'name' => $saleNames[$i - 1] ? $saleNames[$i - 1] : $i . '级会员',
                'total' => $userList[$i] ? count($userList[$i]) : 0
            ];
        }

        $count = $userList[$max] ? count($userList[$max]) : 0;
        $pageData = $this->pageData($count, 10);

        if($userList[$max]) {
            $list = array_slice($userList[$max], $pageData['limit'][0], $pageData['limit'][1]);
        }else {
            $list = [];
        }
        return $this->run([
            'pageData' => $pageData,
            'countList' => $count,
            'pageList' => $list,
            'saleList' => $saleList,
            'type' => $type
        ]);
    }


}