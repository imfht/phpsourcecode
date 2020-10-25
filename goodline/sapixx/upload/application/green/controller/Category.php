<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 商品分类管理
 */
namespace app\green\controller;
use app\green\model\GreenCategory;

class Category extends Common{

    public function initialize() {
        parent::initialize();
        if(!$this->founder){
            $this->error('您无权限操作');
        }
        $this->cate  = new GreenCategory();
    }

    /**
     * 列表
     */
    public function index(){
        $view['parent_id'] = $this->request->param('parent_id/d',0);
        $view['pathMaps']  = $this->cate->selectPath($view['parent_id']);
        $view['lists']     = $this->cate->where(['member_miniapp_id' => $this->member_miniapp_id,'parent_id' => $view['parent_id']])->order('sort desc,id desc')->paginate(20);
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'member_miniapp_id' => $this->member_miniapp_id,
                'title'             => $this->request->param('title/s'),
                'name'              => $this->request->param('name/s'),
                'sort'              => $this->request->param('sort/d'),
                'parent_id'         => $this->request->param('parent_id/d'),
                'picture'           => $this->request->param('picture/s'),
                'price'             => $this->request->param('price/s')
            ];
            $validate = $this->validate($data,'Category.add');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  $this->cate->edit($data);
            if($result){
                return enjson(200,'操作成功',['url'=>url('category/index',['parent_id' => $data['parent_id']])]);
            }else{
                return enjson(0,'删除失败');
            }
        }else{
            $view['parent_id'] = $this->request->param('parent_id/d',0);
            $view['pathMaps']  = $this->cate->selectPath($view['parent_id']);
            return view()->assign($view);
        }
    }

    //编辑
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'        => $this->request->param('id/s'),
                'title'     => $this->request->param('title/s'),
                'name'      => $this->request->param('name/s'),
                'sort'      => $this->request->param('sort/d'),
                'parent_id' => $this->request->param('parent_id/d'),
                'picture'   => $this->request->param('picture/s'),
                'price'     => $this->request->param('price/s')
            ];
            $validate = $this->validate($data,'Category.edit');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  $this->cate->edit($data);
            if($result){
                return enjson(200,'操作成功',['url'=>url('category/index',['parent_id' => $data['parent_id']])]);
            }else{
                return enjson(0,'删除失败');
            }
        }else{
            $view['info'] = $this->cate->where(['id' => $this->request->param('id/d'),'member_miniapp_id' => $this->member_miniapp_id])->find();
            if(empty($view['info'])){
                $this->error("404 NOT FOUND");
            }
            $view['pathMaps'] = $this->cate->selectPath($view['info']->parent_id);
            return $this->fetch()->assign($view);
        }
    }

    /**
     * 排序
     */
    public function sort(){
        if(request()->isAjax()){
            $data = [
                'sort' => $this->request->param('sort/d'),
                'id'   => $this->request->param('id/d'),
            ];
            $validate = $this->validate($data,'Category.sort');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result = $this->cate->save(['sort'=>$data['sort']],['id' => $data['id']]);
            if($result){
                return enjson(200,'操作成功');
            }else{
                return enjson(0,'删除失败');
            }
        }
    }

    //删除
    public function delete(int $id){
        $info =  $this->cate->where(['parent_id' => $id])->find();
        if($info){
            return enjson(403,'删除失败,请查看是否包含子栏目');
        }
        $result = $this->cate->where(['id'=>$id,'member_miniapp_id' => $this->member_miniapp_id])->delete();
        if($result){
            return enjson(200,'操作成功');
        }else{
            return enjson(403,'删除失败,请查看是否包含子栏目');
        }
    }
}