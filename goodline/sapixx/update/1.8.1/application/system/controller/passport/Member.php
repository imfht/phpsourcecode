<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 帐号管理
 */
namespace app\system\controller\passport;
use app\common\model\SystemMember;
use app\common\model\SystemMemberBank;
use app\common\facade\Alisms;

class Member extends Common{

    public function initialize() {
        parent::initialize();
        if($this->user->parent_id){
            $this->error('仅创始人有权限访问');
        }
        if($this->member_miniapp_id){
            $pathMaps[] = ['name'=>$this->member_miniapp->appname,'url'=>'javascript:;'];
        }
        $pathMaps[] = ['name'=>'帐号管理','url'=>'javascript:;'];
        $this->assign('pathMaps',$pathMaps);
    }

    /**
     * 我的帐号信息
     */
    public function index(){
        $bank = SystemMemberBank::where(['member_id' => $this->user->id])->find();
        if(empty($bank)){
            $bank['money']      = money(0);
            $bank['lack_money'] = money(0);
        }
        $view['bank'] = $bank;
        return view()->assign($view);
    }

    /**
     * 修改登录手机号
     */
    public function phone(){
        if(request()->isPost()){
            $data = [
                'id'             => $this->user->id,
                'phone_id'       => $this->request->param('phone_id/s'),
                'sms_code'       => $this->request->param('sms_code/s'),
                'login_password' => $this->request->param('safepassword/s'),
            ];
            $validate = $this->validate($data,'Member.updatephone');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            //判断验证码
            if(!Alisms::isSms($data['phone_id'],$data['sms_code'])){
                return enjson(0,'验证码错误');
            }
            //验证安全密码
            if(!password_verify(md5($data['login_password']),$this->user->safe_password)) {
                return enjson(0,'安全密码错误');
            }
            //验证码通过
            $result = SystemMember::editPhone($data);
            if($result){
                return enjson(200,'修改成功',['url' => url('system/passport.member/index')]);
            }else{
                return enjson(0,'修改失败');
            } 
        }else{
            return view();
        }
    }
    
    /**
     * 修改安全密码
     */
    public function safepassword(){
        if($this->user->lock_config){
            $this->error('你账户锁定配置权限');
        }
        if(request()->isPost()){
            $data = [
                'id'                   => $this->user->id,
                'login_password'       => $this->request->param('login_password/s'),
                'safepassword'         => $this->request->param('safepassword/s'),
                'safepassword_confirm' => $this->request->param('safepassword_confirm/s'),
            ];
            $validate = $this->validate($data,'Member.safepassword');
            if(true !== $validate){
                return enjson(0,$validate);
            }
            //验证密码
            if(!password_verify(md5($data['login_password']),$this->user->safe_password)) {
                return enjson(0,'安全密码错误');
            }
            //验证码通过
            $result = SystemMember::updateSafePasspord($this->user->id,$data['safepassword']);
            if($result){
                return enjson(200,'修改成功',['url' => url('system/passport.member/index')]);
            }else{
                return enjson(0,'修改失败');
            } 
        }else{
            return view();
        }
    }

    /**
     * 员工管理
     */
    public function staff(){
        $view['list'] = SystemMember::where(['parent_id' => $this->user->id])->order('id desc')->paginate(20);
        return view()->assign($view);
    }

    /**
     * 添加员工
     */
    public function staffAdd(){
        if(request()->isPost()){
            $data = [
                'user_id'        => $this->user->id,
                'miniapp_id'     => $this->request->param('bind_miniapp_id/d'),
                'username'       => $this->request->param('username/s'),
                'phone_id'       => $this->request->param('phone_id/d'),
                'login_password' => $this->request->param('login_password/s'),
            ];
            $validate = $this->validate($data,'Member.bindapp');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //判断手机号是否重复
            $info = SystemMember::where(['phone_id' => $data['phone_id']])->find();
            if(!empty($info)){
                return json(['code'=>0,'msg'=>'手机账号已存在']);
            }
            $result = SystemMember::bindEdit($data);
            if($result){
                return json(['code'=>200,'msg'=>'修改成功','url' => url('passport.member/staff')]);
            }else{
                return json(['code'=>0,'msg'=>'修改失败']);
            } 
        }else{
            return view();
        }
    }
    
    /**
     * 添加员工
     */
    public function staffEdit(){
        if(request()->isPost()){
            $data = [
                'user_id'        => $this->user->id,
                'id'             => $this->request->param('id/d'),
                'miniapp_id'     => $this->request->param('bind_miniapp_id/d'),
                'username'       => $this->request->param('username/s'),
                'phone_id'       => $this->request->param('phone_id/d'),
                'login_password' => $this->request->param('login_password/s'),
            ];
            $validate = $this->validate($data,'Member.bindapp');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            //判断手机号是否重复
            $info = SystemMember::where(['phone_id' => $data['phone_id']])->where('id','<>',$data['id'])->find();
            if(!empty($info)){
                return json(['code'=>0,'msg'=>'手机账号已存在']);
            }
            $result = SystemMember::bindEdit($data);
            if($result){
                return json(['code'=>200,'msg'=>'修改成功','url' => url('passport.member/staff')]);
            }else{
                return json(['code'=>0,'msg'=>'修改失败']);
            } 
        }else{
            $id   = $this->request->param('id/d');
            $info = SystemMember::where(['parent_id' => $this->user->id,'id' => $id])->find();
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
    public function staffLock(int $id){
        $result = SystemMember::lock($id);
        if(!$result){
            return json(['code'=>0,'message'=>'操作失败']);
        }else{
            return json(['code'=>200,'message'=>'操作成功']);
        }
    }

    /**
     * [删除]
     * @access public
     * @return bool
     */
    public function staffDelete(){
        $id      = $this->request->param('id/d');
        $result  = SystemMember::where(['parent_id' => $this->user->id,'id' => $id])->delete();
        if(!$result){
            return json(['code' => 0,'msg'=>'操作失败']);
        }else{
            return json(['code' =>200,'msg'=>'操作成功']);
        }
    }

    /**
     * 获取登录/找回密码等验证码
     */
    public function getLoginSms(){
        if(request()->isPost()){
            $data = [
                'phone_id' => $this->request->param('phone/s')
            ];
            $validate = $this->validate($data,'Sms.getsms');
            if(true !== $validate){
                return json(['code'=>0,'message'=>$validate]);
            }
            //判断是否登录
            if($data['phone_id'] != $this->user->phone_id){
                return json(['code'=>0,'message'=>"请输入正确的手机号"]);
            }
            $user  = SystemMember::where(['phone_id' => $this->user->phone_id])->find();
            if(empty($user)) {
                return json(['code'=>0,'message'=>"用户不存在"]);
            }
            $sms = Alisms::putSms($this->user->phone_id);
            return json($sms);
        }else{
            return $this->error("404 NOT FOUND");
        }
    }
}