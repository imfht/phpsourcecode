<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单数据
 */
namespace app\popupshop\model;
use think\Model;

class SaleOrderCache extends Model{
    
    protected $table = 'ai_popupshop_sales_order_cache';

    //产品信息
    public function house(){
        return $this->hasOne('SaleHouse','id','house_id');
    }

}