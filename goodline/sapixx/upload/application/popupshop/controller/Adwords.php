<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      Pillar<ltmn@qq.com>
 * 广告位管理
 */
namespace app\popupshop\controller;
use app\common\controller\Manage;

class Adwords extends Manage{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'广告管理','url'=>'javascript:;']]);
    }

    /**
     * 列表
     */
    public function index(){
        $group = input('get.group/d');
        $view['lists'] = model('adwords')->where(['group_id'=>$group,'member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->paginate(20);
        $view['group'] = $group;
        return view('index',$view);
    }
    
    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'group_id'          => input('post.group_id/d'),
                'open_type'         => input('post.open_type/s','','htmlspecialchars'),
                'title'             => input('post.title/s','','htmlspecialchars'),
                'link'              => input('post.link/s','','htmlspecialchars'),
                'picture'           => input('post.picture/s','','htmlspecialchars'),
                'member_miniapp_id' => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'adwords.add');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  model('adwords')->edit($data);
            if($result){
                return json(['code'=>200,'url'=>url('popupshop/adwords/index',['group'=>$data['group_id']]),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $view['group_id'] = input('get.group/d');
            return view()->assign($view);
        }
    }

    //编辑
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'                => input('post.id/d'),
                'group_id'          => input('post.group_id/s','','htmlspecialchars'),
                'open_type'         => input('post.open_type/s','','htmlspecialchars'),
                'title'             => input('post.title/s','','htmlspecialchars'),
                'link'              => input('post.link/s','','htmlspecialchars'),
                'picture'           => input('post.picture/s','','htmlspecialchars'),
                'member_miniapp_id' => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'adwords.edit');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  model('adwords')->edit($data);
            if($result){
                return json(['code'=>200,'url'=>url('popupshop/adwords/index',['group'=>$data['group_id']]),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $id = input('get.id/d');
            $view['info'] = model('adwords')->get($id);
            return view()->assign($view);
        }
    }

    //删除
    public function delete(){
        $id = input('get.id/d');
        $result = model('adwords')->where(['member_miniapp_id' => $this->member_miniapp_id,'id' =>$id])->delete();
        if($result){
            return json(['code'=>200,'msg'=>'操作成功']);
        }else{
            return json(['code'=>403,'msg'=>'删除失败']);
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
            $validate = $this->validate($data,'adwords.sort');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result = model('adwords')->save(['sort'=>$data['sort']],['member_miniapp_id' => $this->member_miniapp_id,'id' =>$data['id']]);
            if($result){
                return json(['code'=>200,'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }
    }
}