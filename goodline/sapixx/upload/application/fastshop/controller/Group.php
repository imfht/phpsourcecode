<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 团购管理
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;

class Group extends Manage
{
    public function initialize(){
        parent::initialize();
        $this->mini_program = ['member_miniapp_id' => $this->member_miniapp_id];
        $this->assign('pathMaps', [['name'=>'团购管理','url'=>url("group/index")]]);
    }

    /**
     * 团购管理
     */
    public function index(){
        $view['lists'] = model('Group')->lists($this->member_miniapp_id);
        return view()->assign($view);
    }

    /**
     *  特性专题
     */
    public function add(){
        $view['cid']     = (int)input('get.cid/d');
        $view['keyword'] = input('get.keyword/s');
        $view['lists']   = model('Group')->itemList($this->member_miniapp_id,$view['cid'],$view['keyword']);
        return view()->assign($view);
    }  
    
     /**
     * 删除
     */
    public function delete(){
        $id  = input('get.id/d');
        $result = model('Group')->where($this->mini_program)->where(['id' =>$id])->delete();
        if($result){
            return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
        }else{
            return json(['code'=>403,'msg'=>'操作失败']);
        } 
    }

    /**
     * 商品栏目选择
     */
    public function category(){
        $view['index'] = (int)input('get.index');
        return view()->assign($view);
    }

    /**
     * 排序
     */
    public function amount(){
        if(request()->isAjax()){
            $data = [
                'sort' => input('post.sort/f'),
                'id'   => input('post.id/d'),
            ];
            $validate = $this->validate($data,'Category.sort');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $data['sort'] = money($data['sort']);
            $result = model('Group')->where($this->mini_program)->where(['id' => $data['id']])->update(['amount' => $data['sort']]);
            if($result){
                return json(['code'=>200,'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }
    }  
    
    /**
     * 几人成团
     * @return void
     */
    public function hao_people(){
        if(request()->isAjax()){
            $data = [
                'sort' => input('post.sort/f'),
                'id'   => input('post.id/d'),
            ];
            $validate = $this->validate($data,'Category.sort');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $data['sort'] = (int)$data['sort'];
            $result = model('Group')->where($this->mini_program)->where(['id' => $data['id']])->update(['hao_people' => $data['sort']]);
            if($result){
                return json(['code'=>200,'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }
    }  

    /**
     * 商品栏目选择
     */
    public function ids_action(){
        if(request()->isAjax()){
            $ids = input('post.ids/s');
            if(empty($ids)){
                return json(['code'=>0,'msg'=>'请选择商品']);
            }
            $rel = model('Group')->ids_action($this->member_miniapp_id,$ids);
            if($rel){
                return json(['code'=>200,'msg'=>'操作成功','data'=>[]]);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }
    }
}