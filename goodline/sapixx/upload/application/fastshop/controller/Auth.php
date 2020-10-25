<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 权限管理
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;
use app\common\model\SystemMember;

class Auth extends Manage
{

    public function initialize()
    {
        parent::initialize();
        if($this->user->parent_id){
            $this->error('无权限,你非【创始人】身份');
        }
        $this->assign('pathMaps', [['name'=>'员工管理','url'=>'javascript:;']]);
    }


    /**
     * 员工管理
     */
    public function index(){
        $view['list'] = SystemMember::where(['bind_member_miniapp_id' => $this->member_miniapp_id,'parent_id' => $this->user->id])->order('id desc')->paginate(20);
        return view()->assign($view);
    }

    /**
     * 权限编辑
     */
    public function edit(){
        if(request()->isPost()){
            $data = [
                'id'                => input('post.id/d','','htmlspecialchars'),
                'types'             => input('post.types/d','','htmlspecialchars'),
                'member_miniapp_id' => $this->member_miniapp_id
            ];
            $result = model('Auth')->edit($data);
            if($result){
                return json(['code'=>200,'msg'=>'修改成功','url' => url('auth/index')]);
            }else{
                return json(['code'=>0,'msg'=>'修改失败']);
            } 
        }else{
            $id   = input('id/d');
            $info = model('Member')->where(['bind_member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->find();
            if(!$info){
                return $this->error("404 NOT FOUND");
            }
            $view['info']  = $info;
            $view['auth'] = model('Auth')->where(['member_miniapp_id' => $this->member_miniapp_id,'member_id' => $id])->find();
            return view()->assign($view);
        }
    }
}