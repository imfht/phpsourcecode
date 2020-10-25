<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品管理
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\popupshop\model\SaleHouse as House;
use app\popupshop\model\SaleCategory;
use think\facade\Request;

class SaleHouse extends Manage{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'商品仓库','url'=>url("popupshop/SaleHouse/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['keyword']  = Request::param('keyword');
        $view['status']   = Request::param('status',0);
        $view['page']     = Request::param('page',0);
        $view['lists']    = House::list($this->member_miniapp_id,$view['status'],$view['keyword']);
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'category_id'      => Request::param('category_id/d'),
                'title'            => Request::param('title/s'),
                'name'             => Request::param('name/s'),
                'note'             => Request::param('note/s'),
                'sell_price'       => Request::param('sell_price/f'),
                'cost_price'       => Request::param('cost_price/f'),
                'imgs'             => Request::param('imgs/a'),
                'img'              => Request::param('img/s'),
                'content'          => Request::param('content/s'),
            ];
            $validate = $this->validate($data,'SaleHouse.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  House::edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('popupshop/saleHouse/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['cate'] = SaleCategory::where(['member_miniapp_id' => $this->member_miniapp_id])->select();
            return view()->assign($view);
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'               => Request::param('id/d'),
                'category_id'      => Request::param('category_id/d'),
                'title'            => Request::param('title/s'),
                'name'             => Request::param('name/s'),
                'note'             => Request::param('note/s'),
                'sell_price'       => Request::param('sell_price/f'),
                'cost_price'       => Request::param('cost_price/f'),
                'imgs'             => Request::param('imgs/a'),
                'img'              => Request::param('img/s'),
                'content'          => Request::param('content/s'),
            ];
            $validate = $this->validate($data,'SaleHouse.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  House::edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('popupshop/saleHouse/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['id']      = Request::param('id/d');
            $view['info']    = House::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $view['id']])->find();
            $view['cate']    = SaleCategory::where(['member_miniapp_id' => $this->member_miniapp_id])->select();
            $view['imgs']    = json_decode($view['info']['imgs'],true); 
            $view['status']  = Request::param('status',0);
            $view['page']    = Request::param('page',0);
            return view()->assign($view);
        }
    }

     /**
     * 删除
     */
    public function delete(){
        $id  = Request::param('id/d',0);
        $ids = Request::param('ids/s');
        $result = House::deletes($id,$ids);
        if($result){
            return enjson();
        }else{
            return enjson(403);
        } 
    }

    /**
     * 上架,下架,从回收站恢复
     */
    public function ids_action(){
        if(request()->isAjax()){
            $issale = Request::param('issale/d',0);
            $ids    = Request::param('ids/s');
            if(empty($ids)){
                return enjson(403,'没有选择任何要操作商品');
            }else{
                House::ids_action($issale,$ids);
                return enjson();
            }
        }
    }

     /**
    * 选择商品
    * @return void
    */
    public function select(){
        $view['pathMaps'] = [['name' => '商品选择','url'=>'javascript:;']];
        $view['keyword']  = Request::param('keyword');
        $view['input']    = Request::param('input','item_id');
        $view['lists']    = House::list($this->member_miniapp_id,'',$view['keyword']);
        return view()->assign($view);
    }

     /**
    * 选择商品读取价格
    * @return void
    */
    public function getView(){
        $id   = Request::param('id',0);
        $info = House::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->find();
        $data = [];
        $data['sell_price'] = $info->sell_price;
        $data['cost_price'] = $info->cost_price;
        return enjson(200,'成功',$data);
    }
}