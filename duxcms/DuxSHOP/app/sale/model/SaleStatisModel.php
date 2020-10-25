<?php

/**
 * 推广用户统计
 */
namespace app\sale\model;

use app\system\model\SystemModel;

class SaleStatisModel extends SystemModel {

    protected $infoModel = [
        'pri' => 'id',
        'into' => '',
        'out' => '',
    ];

    public function checkUser($userId) {
        $info = $this->getWhereInfo(['user_id' => $userId]);
        if(!empty($info)) {
            return true;
        }
        $this->add([
            'user_id' => $userId
        ]);
        return true;
    }

    public function updateUser($userId) {
        if(empty($userId)) {
            return true;
        }
        $saleConfig = target('sale/SaleConfig')->getConfig();
        $parentUsers = target('sale/SaleUser')->loadParentList($userId, 0, $saleConfig['sale_purchase'] ? $saleConfig['sale_level'] - 1 : $saleConfig['sale_level']);

        foreach ($parentUsers as $key => $vo) {
            $this->checkUser($vo['user_id']);
            if(!$key) {
                target('sale/SaleStatis')->where(['user_id' => $vo['user_id']])->setInc('has_user_num', 1);
            }
            target('sale/SaleStatis')->where(['user_id' => $vo['user_id']])->setInc('sale_user_num', 1);
        }
        return true;
    }

    public function updateOrder($userId, $money) {
        $this->checkUser($userId);
        $saleConfig = target('sale/SaleConfig')->getConfig();
        target('sale/SaleStatis')->where(['user_id' => $userId])->setInc('order_money', $money);
        target('sale/SaleStatis')->where(['user_id' => $userId])->setInc('order_num', 1);
        $parentUsers = target('sale/SaleUser')->loadParentList($userId,  1, $saleConfig['sale_purchase'] ? $saleConfig['sale_level'] - 1 : $saleConfig['sale_level']);
        foreach ($parentUsers as $key => $vo) {
            if(!$key) {
                target('sale/SaleStatis')->where(['user_id' => $vo['user_id']])->setInc('has_order_money', $money);
                target('sale/SaleStatis')->where(['user_id' => $vo['user_id']])->setInc('has_order_num', 1);
            }
            target('sale/SaleStatis')->where(['user_id' => $vo['user_id']])->setInc('sale_order_money', $money);
            target('sale/SaleStatis')->where(['user_id' => $vo['user_id']])->setInc('sale_order_num', 1);
        }
        return true;
    }


}