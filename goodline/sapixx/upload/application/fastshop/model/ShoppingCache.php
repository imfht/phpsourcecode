<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单数据
 */
namespace app\fastshop\model;
use think\Model;

class ShoppingCache extends Model{
    
    protected $pk    = 'id';
    protected $table = 'ai_fastshop_shopping_cache';
    
    //订单列表
    public function shopping(){
        return $this->hasOne('Shopping','order_no','order_no');
    }
}