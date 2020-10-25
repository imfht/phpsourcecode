<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品分类管理
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;

class Category extends Manage{

    public function initialize() {
        parent::initialize();
        if(!model('auth')->getAuth($this->user->id,1)){
            $this->error('无权限,你非【内容管理员】');
        }
    }

    /**
     * 列表
     */
    public function index(){
        $parent_id = input('?get.parent_id') ? input('get.parent_id/d') :0;
        $view['pathMaps'] = model('Category')->selectPath($this->member_miniapp_id,$parent_id);  
        $view['lists']    = model('Category')->where(['parent_id' => $parent_id,'member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->paginate(20); 
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
            $result =  model('Category')->edit($data);
            if($result){
                return json(['code'=>200,'url'=>url('fastshop/category/index',['parent_id'=>$data['parent_id']]),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $parent_id = input('?get.parent_id') ? input('get.parent_id/d') :0;
            $view['pathMaps']  = model('Category')->selectPath($this->member_miniapp_id,$parent_id);  
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
            $result =  model('Category')->edit($data);
            if($result){
                return json(['code'=>200,'url'=>url('fastshop/category/index',['parent_id'=>$data['parent_id']]),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $id   = input('get.id/d');
            $info = model('Category')->get($id);
            if(!$info){
                $this->error("404 NOT FOUND");
            }
            $view['pathMaps'] = model('Category')->selectPath($this->member_miniapp_id,$info['parent_id']);  
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
            $result = model('Category')->save(['sort'=>$data['sort']],['id' => $data['id']]);
            if($result){
                return json(['code'=>200,'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }
    }

    //删除
    public function delete(){
        $id = input('get.id/d');
        $info = model('Category')->get(['parent_id' => $id]);
        if($info){
            return json(['code'=>403,'msg'=>'删除失败,请查看是否包含子栏目']);
        }
        $goods =  model('Item')->get(['category_id' => $id]);
        if($goods){
            return json(['code'=>403,'msg'=>'删除失败,栏目中还包含商品']);
        }
        $result = model('Category')->destroy(['id' => $id,'member_miniapp_id' => $this->member_miniapp_id]);
        if($result){
            return json(['code'=>200,'msg'=>'操作成功']);
        }else{
            return json(['code'=>403,'msg'=>'删除失败,请查看是否包含子栏目']);
        } 
    }

    //全选删除
    public function alldelete(){
        $ids = input('?post.ids')?input('post.ids/s'):null;
        $ids = ids($ids,true);
        foreach ($ids as $id) {
            $goods =  model('Item')->get(['category_id' => $id]);
            if(empty($goods)){
                $info = model('Category')->get(['parent_id' => $id]);
                if(empty($info)){
                    model('Category')->destroy(['id' => $id,'member_miniapp_id' => $this->member_miniapp_id]);
                }
            }        
        }
        return json(['code'=>200,'msg'=>'操作成功,如有未删除的,可能含有子栏目或商品','data' => []]);
    }   
}