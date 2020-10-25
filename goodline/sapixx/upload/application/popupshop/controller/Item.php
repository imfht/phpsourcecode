<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品管理
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\popupshop\model\Category;
use app\popupshop\model\Item as AppItem;
use think\facade\Request;

class Item extends Manage{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'商品管理','url'=>url("popupshop/item/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['keyword']  = Request::param('keyword');
        $view['status']   = Request::param('status',0);
        $view['page']     = Request::param('page/d',0);
        $view['lists']    = AppItem::list($this->member_miniapp_id,$view['status'],$view['keyword']);
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'category_id'      => Request::param('category_id/d'),
                'category_path_id' => Request::param('category_path_id/s'),
                'name'             => Request::param('name/s'),
                'note'             => Request::param('note/s'),
                'sell_price'       => Request::param('sell_price/f'),
                'market_price'     => Request::param('market_price/f'),
                'cost_price'       => Request::param('cost_price/f'),
                'types'            => Request::param('types/d'),
                'points'           => Request::param('points/d'),
                'repoints'         => Request::param('repoints/d'),
                'store_nums'       => Request::param('store_nums/d'),
                'weight'           => Request::param('weight/f'),
                'unit'             => Request::param('unit/s'),
                'imgs'             => Request::param('imgs/a'),
                'img'              => Request::param('img/s'),
                'content'          => Request::param('content/s'),
            ];
            $validate = $this->validate($data,'item.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  AppItem::edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('popupshop/item/index'),'msg'=>'操作成功']);
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
                'category_id'      => Request::param('category_id/d'),
                'category_path_id' => Request::param('category_path_id/s'),
                'types'            => Request::param('types/d'),
                'points'           => Request::param('points/d'),
                'repoints'         => Request::param('repoints/d'),
                'name'             => Request::param('name/s'),
                'note'             => Request::param('note/s'),
                'sell_price'       => Request::param('sell_price/f'),
                'market_price'     => Request::param('market_price/f'),
                'cost_price'       => Request::param('cost_price/f'),
                'store_nums'       => Request::param('store_nums/d'),
                'weight'           => Request::param('weight/f'),
                'unit'             => Request::param('unit/s'),
                'imgs'             => Request::param('imgs/a'),
                'img'              => Request::param('img/s'),
                'content'          => Request::param('content/s'),
            ];
            $validate = $this->validate($data,'item.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  AppItem::edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('popupshop/item/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['id']     = Request::param('id/d');
            $view['info']   = AppItem::where(['id' => $view['id']])->find();
            $view['imgs']   = json_decode($view['info']->imgs,true); 
            $view['status'] = Request::param('status/d',0);
            $view['page']   = Request::param('page/d',0);
            //当前商品目录
            $category      = Category::getPath($this->member_miniapp_id,$view['info']->category_id);
            $category_path = null;
            foreach ($category as $value) {
                $category_path .= $value['title'].' / ';
            }
            $view['category_path'] = $category_path;
            return view()->assign($view);
        }
    }

     /**
     * 删除
     */
    public function delete(){
        $id  =  Request::param('id/d',0);
        $ids =  Request::param('ids/s');
        $result = AppItem::spu_delete($id,$ids);
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
                AppItem::ids_action($issale,$ids);
                return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
            }
        }
    }
    
    /**
     * 商品栏目
     */
    public function category(){
        if(request()->isAjax()){
            $parent_id = Request::param('parent_id/d',0);
            $info      = Category::where(['member_miniapp_id' => $this->member_miniapp_id,'parent_id' => $parent_id])->field('id,parent_id,title')->order(['sort'=>'desc','id'=>'desc'])->select();
            return json(['code'=>200,'msg'=>'操作成功','data'=>$info]);
        }else{
            $view['input'] = Request::param('input');
            $view['path']  = Request::param('path');
            return view('category',$view);
        }
    }
    
    /**
     * 及时返回当前路径
     */
    public function category_path(){
        $parent_id = Request::param('parent_id/d',0);
        $info = Category::getPath($this->member_miniapp_id,$parent_id);
        if($info){
            $category = [];
            foreach ($info as $value) {
                $category[] = $value['id'];
            }
            $category_id = implode(',',$category);
            return json(['code'=>200,'msg'=>'操作成功','data'=>$info,'category_id' => $category_id]);
        }
        return json(['code'=>403,'msg'=>'读取商品分类路径失败']);
    }
}