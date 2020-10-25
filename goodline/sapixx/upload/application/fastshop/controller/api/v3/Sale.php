<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 小程序公共API服务
 */
namespace app\fastshop\controller\api\v3;
use app\fastshop\controller\api\Base;
use app\fastshop\model\Sale as AppSale;
use app\fastshop\model\Item;
use think\facade\Request;

class Sale extends Base{

    /**
     * 获得首页
     */
    public function index(){
        $param['signkey'] = Request::param('signkey');
        $param['sign']    = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
        $condition[] = ['types','=',1];
        $condition[] = ['sale_nums','>=',1];
        $info = AppSale::with(['Item'=> function($query) {
            $query->field('id,name,sell_price,img');
        }])
        ->where($condition)->field('id,types,is_vip,end_time,gift,img,item_id,market_price,member_miniapp_id,sale_nums,sale_price,start_time,title')->order('id desc')->limit(5)->select();
        if($info->isEmpty()){
            return enjson(204,'空内容');
        }
        $data = [];
        foreach ($info as $key => $value) {
            $data[$key] = $value;
            $data[$key]['sale_price'] = money($value->sale_price/100);
            $data[$key]['user']  = empty($value->user) ? []  : $value->user;
            $data[$key]['item'] = $value->item;
            $item_ids = array_column(json_decode($value->gift),'item_id');
            $gift = [];
            foreach ($item_ids as $i => $id) {
                $item = Item::where(['id' => $id])->field('id,name,sell_price,img')->find()->toArray();
                $gift[$i] = $item;
                $gift[$i]['sell_price'] = money($item['sell_price']);

            }
            $data[$key]['gift'] = $gift;
        }
        return enjson(200,'成功',$data);
    }

    /**
     * 获得首页
     */
    public function lists(){
        $param['time_id'] = Request::param('time_id/d',0);
        $param['page']    = Request::param('page/d');
        $param['sign']    = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition[] = ['member_miniapp_id','=',$this->miniapp_id];
        $condition[] = ['types','=',1];
        $condition[] = ['sale_nums','>=',1];
        if($param['time_id']){
            $condition[] = ['category_id','=',$param['time_id']];
        }
        $info = AppSale::with(['Item'=> function($query) {
            $query->field('id,name,sell_price,img');
        }])
        ->where($condition)->field('id,types,is_vip,is_newuser,end_time,gift,item_id,market_price,member_miniapp_id,sale_nums,sale_price,start_time,end_time,title')->order('sort desc,id desc')->paginate(10);
        if($info->isEmpty()){
            return enjson(204,'空内容');
        }
        $h = time();
        $data = [];
        foreach ($info as $key => $value) {
            $data[$key] = $value;
            $data[$key]['sale_price']   = money($value->sale_price/100);
            $data[$key]['market_price'] = money($value->market_price/100);
            $data[$key]['user']         = empty($value->user) ? []: $value->user;
            $data[$key]['item']         = $value->item;
            if($h <= $value->end_time){
                if($h >= $value->start_time && $h <= $value->end_time){
                    $data[$key]['types'] = 1;
                }else if($h <= $value->end_time){
                    $data[$key]['types'] = 2;
                }
            }else{
                $data[$key]['types'] = 1;
            }
            if(time() > $value->end_time){
                $data[$key]['state'] = 0;
            }else{
                $data[$key]['state'] = $value->sale_nums > 0 ? 2 : 0;
            }
            $data[$key]['start_time']   = '开始 '.date('d日H:i',$value->start_time);
            $data[$key]['end_time']     = '结束 '.date('d日H:i',$value->end_time);
            $data[$key]['progress']     = $value['sale_nums'] < 100 ? 100-$value['sale_nums'] : 45;
            $data[$key]['sale_nums']    = $value['sale_nums'] <= 0 ?  0 : $value['sale_nums'] * 10;
            $item_ids = array_column(json_decode($value->gift),'item_id');
            $gift = [];
            foreach ($item_ids as $i => $id) {
                $item = Item::where(['id' => $id])->field('id,name,sell_price,img')->find()->toArray();
                $gift[$i] = $item;
                $gift[$i]['sell_price'] = money($item['sell_price']);

            }
            $data[$key]['gift'] = $gift;
        }
        return enjson(200,'成功',$data);
    }

    /**
     * 获取某个产品
     */
    public function item(){
        $param['id']   = Request::param('id/d',1);
        $param['sign'] = Request::param('sign');
        $rel = $this->apiSign($param);
        if($rel['code'] != 200){
            return enjson(204,'签名失败');
        }
        $condition[] = ['types','=',1];
        $condition[] = ['id','=',$param['id']];
        $info = AppSale::with(['Item'=> function($query) {
            $query->field('id,name,sell_price,img,imgs,content');
        }])->where($condition)->field('id,title,item_id,sale_price,market_price,sale_nums,gift,update_time,start_time,end_time')->order('id desc')->find();
        if(empty($info)){
            return enjson(204,'空内容');
        }
        $data = $info->toArray();
        $data['item'] = $info->item;
        $data['market_price']  = money($info->market_price/100);
        $data['sale_price']    = money($info->sale_price/100);
        $data['state']         = $info->sale_nums <= 0 ? 0 : 1;
        $h = time();
        if($h <= $info->end_time && $info->sale_nums > 0){
            if($h >= $info->start_time && $h <= $info->end_time){
                $data['types'] = 1;
                $state_text = '立即购买';
            }else if($h <= $info->end_time){
                $data['types'] = 0;
                $state_text = '活动未开始';
            }
        }else{
            $data['types'] = 0;
            $state_text = '活动结束';
        }
        $data['start_time']   = date('m-d H:i',$info->start_time);
        $data['end_time']     = date('m-d H:i',$info->end_time);
        $data['state_text']   = $state_text;
        $data['item']['imgs'] = json_decode($info->item->imgs,true);
        $item_ids = array_column(json_decode($info->gift),'item_id');
        $gift = [];
        foreach ($item_ids as $i => $id) {
            $gift[$i] = Item::where(['id' => $id])->field('id,name,sell_price,img,imgs,content')->find()->toArray();
        }
        array_unshift($gift,$info->item);
        $data['gift'] = $gift;
        return enjson(200,'成功',$data);
    }
}