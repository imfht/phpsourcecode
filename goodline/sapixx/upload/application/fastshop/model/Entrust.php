<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 委托购买
 */
namespace app\fastshop\model;
use think\Model;

class Entrust extends Model{

    protected $pk     = 'id';
    protected $table  = 'ai_fastshop_entrust';
    protected $table_entrust = 'ai_fastshop_entrust_list';  //寄卖商品表
    protected $autoWriteTimestamp = true;
    protected $createTime = false;

    
    /**
     * 商品基础库
     * @return void
     */
    public function item(){
        return $this->hasOne('Item','id','item_id');
    }
    
    /**
     * 成交管理
     */
    public function entrustList(array $condition){
        return self::view('fastshop_entrust','*')
        ->view('fastshop_item','img,name','fastshop_entrust.item_id = fastshop_item.id')->where($condition)->order('fastshop_entrust.gite_count desc')
        ->paginate(10);
    }

    /**
     * 小程序管理中心寄卖管理
     */
    public function giftManagelist(array $condition,int $types = 0,$keyword = ''){
        switch ($types) {
            case 1:
                $condition['fastshop_entrust_list.is_rebate'] = 0;
                break;
            case 2:
                $condition['fastshop_entrust_list.is_rebate'] = 1;
            case 3:
                $condition['fastshop_entrust_list.is_rebate'] = 1; 
                $condition['fastshop_entrust_list.is_diy']    = 1; 
                break;
        }
        return self::view('fastshop_entrust_list','id,item_id,entrust_price,rebate,is_rebate,user_id,create_time,is_under,update_time')
        ->view('system_user','nickname,face','fastshop_entrust_list.user_id = system_user.id','left')
        ->view('fastshop_item','img,name','fastshop_entrust_list.item_id = fastshop_item.id')->where($condition)->order('fastshop_entrust_list.id desc')
        ->paginate(10,false,['query' =>['types' => $types,'keyword' => $keyword]]);
    }

    /**
     * 用户寄卖列表(API) (V2.0待删除)
     */
    public function giftlist(int $uid,int $types = 0){
        $condition['fastshop_entrust_list.user_id'] =  $uid;
        switch ($types) {
            case 1:
                $condition['fastshop_entrust_list.is_rebate'] = 0;
                break;
            case 2:
                $condition['fastshop_entrust_list.is_rebate'] = 1;
                break;
        }
        $info = self::view('fastshop_entrust_list','id,item_id,entrust_price,rebate,is_rebate,is_under')
        ->view('fastshop_item','img,name','fastshop_entrust_list.item_id = fastshop_item.id')->where($condition)->order('fastshop_entrust_list.id desc')
        ->paginate(10,true)->toArray();
        $data = [];
        foreach ($info['data'] as $key => $value) {
            $data[$key] = $value;
            $data[$key]['is_under']      = empty($value['is_under']) ? 0 : 1;
            $data[$key]['entrust_price'] = money($value['entrust_price']/100);
            $data[$key]['rebate']        = money($value['rebate']/100);
            $data[$key]['service_price'] = money($value['rebate']/100);
            $data[$key]['img']           = $value['img'];
        }
        return $data;
    }
}