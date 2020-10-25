<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 当前销售产品ID
 */
namespace app\fastshop\model;
use think\Model;
use filter\filter;

class Sale extends Model
{
    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_sales';
    protected $autoWriteTimestamp = false;
    protected $createTime = false;

    //联盟商品
    public function item(){
        return $this->hasOne('Item','id','item_id');
    }

    /**
     * 查询单个商品（包裹委托价格）
     * @param string $status
     * @param string $keyword
     * @return void
     */
    public function getfind(int $miniapp_id,int $id){
        return self::view('fastshop_sales','id,types,is_vip,is_fusion,end_time,gift,item_id,market_price,member_miniapp_id,sale_nums,sale_price,start_time,title','fastshop_item.id = fastshop_sales.item_id')
        ->view('fastshop_item','id as item_id,name,weight,img,imgs,content')
        ->where(['fastshop_sales.member_miniapp_id' => $miniapp_id,'fastshop_sales.id' => $id])
        ->where('fastshop_item.is_sale','<>','1') //下架或商家商品 1是删除
        ->where('fastshop_sales.types', '=', 1)  //允许抢购的
        ->find();
    }

    
    //添加或编辑
    public function edit(int $miniapp_id,array $param){
        $data['category_id']       = $param['category_id'];
        $data['types']             = $param['types'];
        $data['title']             = trim($param['title']);
        $data['sale_nums']         = (int)$param['sale_nums'];
        $data['item_id']           = (int)$param['item_id'];
        $data['cost_price']        = $param['cost_price']*100;
        $data['market_price']      = $param['market_price']*100;
        $data['sale_price']        = $param['sale_price']*100;
        $data['start_time']        = strtotime($param['start_time']);
        $data['end_time']          = strtotime($param['end_time']);
        $data['is_vip']            = $param['is_vip'];
        $data['is_newuser']        = $param['is_newuser'];
        $data['is_fusion']         = $param['is_fusion'];
        $data['update_time']       = time();
        $data['member_miniapp_id'] = $miniapp_id;
        $data['gift']              = json_encode($param['gift']);
        if(isset($param['id'])){
            $id = (int)$param['id'];
            self::where('id',$id)->update($data);
            return $id;
        }else{
            return self::insertGetId($data);
        }
    } 
}