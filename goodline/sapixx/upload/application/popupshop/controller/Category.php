<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品分类管理
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;
use app\popupshop\model\Category as AppCategory;
use app\popupshop\model\Item;
use think\facade\Request;

class Category extends Manage{


    /**
     * 列表
     */
    public function index(){
        $parent_id        = Request::param('parent_id/d',0);
        $view['pathMaps'] = AppCategory::selectPath($this->member_miniapp_id,$parent_id);  
        $view['lists']    = AppCategory::where(['parent_id' => $parent_id,'member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->paginate(20); 
        $view['parent_id'] = $parent_id;
        return view('index',$view);
    }
    
    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'title'             => input('post.title/s'),
                'name'              => input('post.name/s'),
                'sort'              => input('post.sort/d'),
                'parent_id'         => input('post.parent_id/d'),
                'picture'           => input('post.picture/s'),
                'types'             => input('post.types/d'),
                'member_miniapp_id' => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'Category.add');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  AppCategory::edit($data);
            if($result){
                return enjson(200,'操作成功',['url'=>url('popupshop/category/index',['parent_id'=>$data['parent_id']])]);
            }else{
                return enjson(0);
            }
        }else{
            $parent_id         = Request::param('parent_id/d',0);
            $view['pathMaps']  = AppCategory::selectPath($this->member_miniapp_id,$parent_id);  
            $view['parent_id'] = $parent_id;
            return view('add',$view);
        }
    }

    //编辑
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'        => input('post.id/s'),
                'title'     => input('post.title/s'),
                'name'      => input('post.name/s'),
                'sort'      => input('post.sort/d'),
                'parent_id' => input('post.parent_id/d'),
                'types'     => input('post.types/d'),
                'picture'   => input('post.picture/s'),
            ];
            $validate = $this->validate($data,'Category.edit');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  AppCategory::edit($data);
            if($result){
                return enjson(200,'操作成功',['url'=>url('popupshop/category/index',['parent_id'=>$data['parent_id']])]);
            }else{
                return enjson(0);
            }
        }else{
            $info = AppCategory::where(['id' => Request::param('id/d',0),'member_miniapp_id' => $this->member_miniapp_id])->find();
            if(!$info){
                $this->error("404 NOT FOUND");
            }
            $view['pathMaps'] = AppCategory::selectPath($this->member_miniapp_id,$info['parent_id']);  
            $view['info']     = $info;
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
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result = AppCategory::update(['sort'=>$data['sort']],['id' => $data['id']]);
            if($result){
                return enjson(200);
            }else{
                return enjson(0);
            }
        }
    }

    //删除
    public function delete(){
        $id   = Request::param('id/d',0);
        $info = AppCategory::where(['parent_id' => $id,'member_miniapp_id' => $this->member_miniapp_id])->find();
        if($info){
            return enjson(403,'删除失败,请查看是否包含子栏目');
        }
        $goods =  Item::where(['category_id' => $id,'member_miniapp_id' => $this->member_miniapp_id])->find();
        if($goods){
            return enjson(403,'删除失败,栏目中还包含商品');
        }
        $result = AppCategory::destroy($id);
        if($result){
            return enjson(200);
        }else{
            return enjson(403,'删除失败,请查看是否包含子栏目');
        } 
    }

    //全选删除
    public function alldelete(){
        $ids = ids(Request::param('ids/s',''),true);
        foreach ($ids as $id) {
            $goods =  Item::where(['category_id' => $id,'member_miniapp_id' => $this->member_miniapp_id])->find();
            if(empty($goods)){
                $info = AppCategory::where(['parent_id' => $id,'member_miniapp_id' => $this->member_miniapp_id])->find();
                if(empty($info)){
                    AppCategory::destroy(['id' => $id]);
                }
            }        
        }
        return enjson(200,'操作成功,如有未删除的,可能含有子栏目或商品');
    }   
}