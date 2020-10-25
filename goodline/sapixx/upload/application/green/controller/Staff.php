<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 回收人员
 */
namespace app\green\controller;
use app\green\model\GreenStaff;

class Staff extends Common{


    public function initialize(){
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'回收人员','url'=>url("staff/index")]]);
    }

    /**
     * 列表
     */
    public function index(){
        $view['lists'] = GreenStaff::where(['member_miniapp_id' => $this->member_miniapp_id])->order('sort desc,id desc')->paginate(20);
        return view()->assign($view);
    }

    /**
     * 编辑或添加
     * @return void
     */
    public function edit(){
        if(request()->isAjax()){
            $id = $this->request->param('id/d');
            $data = [
                'uid'               => $this->request->param('uid/d'),
                'title'             => $this->request->param('title/s'),
                'about'             => $this->request->param('about/s'),
                'operate_id'        => $this->founder ?  $this->request->param('operate_id/d') : $this->operate_id,
                'member_miniapp_id' => $this->member_miniapp_id,
                'update_time'       => time(),
            ];
            $validate = $this->validate($data,'Staff.edit');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            if($id > 0){
                $data['id'] = $id;
                $result = GreenStaff::update($data);
            }else{
                $data['create_time'] = time();
                $result = GreenStaff::create($data);
            }
            if($result){
                return enjson(200,'操作成功');
            }else{
                return enjson(0);
            }
        }else{
            $view['info']  = GreenStaff::where(['id' => $this->request->param('id/d',0),'member_miniapp_id' => $this->member_miniapp_id])->find();
            return view()->assign($view);
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
            $validate = $this->validate($data,'Staff.sort');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            $result = GreenStaff::update(['sort'=>$data['sort']],['id' => $data['id']]);
            if($result){
                return enjson(200);
            }else{
                return enjson(0);
            }
        }
    }

    //删除
    public function delete(int $id){
        $result = GreenStaff::destroy($id);
        if($result){
            return enjson(200);
        }else{
            return enjson(403,'删除失败');
        }
    }
}