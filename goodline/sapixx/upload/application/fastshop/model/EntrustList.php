<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 委托订单
 */
namespace app\fastshop\model;
use think\Model;

class EntrustList extends Model{

    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_entrust_list';


   /**
     * 商品基础库
     * @return void
     */
    public function item(){
        return $this->hasOne('Item','id','item_id');
    }

    /**
     * 所属用户信息
     * @return void
     */
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','user_id');
    }

    /**
     * 状态数字变文字(前台)
     * @return void
     */
    public static function status($param){
        if($param->is_rebate){
            $status_text = '已成交';
        }else{
            if($param->is_under){
                $status_text = '未上架';
            }else{
                $status_text = '已上架';
            }
        }
        return $status_text;
    }
}