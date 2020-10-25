<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 栏目管理
 */
namespace app\green\controller;
use app\green\model\GreenNews;
use app\green\model\GreenNewsCate;

class Newscate extends Common{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'文章分类','url'=>url("newscate/index")]]);
    }
    /**
     * 列表
     */
    public function index(){
        $view['lists'] = GreenNewsCate::where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->paginate(20);
        return view()->assign($view);
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
                'sort'              => input('post.sort/d'),
                'picture'           => input('post.picture/s')
            ];
            $validate = $this->validate($data,'Cate.news');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  GreenNewsCate::edit($data);
            if($result){
                return enjson(200,'操作成功',['url' => url('newscate/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            return view();
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
                'picture'   => input('post.picture/s'),
            ];
            $validate = $this->validate($data,'Cate.news');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  GreenNewsCate::edit($data);
            if($result){
                return enjson(200,'操作成功',['url' => url('newscate/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $id   = input('get.id/d');
            $info = GreenNewsCate::get($id);
            if(!$info){
                $this->error("404 NOT FOUND");
            }
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
            $validate = $this->validate($data,'Cate.sort');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result = GreenNewsCate::where(['id' => $data['id']])->update(['sort'=>$data['sort']]);
            if($result){
                return enjson(200,'操作成功');
            }else{
                return enjson(0,'操作失败');
            }
        }
    }

    //删除
    public function delete(){
        $id = input('get.id/d');
        $goods =  GreenNews::get(['cate_id' => $id]);
        if($goods){
            return enjson(403,'删除失败,栏目中还包含内容');
        }
        $result = GreenNewsCate::where(['id'=>$id,'member_miniapp_id' => $this->member_miniapp_id])->delete();
        if($result){
            return enjson(200,'操作成功');
        }else{
            return enjson(403,'操作失败');
        } 
    }
}