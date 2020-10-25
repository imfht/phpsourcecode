<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 栏目管理
 */
namespace app\green\controller;
use app\green\model\GreenNews;
use app\green\model\GreenRecruit;

class Recruit extends Common{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'招募管理','url'=>url("recruit/index")]]);
    }
    /**
     * 列表
     */
    public function index(){
        $view['lists'] = GreenRecruit::where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->paginate(20);
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
                'state'             => input('post.state/d'),
                'news_id'           => input('post.news_id/d')
            ];
            $validate = $this->validate($data,'Recruit.edit');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  GreenRecruit::edit($data);
            if($result){
                return enjson(200,'操作成功',['url' => url('recruit/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $view['lists'] = GreenNews::where($this->mini_program)->order('id desc')->select();
            return view()->assign($view);
        }
    }

    //编辑
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'      => input('post.id/s'),
                'title'   => input('post.title/s'),
                'name'    => input('post.name/s'),
                'sort'    => input('post.sort/d'),
                'state'   => input('post.state/d'),
                'news_id' => input('post.news_id/d')
            ];
            $validate = $this->validate($data,'Recruit.edit');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  GreenRecruit::edit($data);
            if($result){
                return enjson(200,'操作成功',['url' => url('recruit/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $id   = input('get.id/d');
            $info = GreenRecruit::get($id);
            if(!$info){
                $this->error("404 NOT FOUND");
            }
            $view['info']     = $info;
            $view['lists'] = GreenNews::where($this->mini_program)->order('id desc')->select();
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
            $result = GreenRecruit::where(['id' => $data['id']])->update(['sort'=>$data['sort']]);
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
        $result = GreenRecruit::where(['id'=>$id,'member_miniapp_id' => $this->member_miniapp_id])->delete();
        if($result){
            return enjson(200,'操作成功');
        }else{
            return enjson(403,'操作失败');
        } 
    }
}