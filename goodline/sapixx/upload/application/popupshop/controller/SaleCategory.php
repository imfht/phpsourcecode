<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品分类管理
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\popupshop\model\Sale;
use app\popupshop\model\SaleCategory as Category;
use think\facade\Request;
use app\popupshop\model\Item;

class SaleCategory extends Manage{

    public function initialize()
    {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'库存分类','url'=>'javascript:;']]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['lists'] = Category::where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->paginate(20);
        return view('index',$view);
    }
    
    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'member_miniapp_id' => $this->member_miniapp_id,
                'title'             => input('post.title/s'),
                'name'              => input('post.name/s'),
                'picture'           => input('post.picture/s'),
            ];
            $validate = $this->validate($data,'Category.cate');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  Category::edit($data);
            if($result){
                return enjson(200,'操作成功',['url'=>url('popupshop/saleCategory/index')]);
            }else{
                return enjson(0);
            }
        }else{
            return view();
        }
    }

    //编辑
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'        => input('post.id/d'),
                'title'     => input('post.title/s'),
                'name'      => input('post.name/s'),
                'picture'   => input('post.picture/s'),
            ];
            $validate = $this->validate($data,'Category.cate');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  Category::edit($data);
            if($result){
                return enjson(200,'操作成功',['url'=>url('popupshop/saleCategory/index')]);
            }else{
                return enjson(0);
            }
        }else{
            $id  = Request::param('id/d');
            $view['info'] = Category::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->find();
            return view('edit',$view);
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
                return enjson(0,$validate);
            }
            $result = Category::where(['id' => $data['id']])->update(['sort'=>$data['sort']]);
            if($result){
                return enjson(200);
            }else{
                return enjson(0);
            } 
        }
    }

    //删除
    public function delete(){
        $id  = Request::param('id/d');
        $goods = Item::where(['category_id' => $id])->find();
        if($goods){
            return enjson(403,'删除失败,栏目中还包含商品');
        }
        $result = Category::destroy(['id' => $id,'member_miniapp_id' => $this->member_miniapp_id]);
        if($result){
            return enjson(200);
        }else{
            return enjson(403);
        } 
    }  
}