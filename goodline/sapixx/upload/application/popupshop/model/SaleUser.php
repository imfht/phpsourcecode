<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 当前销售产品ID
 */
namespace app\popupshop\model;
use think\Model;

class SaleUser extends Model
{
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_sales_user';
    
    /**
     * 默认主产品
     * @return void
     */
    public function house(){
        return $this->hasOne('SaleHouse','id','house_id');
    }
    
    /**
     * 所属用户
     * @return void
     */
    public function user(){
        return $this->hasOne('app\common\model\SystemUser','id','user_id');
    }

    /**
     * 所属用户
     * @return void
     */
    public function sale(){
        return $this->hasOne('Sale','sales_user_id','id');
    }

    /**
     * 状态数字变文字(前台)
     * @return void
     */
    public static function status($param){
        if($param->is_out){
            $status_text = '已提货';
        }else{
            if($param->is_rebate){
                $status_text = '已成交';
            }else{
                if($param->is_sale){
                    $status_text = '已上架';
                }else{
                    $status_text = '未上架';
                }
            }
 
        }
        return $status_text;
    }
}