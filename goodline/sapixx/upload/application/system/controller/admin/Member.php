<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户管理
 */
namespace app\system\controller\admin;
use app\common\controller\Admin;
use app\common\model\SystemMember;
use app\common\model\SystemMemberMiniapp;
use app\common\model\SystemMemberBank;
use app\common\model\SystemMemberBankBill;
use app\common\event\Passport;
use think\facade\Request;

class Member extends Admin{

    public function initialize(){
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'用户管理','url'=>url("system/admin.member/index")]]);
    }

    /**
     * 会员列表
     */
    public function index($types = 0){ 
        $keyword = $this->request->param('keyword');
        $condition['is_lock']   = $types ? 1 : 0;
        $condition['parent_id'] = 0;
        if(!empty($keyword)){
            $condition['phone_id'] = $keyword;     
        }
        $view['keyword']     = $this->request->param('keyword');
        $view['list']        = SystemMember::where($condition)->order('id desc')->paginate(20,false,['query'=>['types'=>$types]]);
        $view['money']       = SystemMemberBank::sum('money');
        $view['lack_money']  = SystemMemberBank::sum('lack_money');
        $view['consume']     = SystemMemberBankBill::where(['state' => 1])->sum('money');
        $view['types'] = $types;
        return view()->assign($view);   
    }

    /**
     * 用户列表
     */
    public function select(){
        $keyword = $this->request->param('keyword');
        $condition['is_lock'] = 0;
        if(!empty($keyword)){
            $condition['phone_id'] = $keyword;     
        }
        $view['keyword'] = $this->request->param('keyword');
        $view['input']   = $this->request->param('input');
        $view['list']    = SystemMember::where($condition)->order('id desc')->paginate(20);
        return view()->assign($view);   
    }    

    /**
     * 会员列表
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'username'         => $this->request->param('username/s'),
                'login_password'   => $this->request->param('password/s'),
                'safe_password'    => $this->request->param('safe_password/s'),
                'phone_id'         => $this->request->param('phone/d'),
                'lock_config'      => 0,
            ];
            $validate = $this->validate($data,'Member.add');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //判断手机号是否重复
            $info = SystemMember::where(['phone_id' => $data['phone_id']])->find();
            if(!empty($info)){
                return json(['code'=>0,'msg'=>'注册手机账号已存在']);
            }
            $result  = SystemMember::edit($data);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/admin.member/index')]);
            }
        }else{
            return view();   
        }
    }

    /**
     * 编辑用户
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'                  => $this->request->param('id/d'),
                'username'            => $this->request->param('username/s'),
                'phone_id'            => $this->request->param('phone/d'),
                'edit_login_password' => $this->request->param('password/s'),
                'edit_safe_password'  => $this->request->param('safe_password/s'),
                'lock_config'         => $this->request->param('lock_config/d',0),
            ];
            $validate = $this->validate($data,'Member.edit');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $data['login_password'] = $data['edit_login_password'];
            $data['safe_password']  = $data['edit_safe_password'];
            $result  = SystemMember::edit($data);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/admin.member/index')]);
            }
        }else{
            $id   = $this->request->param('id/d');
            $info = SystemMember::where(['id'=>$id])->find();
            if(!$info){
                return $this->error("404 NOT FOUND");
            }
            $view['info']  = $info;
            return view()->assign($view);
        }
    }

    /**
     * 检测手机号是否重复
     */
    public function isphone(){
        $userid = $this->request->param('id/d');
        $value  = $this->request->param('param/s');
        if($userid){
            $result = SystemMember::where('id','<>',$userid)->where(['phone_id' => $value])->find();
        }else{
            $result = SystemMember::where(['phone_id' => $value])->find();
        }
        if($result){
            return json(['status'=>'n','info'=>'手机号已存在']);
        }else{
            return json(['status'=>'y','info'=>'可以使用']);
        }
    } 
    
    /**
     * 锁定
     * @param integer $id 用户ID
     */
    public function islock(int $id){
        $result = SystemMember::lock($id);
        if(!$result){
            return json(['code'=>0,'message'=>'操作失败']);
        }else{
            return json(['code'=>200,'message'=>'操作成功']);
        }
    }

    /**
     * 管理中心
     * @param integer $id 用户ID
     */
    public function manage(){
        $uid = Request::param('uid/d',0);
        if($uid){
            $condition['member_id'] = $uid;
        }
        $id = Request::param('id/d',0);
        if($id){
            $condition['id'] = $id;
        }
        if(empty($condition)){
            return $this->error("参数不能为空");
        }
        Passport::clearMiniapp();
        Passport::setlogout();
        if($id){
            $rel = SystemMemberMiniapp::where($condition)->find();
            if(empty($rel)){
                return $this->error("当前用户未开通任何应用");
            }
            $uid = $rel->member_id;
            if($rel['is_lock'] == 0){
                Passport::setMiniapp(['member_id' => $rel['member_id'],'miniapp_id' => $rel['miniapp_id'],'member_miniapp_id' => $rel['id']]);
            }
        }
        $member = SystemMember::where(['id' => $uid])->find();
        if(empty($member) || $member['is_lock'] == 1){
            Passport::clearMiniapp();
            $this->error("帐号已被锁定,禁止管理");
        }
        Passport::setLogin($member);
        $this->redirect(url('system/passport.index/index'),302);
    }

    /**
     * 用户账单
     * @return void
     */
    public function bill(){
        $uid = Request::param('uid/d',0);
        $view['bank']    = SystemMemberBank::where(['member_id' =>$uid])->find();
        $view['consume'] = SystemMemberBankBill::where(['member_id' => $uid,'state' => 1])->sum('money');
        $view['list']    = SystemMemberBankBill::where(['member_id' => $uid])->order('update_time desc')->paginate(20,false,['query'=>['uid' => $uid]]);
        $view['pathMaps'] = [['name'=>'用户管理','url'=>'javascript:;'],['name'=>'财务管理','url'=>'javascript:;']];
        return view()->assign($view);
    }
}
