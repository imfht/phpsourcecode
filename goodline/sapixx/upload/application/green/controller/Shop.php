<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品管理
 */
namespace app\green\controller;
use app\green\model\GreenCategory;
use app\green\model\GreenShop;
use think\facade\Request;

class Shop extends Common{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'商品管理','url'=>url("green/shop/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['keyword']  = Request::param('keyword');
        $view['status']   = Request::param('status',0);
        $view['page']     = Request::param('page/d',0);
        $view['lists']    = GreenShop::list($this->member_miniapp_id,$view['status'],$view['keyword']);
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'name'             => Request::param('name/s'),
                'note'             => Request::param('note/s'),
                'points'           => Request::param('points/d'),
                'imgs'             => Request::param('imgs/a'),
                'img'              => Request::param('img/s'),
                'content'          => Request::param('content/s'),
            ];
            $validate = $this->validate($data,'shop.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  GreenShop::edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('green/shop/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            return view();
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'               => Request::param('id/d'),
                'points'           => Request::param('points/d'),
                'name'             => Request::param('name/s'),
                'note'             => Request::param('note/s'),
                'imgs'             => Request::param('imgs/a'),
                'img'              => Request::param('img/s'),
                'content'          => Request::param('content/s'),
            ];
            $validate = $this->validate($data,'shop.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  GreenShop::edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('green/shop/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['id']     = Request::param('id/d');
            $view['info']   = GreenShop::where(['id' => $view['id']])->find();
            $view['imgs']   = json_decode($view['info']->imgs,true); 
            $view['status'] = Request::param('status/d',0);
            $view['page']   = Request::param('page/d',0);
            return view()->assign($view);
        }
    }

     /**
     * 删除
     */
    public function delete(){
        $id  =  Request::param('id/d',0);
        $ids =  Request::param('ids/s');
        $result = GreenShop::spu_delete($id,$ids);
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
            $issale = Request::param('issale/d',0);
            $ids    = Request::param('ids/s');
            if(empty($ids)){
                return json(['code'=>403,'msg'=>'没有选择任何要操作商品']);
            }else{
                GreenShop::ids_action($issale,$ids);
                return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
            }
        }
    }    
}