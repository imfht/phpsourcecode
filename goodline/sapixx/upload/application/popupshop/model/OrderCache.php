<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单数据
 */
namespace app\popupshop\model;
use think\Model;

class OrderCache extends Model{
    
    protected $table = 'ai_popupshop_order_cache';

    //产品嘻嘻你
    public function Item(){
        return $this->hasOne('Item','id','item_id');
    }

}