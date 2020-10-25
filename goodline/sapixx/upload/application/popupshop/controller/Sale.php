<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 市场销售产品
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\popupshop\model\Sale as AppSale;
use think\facade\Request;
use app\popupshop\model\Sale as saleModel;

class Sale extends Manage{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'活动套装','url'=>url("popupshop/item/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['types'] = $this->request->param('types/d',0);
        $view['lists'] = AppSale::lists($this->member_miniapp_id,$view['types']);
        $view['platform'] = saleModel::where(['member_miniapp_id' => $this->member_miniapp_id,'user_id' => 0])->count();
        $view['user'] = saleModel::where(['member_miniapp_id' => $this->member_miniapp_id])->where('user_id','>',0)->count();
        $view['complete'] = saleModel::where(['member_miniapp_id' => $this->member_miniapp_id,'is_pay' => 1])->sum('user_sale_price');
        $view['no_complete'] = saleModel::where(['member_miniapp_id' => $this->member_miniapp_id,'is_pay' => 0])->sum('user_sale_price');
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'house_id'          => Request::param('house_id/d'),
                'cost_price'        => Request::param('cost_price/f'),
                'entrust_price'     => Request::param('entrust_price/f'),
                'sale_price'        => Request::param('sale_price/f'),
                'gift'              => Request::param('gift/a'),
                'member_miniapp_id' => $this->member_miniapp_id,
                'store_id'          => 0,
                'user_id'           => 0,
                'is_sale'           => 0,
            ];
            $validate = $this->validate($data,'Sale.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //处理产品数据
            $gift = [];
            foreach ($data['gift'] as $key => $value) {
                foreach ($value as $k => $v) {
                    $gift[$k][$key] = $v;
                }
            }
            foreach ($gift as $key => $value) {
                if(empty($value['house_id']) || empty($value['cost_price']) || empty($value['sale_price']) || empty($value['entrust_price'])){
                    return enjson(0,'价格信息填写不全'); 
                }else{
                    $gift[$key]['house_id']      = intval($value['house_id']);
                    $gift[$key]['cost_price']    = money($value['cost_price']);
                    $gift[$key]['entrust_price'] = money($value['entrust_price']);
                    $gift[$key]['sale_price']    = money($value['sale_price']);
                }
            }
            $data['gift'] = $gift;
            $result = AppSale::edit($data);
            if($result){
                return enjson(200,'成功',['url' => url('popupshop/sale/index'),]);
            }else{
                return enjson(0);
            }
        }else{
            $view['i'] = 0;
            return view()->assign($view);
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'                => Request::param('id/d'),
                'house_id'          => Request::param('house_id/d'),
                'cost_price'        => Request::param('cost_price/f'),
                'entrust_price'     => Request::param('entrust_price/f'),
                'sale_price'        => Request::param('sale_price/f'),
                'gift'              => Request::param('gift/a'),
                'store_id'          => Request::param('store_id/d'),
                'user_id'           => Request::param('user_id/d'),
                'is_sale'           => Request::param('is_sale/d'),
                'member_miniapp_id' => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'Sale.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //处理产品数据
            $gift = [];
            foreach ($data['gift'] as $key => $value) {
                foreach ($value as $k => $v) {
                    $gift[$k][$key] = $v;
                }
            }
            foreach ($gift as $key => $value) {
                if(empty($value['house_id']) || empty($value['cost_price']) || empty($value['sale_price']) || empty($value['entrust_price'])){
                    return enjson(0,'价格信息填写不全'); 
                }else{
                    $gift[$key]['house_id']      = intval($value['house_id']);
                    $gift[$key]['cost_price']    = money($value['cost_price']);
                    $gift[$key]['entrust_price'] = money($value['entrust_price']);
                    $gift[$key]['sale_price']    = money($value['sale_price']);
                }
            }
            $data['gift'] = $gift;
            $result = AppSale::edit($data);
            if($result){
                return enjson(200,'成功',['url' => url('popupshop/sale/index'),]);
            }else{
                return enjson(0);
            }
        }else{
            $info  = AppSale::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => Request::param('id/d',0)])->find();
            if(empty($info)){
                $this->error('产品不存在');
            }
            $info->gift = json_decode($info->gift,true);
            $gift_num = count($info->gift);
            $view['info']  = $info;
            $view['i']     = $gift_num ? $gift_num-1 : 0;
            return view()->assign($view);
        }
    }

     /**
     * 删除
     */
    public function delete(){
        $result = AppSale::where(['id' => $this->request->param('id'),'is_pay' => 0])->delete();
        if($result){
            
            return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
        }else{
            return json(['code'=>403,'msg'=>'操作失败']);
        } 
    }


     /**
     * 下架
     */
    public function offSale(){
        $result = AppSale::where(['id' => $this->request->param('id')])->update(['is_sale' => 0]);
        if($result){
            return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
        }else{
            return json(['code'=>403,'msg'=>'操作失败']);
        } 
    }
    

     /**
     * 上架
     */
    public function onSale(){
        $result = AppSale::where(['id' => $this->request->param('id'),'is_pay' => 0,'is_out' => 0])->update(['is_sale' => 1]);
        if($result){
            return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
        }else{
            return json(['code'=>403,'msg'=>'已提货/已成交商品禁止上架']);
        } 
    }
}