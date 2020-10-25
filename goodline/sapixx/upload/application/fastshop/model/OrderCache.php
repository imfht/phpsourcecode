<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 订单数据
 */
namespace app\fastshop\model;
use think\Model;

class OrderCache extends Model{
    
    protected $pk    = 'id';
    protected $table = 'ai_fastshop_order_cache';
    
    //订单列表
    public function Order(){
        return $this->hasOne('Order','order_no','order_no');
    }
}