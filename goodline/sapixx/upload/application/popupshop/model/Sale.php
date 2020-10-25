<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 当前销售产品ID
 */
namespace app\popupshop\model;
use think\Model;
use app\popupshop\model\SaleHouse;

class Sale extends Model
{
    protected $pk     = 'id';
    protected $table  = 'ai_popupshop_sales';
    
    /**
     * 默认主产品
     * @return void
     */
    public function House(){
        return $this->hasOne('SaleHouse','id','house_id');
    }

    /**
     * 寄卖的产品列表
     * @return void
     */
    public function salesUser(){
        return $this->hasOne('SaleUser','id','sales_user_id');
    }

    /**
     * 所属用户
     * @return void
     */
    public function User(){
        return $this->hasOne('app\common\model\SystemUser','id','user_id');
    }

   /**
     * 所属好店
     * @return void
     */
    public function Store(){
        return $this->hasOne('Store','id','store_id');
    }
    

    /**
     * 所有产品列表
     * @param [int] $member_miniapp_id
     * @return void
     */
    public static function lists($member_miniapp_id,int $types = 0){
        $condition[] =  ['member_miniapp_id','=',$member_miniapp_id];
        switch ($types) {
            case 1:
                $condition[] =  ['user_id','=',0];
                break;
            case 2:
                $condition[] =  ['user_id','>',0];
                break;
             case 3:
                $condition[] =  ['is_pay','=',0];
                break;
            case 4:
                $condition[] =  ['is_pay','=',1];
                break;
        }
        $info = self::where($condition)->order('id desc')->paginate(5,false,['query' => ['types' => $types]]);
        foreach ($info as $key => $value) {
            $house_ids = array_column(json_decode($value->gift),'house_id');
            $gift = [];
            foreach ($house_ids as $i => $id) {
                $gift[$i] = SaleHouse::where(['id' => $id])->find()->toArray();
            }
            $info[$key]['gift'] = $gift;
        }
        return $info;
    }

    //添加或编辑
    public static function edit(array $param){
        $data['is_sale']           = $param['is_sale'];
        $data['house_id']          = $param['house_id'];
        $data['store_id']          = $param['store_id'];
        $data['user_id']           = $param['user_id'];
        $data['user_cost_price']   = $param['cost_price'];
        $data['user_entrust_price']= $param['entrust_price'];
        $data['user_sale_price']   = $param['sale_price'];
        $data['member_miniapp_id'] = $param['member_miniapp_id'];
        $data['gift']              = json_encode($param['gift']);
        $data['update_time']       = time();
        if(isset($param['sales_user_id'])){
            $data['sales_user_id'] = $param['sales_user_id'];
        }
        if(isset($param['id'])){
            return self::where(['id' => $param['id']])->update($data);
        }else{
            $data['create_time']    = time();
            return self::insert($data);
        }
    } 
}