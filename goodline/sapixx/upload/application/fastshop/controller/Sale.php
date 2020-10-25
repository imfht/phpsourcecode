<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 市场销售产品
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;

class Sale extends Manage{

    public function initialize() {
        parent::initialize();
        if(!model('auth')->getAuth($this->user->id,1)){
            $this->error('无权限,你非【内容管理员】');
        }
        $this->assign('pathMaps',[['name'=>'抢购管理','url'=>url("fastshop/item/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['lists'] = model('Sale')->where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->paginate(10);
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'category_id'   => input('post.category_id/d'),
                'types'         => input('post.types/d'),
                'title'         => input('post.title/s','','htmlspecialchars'),
                'sale_nums'     => input('post.sale_nums/d'),
                'item_id'       => input('post.item_id/d'),
                'cost_price'    => input('post.cost_price/f'),
                'market_price'  => input('post.market_price/f'),
                'sale_price'    => input('post.sale_price/f'),
                'gift'          => input('post.gift/a'),
                'start_time'    => input('post.start_time/s'),
                'end_time'      => input('post.end_time/s'),
                'is_vip'        => input('post.is_vip/d',0),
                'is_newuser'    => input('post.is_newuser/d',0),
                'is_fusion'    => input('post.is_fusion/d',0),
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
                if(empty($value['item_id']) || empty($value['cost_price']) || empty($value['sale_price']) || empty($value['market_price'])){
                    return json(['code'=>0,'msg'=>'价格信息填写不全']); 
                }else{
                    $gift[$key]['item_id']      = $value['item_id'];
                    $gift[$key]['cost_price']   = $value['cost_price']*100;
                    $gift[$key]['sale_price']   = $value['sale_price']*100;
                    $gift[$key]['market_price'] = $value['market_price']*100;
                }
            }
            $data['gift'] = $gift;
            $result =  model('Sale')->edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('fastshop/sale/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['i'] = 0;
            $view['times'] = model('Times')->where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->select(); 
            return view()->assign($view);
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'            => input('post.id/d'),
                'category_id'   => input('post.category_id/d'),
                'types'         => input('post.types/d'),
                'title'         => input('post.title/s','','htmlspecialchars'),
                'sale_nums'     => input('post.sale_nums/d'),
                'item_id'       => input('post.item_id/d'),
                'cost_price'    => input('post.cost_price/f'),
                'market_price'  => input('post.market_price/f'),
                'sale_price'    => input('post.sale_price/f'),
                'gift'          => input('post.gift/a'),
                'start_time'    => input('post.start_time/s'),
                'end_time'      => input('post.end_time/s'),
                'is_vip'        => input('post.is_vip/d',0),
                'is_newuser'    => input('post.is_newuser/d',0),
                'is_fusion'    => input('post.is_fusion/d',0),
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
                if(empty($value['item_id']) || empty($value['cost_price']) || empty($value['sale_price']) || empty($value['market_price'])){
                    return json(['code'=>0,'msg'=>'价格信息填写不全']); 
                }else{
                    $gift[$key]['item_id']      = $value['item_id'];
                    $gift[$key]['cost_price']   = $value['cost_price']*100;
                    $gift[$key]['sale_price']   = $value['sale_price']*100;
                    $gift[$key]['market_price'] = $value['market_price']*100;
                }
            }
            $data['gift'] = $gift;
            $result =  model('Sale')->edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('fastshop/sale/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['id']    = input('get.id/d');
            $view['times'] = model('Times')->where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->select(); 
            $view['info']  = model('Sale')->get($view['id']);
            $view['info']['gift'] = json_decode($view['info']['gift'],true);
            $i = count($view['info']['gift']);
            $view['i'] = $i ? $i-1 : 0;
            return view()->assign($view);
        }
    }

     /**
     * 删除
     */
    public function delete(){
        $id  = input('get.id');
        $rel = model('Order')->where(['member_miniapp_id' => $this->member_miniapp_id,'sale_id' => $id])->find(); 
        if($rel){
            return json(['code'=>403,'msg'=>'当前活动已有订单,禁止删除,建议下架操作']);
        }
        $result = model('sale')->destroy($id);
        if($result){
            return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
        }else{
            return json(['code'=>403,'msg'=>'操作失败']);
        } 
    }

    /**
     * 上架,下架,从回收站恢复
     */
    public function ids_action(){
        if(request()->isAjax()){
            $issale = input('get.issale/d');
            $ids    = input('post.ids/s');
            if(empty($ids)){
                return json(['code'=>403,'msg'=>'没有选择任何要操作商品']);
            }else{
                model('Item')->ids_action($issale,$ids);
                return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
            }
        }
    }
    
    /**
     * 排序
     */
    public function sort(){
        if(request()->isAjax()){
            $data = [
                'sort' => input('post.sort/d'),
                'id'   => input('post.id/d'),
            ];
            $validate = $this->validate($data,'Category.sort');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result = model('Sale')->where(['id' => $data['id'],'member_miniapp_id' => $this->member_miniapp_id])->update(['sort'=>$data['sort']]);
            if($result){
                return json(['code'=>200,'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }
    }
}