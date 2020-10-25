<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品管理
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;

class Item extends Manage{

    public function initialize() {
        parent::initialize();
        if(!model('auth')->getAuth($this->user->id,1)){
            $this->error('无权限,你非【内容管理员】');
        }
        $this->assign('pathMaps',[['name'=>'商品管理','url'=>url("fastshop/item/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['keyword']  = input('get.keyword');
        $view['status']   = input('?status') ? input('status') : 0;
        $view['page']     = input('?page') ? input('page') : 0;
        $view['lists']    = model('Item')->list($this->member_miniapp_id,$view['status'],$view['keyword']);
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'category_id'      => input('post.category_id/d'),
                'category_path_id' => input('post.category_path_id/s'),
                'name'             => input('post.name/s'),
                'price'            => input('post.price/f'),
                'sell_price'       => input('post.sell_price/f'),
                'market_price'     => input('post.market_price/f'),
                'cost_price'       => input('post.price/f'),
                'types'            => input('post.types/d'),
                'points'           => input('post.points/d'),
                'repoints'         => input('post.repoints/d'),
                'store_nums'       => input('post.store_nums/d'),
                'weight'           => input('post.weight/f'),
                'unit'             => input('post.unit/s'),
                'imgs'             => input('post.imgs/a'),
                'img'              => input('post.img/s'),
                'content'          => input('post.content/s'),
            ];
            $validate = $this->validate($data,'item.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  model('item')->edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('fastshop/item/index'),'msg'=>'操作成功']);
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
                'id'               => input('post.id/d'),
                'category_id'      => input('post.category_id/d'),
                'category_path_id' => input('post.category_path_id/s'),
                'types'            => input('post.types/d'),
                'points'           => input('post.points/d'),
                'repoints'         => input('post.repoints/d'),
                'name'             => input('post.name/s','','htmlspecialchars'),
                'price'            => input('post.price/f'),
                'sell_price'       => input('post.sell_price/f'),
                'market_price'     => input('post.market_price/f'),
                'cost_price'       => input('post.price/f'),
                'store_nums'       => input('post.store_nums/d'),
                'weight'           => input('post.weight/f'),
                'unit'             => input('post.unit/s','','htmlspecialchars'),
                'imgs'             => input('post.imgs/a'),
                'img'              => input('post.img/s','','htmlspecialchars'),
                'content'          => input('post.content/s','','htmlspecialchars'),
            ];
            $validate = $this->validate($data,'item.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  model('item')->edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('fastshop/item/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['id']   = input('get.id/d');
            $view['info'] = model('Item')->get($view['id']);
            $view['imgs'] = json_decode($view['info']['imgs'],true); 
            $view['status']  = input('?status') ? input('status'): 0;
            $view['page']    = input('?page') ? input('page')    : 0;
            //当前商品目录
            $category      = model('Category')->getPath($this->member_miniapp_id,$view['info']['category_id']);
            $category_path = null;
            foreach ($category as $key => $value) {
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
        $id  = input('?get.id')?input('get.id/d'):0;
        $ids = input('?post.ids')?input('post.ids/s'):null;
        $result = model('Item')->spu_delete($id,$ids);
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
     * 商品栏目
     */
    public function category(){
        if(request()->isAjax()){
            $parent_id = input('?get.parent_id') ? input('get.parent_id/d'): 0;
            $info      = model('Category')->field('id,parent_id,title')->where(['member_miniapp_id' => $this->member_miniapp_id,'parent_id' => $parent_id])->order(['sort'=>'desc','id'=>'desc'])->select();
            return json(['code'=>200,'msg'=>'操作成功','data'=>$info]);
        }else{
            $view['input'] = input('get.input');
            $view['path']  = input('get.path');
            return view('category',$view);
        }
    }
    
    /**
     * 及时返回当前路径
     */
    public function category_path(){
        $parent_id = input('?get.parent_id') ? input('get.parent_id/d'): 0;
        $info = model('Category')->getPath($this->member_miniapp_id,$parent_id);
        if($info){
            $category = [];
            foreach ($info as $key => $value) {
                $category[] = $value['id'];
            }
            $category_id = implode(',',$category);
            return json(['code'=>200,'msg'=>'操作成功','data'=>$info,'category_id' => $category_id]);
        }
        return json(['code'=>403,'msg'=>'读取商品分类路径失败']);
    }
    
    /**
    * 选择商品
    * @return void
    */
    public function select(){
        $view['keyword']  = input('get.keyword');
        $view['input']    = input('get.input');
        $view['status']   = 0;
        $view['lists']    = model('Item')->list($this->member_miniapp_id,$view['status'],$view['keyword'],$view['input']);
        $view['pathMaps'] = [['name' => '商品选择','url'=>'javascript:;']];
        return view()->assign($view);
    }

    /**
    * 选择商品
    * @return void
    */
    public function getView(){
        $id = input('get.id/d');
        $info = model('Item')->where(['id' => $id])->find();
        if(!empty($info)){
            $info['price'] = money($info['price']/100);
            $info['entrust_number'] = model('EntrustList')->where(['item_id' => $id,'is_rebate' => 0])->count();
        }
        return json(['code'=>200,'msg'=>'操作成功','data'=>$info]);
    }
}