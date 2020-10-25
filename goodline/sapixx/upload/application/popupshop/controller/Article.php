<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 文章管理
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;

class Article extends Manage
{

    public function initialize()
    {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'内容管理','url'=>'javascript:;']]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['lists'] = model('Article')->where(['member_miniapp_id' => $this->member_miniapp_id])->order('id desc')->paginate(20);
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'types'   => input('post.types/d'),
                'title'   => input('post.title/s'),
                'content' => input('post.content/s'),
            ];
            $validate = $this->validate($data,'article.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  model('Article')->edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('article/index'),'msg'=>'操作成功']);
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
                'id'      => input('post.id/d'),
                'types'   => input('post.types/d'),
                'title'   => input('post.title/s'),
                'content' => input('post.content/s'),
            ];
            $validate = $this->validate($data,'article.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  model('Article')->edit($this->member_miniapp_id,$data);
            if($result){
                return json(['code'=>200,'url' => url('article/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $id  = input('get.id/d');
            $view['info'] = model('Article')->where(['id' => $id,'member_miniapp_id' => $this->member_miniapp_id])->find();
            return view()->assign($view);
        }
    }

     /**
     * 删除
     */
    public function delete(){
        $id     = input('get.id/d');
        $result = model('Article')->where(['id' => $id,'member_miniapp_id' => $this->member_miniapp_id])->delete();
        if($result){
            return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
        }else{
            return json(['code'=>403,'msg'=>'操作失败']);
        } 
    }
}