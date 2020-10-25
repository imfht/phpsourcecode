<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 抢购时间管理
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;

class Times extends Manage{

    public function initialize() {
        parent::initialize();
        if(!model('auth')->getAuth($this->user->id,1)){
            $this->error('无权限,你非【内容管理员】');
        }
        $this->assign('pathMaps',[['name'=>'抢购时段管理','url'=>url("fastshop/times/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['lists'] = model('Times')->where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->paginate(20); 
        return view()->assign($view);
    }
    
    /**
     * 添加
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'name'              => input('post.name/s'),
                'sort'              => input('post.sort/d'),
                'start_time'        => input('post.start_time/d'),
                'end_time'          => input('post.end_time/d'),
                'member_miniapp_id' => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'Times.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  model('Times')->edit($data);
            if($result){
                return json(['code'=>200,'url'=>url('times/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            return view();
        }
    }

    //编辑
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'                => input('post.id/s'),
                'name'              => input('post.name/s'),
                'sort'              => input('post.sort/d'),
                'start_time'        => input('post.start_time/d'),
                'end_time'          => input('post.end_time/d'),
                'member_miniapp_id' => $this->member_miniapp_id,
            ];
            $validate = $this->validate($data,'Times.save');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result =  model('Times')->edit($data);
            if($result){
                return json(['code'=>200,'url'=>url('times/index'),'msg'=>'操作成功']);
            }else{
                return json(['code'=>0,'msg'=>'操作失败']);
            }
        }else{
            $id   = input('get.id/d');
            $view['info']  = model('Times')->get($id);
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
            $validate = $this->validate($data,'Times.sort');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result = model('Times')->save(['sort'=>$data['sort']],['id' => $data['id']]);
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
        $sale =  model('Sale')->get(['category_id' => $id]);
        if($sale){
            return json(['code'=>403,'msg'=>'删除失败,其中还有活动']);
        }
        $result = model('Times')->destroy(['id' => $id,'member_miniapp_id' => $this->member_miniapp_id]);
        if($result){
            return json(['code'=>200,'msg'=>'操作成功']);
        }else{
            return json(['code'=>403,'msg'=>'删除失败']);
        } 
    } 
}