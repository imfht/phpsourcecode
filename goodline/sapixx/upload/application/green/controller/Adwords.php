<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 图标管理
 */
namespace app\green\controller;
use app\green\model\GreenAdwords;
use app\green\model\GreenCategory;
use think\facade\Request;

class Adwords extends Common{

    public function initialize() {
        parent::initialize();
        $this->ads  = new GreenAdwords();
        $this->assign('pathMaps', [['name'=>'图标管理','url'=>url("adwords/index")]]);
    }

    /**
     * 列表
     */
    public function index($group){
        $condition          = [];
        $condition['group'] = $group;
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $view['lists'] = $this->ads->where($condition)->order('sort desc,id desc')->paginate(20);
        $view['pathMaps']   = [['name'=>'广告管理','url'=>url("adwords/index",['group' => $group])]];
        $view['group'] = $group;
        return view('index',$view);
    }
    
    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'group'      => input('post.group/s', '', 'htmlspecialchars'),
                'open_type'  => input('post.open_type/s', '', 'htmlspecialchars'),
                'title'      => input('post.title/s', '', 'htmlspecialchars'),
                'link'       => input('post.link/s', '', 'htmlspecialchars'),
                'picture'    => input('post.picture/s', '', 'htmlspecialchars'),
                'member_miniapp_id' => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'Adwords.add');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  $this->ads->edit($data);
            if($result){
                return enjson(200,'操作成功',['url'=>url('adwords/index',['group' => $data['group']])]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $view['lists']     = GreenCategory::where(['member_miniapp_id' => $this->member_miniapp_id,'parent_id' => 0])->select();
            $view['group'] = Request::param('group');
            return view()->assign($view);
        }
    }

    //编辑
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'                => input('post.id/s'),
                'group'             => input('post.group/s'),
                'open_type'         => input('post.open_type/s','','htmlspecialchars'),
                'title'             => input('post.title/s'),
                'link'              => input('post.link/s'),
                'picture'           => input('post.picture/s'),
                'member_miniapp_id' => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'adwords.edit');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result =  $this->ads->edit($data);
            if($result){
                return enjson(200,'操作成功',['url'=>url('adwords/index',['group' => $data['group']])]);
            }else{
                return enjson(0,'操作失败');
            }
        }else{
            $id = input('get.id/d');
            $view['info'] = $this->ads->get($id);
            $view['lists']     = GreenCategory::where(['member_miniapp_id' => $this->member_miniapp_id,'parent_id' => 0])->select();
            return view()->assign($view);
        }
    }

    //删除
    public function delete(){
        $id              = input('get.id/d');
        $condition       = [];
        $condition['id'] = $id;
        $result = $this->ads->where($this->mini_program)->where($condition)->delete();
        if($result){
            return enjson(200,'操作成功');
        }else{
            return enjson(403,'操作失败');
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
                return enjson(0,$validate);
            }
            $result = $this->ads->save(['sort'=>$data['sort']],['member_miniapp_id' => $this->member_miniapp_id,'id' =>$data['id']]);
            if($result){
                return enjson(200,'操作成功');
            }else{
                return enjson(0,'操作失败');
            }
        }
    }
}