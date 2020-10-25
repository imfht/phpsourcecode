<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 文章管理
 */
namespace app\green\controller;
use app\green\model\GreenNews;
use app\green\model\GreenNewsCate;

class News extends Common{

    public function initialize()
    {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'文章管理','url'=>url("news/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['lists'] = GreenNews::where($this->mini_program)->order('id desc')->paginate(20);
        return view()->assign($view);
    }

    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'cate_id' => input('post.cate_id/d'),
                'types'   => input('post.types/d'),
                'title'   => input('post.title/s','','htmlspecialchars'),
                'img'     => input('post.img/s','','htmlspecialchars'),
                'desc'    => input('post.desc/s','','htmlspecialchars'),
                'content' => input('post.content/s','','htmlspecialchars'),
            ];
            $validate = $this->validate($data,'news.save');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $cate = GreenNewsCate::field('name')->where(['id' => $data['cate_id']])->find();
            $data['cate_name'] = empty($cate['name']) ? '' : $cate['name'];
            $result =  GreenNews::edit($this->member_miniapp_id,$data);
            if($result){
                return enjson(200,'操作成功',['url' => url('news/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $view['cate'] =GreenNewsCate::where($this->mini_program)->order('sort desc,id desc')->select();
            return view()->assign($view);
        }
    }

    /**
     * 编辑
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'      => input('post.id/d'),
                'cate_id' => input('post.cate_id/d'),
                'types'   => input('post.types/d'),
                'title'   => input('post.title/s','','htmlspecialchars'),
                'img'     => input('post.img/s','','htmlspecialchars'),
                'desc'    => input('post.desc/s','','htmlspecialchars'),
                'content' => input('post.content/s','','htmlspecialchars'),
            ];
            $validate = $this->validate($data,'news.save');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $cate = GreenNewsCate::field('name')->where(['id' => $data['cate_id']])->find();
            $data['cate_name'] = empty($cate['name']) ? '' : $cate['name'];
            $result =  GreenNews::edit($this->member_miniapp_id,$data);
            if($result){
                return enjson(200,'操作成功',['url' => url('news/index')]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $id  = input('get.id/d');
            $view['info'] = GreenNews::where(['id' => $id,'member_miniapp_id' => $this->member_miniapp_id])->find();
            $view['cate'] =GreenNewsCate::where($this->mini_program)->order('sort desc,id desc')->select();
            return view()->assign($view);
        }
    }

     /**
     * 删除
     */
    public function delete(int $id){
        $condition = [];
        $condition['id'] = $id;
        $result = GreenNews::where($condition)->where($this->mini_program)->delete();
        if($result){
            return enjson(200,'操作成功');
        }else{
            return enjson(403,'操作失败');
        } 
    }
}