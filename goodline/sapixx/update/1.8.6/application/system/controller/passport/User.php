<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户管理
 */
namespace app\system\controller\passport;
use app\common\controller\Manage;
use app\common\model\SystemUser;
use app\common\model\SystemUserLevel;
use think\facade\Validate;
use think\facade\Request;

class User extends Manage
{

    public function initialize(){
        parent::initialize();
        if(!$this->member_miniapp_id){
            $this->error('未找到所属应用,请先开通应用。');
        }
        $this->assign('pathMaps', [['name'=>$this->member_miniapp->appname,'url'=>'javascript:;'],['name'=>'用户管理','url'=>'javascript:;']]);
    }

   /**
     * 会员列表
     */
    public function index($types = 0){ 
        $keyword = Request::param('keyword/s');
        $condition['is_lock']   = $types ? 1 : 0;
        $condition['is_delete'] = 0;
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $search = [];
        if(!empty($keyword)){
            if(Validate::isMobile($keyword)){
                $condition['phone_uid'] = $keyword;
            }else{
                $search[] = ['nickname','like','%'.$keyword.'%'];
            }
        }
        $view['keyword'] = $keyword;
        $view['list']    = SystemUser::where($condition)->where($search)->order('id desc')->paginate(20,false,['query'=>['keyword' => $keyword,'types' => $types]]);
        $view['types']   = $types;
        return view()->assign($view);   
    }

    /**
     * 邀请关系
     */
    public function level(int $id){
        $user = SystemUser::field('id,nickname,face,phone_uid,invite_code,create_time')->where(['id' => $id])->find();
        if(!$user){
            return $this->error("404 NOT FOUND");
        }
        $data['nickname']    = $user['nickname'];
        $data['invite_code'] = $user['invite_code'];
        $data['phone_uid']   = $user['phone_uid'];
        $data['face']        = $user['face'];
        $data['level']       = 0;
        $view['user']        = $data;
        $view['level']       = SystemUserLevel::children_user($id);
        return view()->assign($view);
    }

    /**
     * 伞下
     */
    public function pyramid(int $id){
        $view['pathMaps']   = [['name'=>'用户管理','url'=>url('passport.user/index')],['name'=>'伞下用户','url'=>'javascript:;']];
        $view['uid']        = $id;
        $view['people_num'] = SystemUserLevel::where(['parent_id' => $id])->count();
        return view()->assign($view);
    }
    /**
     * 目录树
     * @param integer $uid
     * @return void
     */
    public function ztree(int $uid){
        if (request()->isAjax()) {
            $id = $this->request->param('id/d',0);
            $parent_uid =  $id ? $id : $uid;
            $info = SystemUserLevel::pyramid($parent_uid);
            if(empty($info)){
                $info = [['id' => $uid,'name'=>'无用户']];
            }
            return json($info);
        }
    }

    /**
     * 选择所属用户
     */
    public function selectUser(){
        $keyword = Request::param('keyword/s');
        $input   = Request::param('input/s');
        $condition = [];
        $condition[] =  ['member_miniapp_id','=',$this->member_miniapp_id];
        if(!empty($keyword)){
            if(Validate::isMobile($keyword)){
                $condition[] = ['phone_uid','=',$keyword];
            }else{
                $condition[] = ['nickname','like','%'.$keyword.'%'];
            }
        }
        $view['list'] = SystemUser::where($condition)->order('id desc')->paginate(10,false,['query'=>['keyword' => $keyword,'input' => $input]]);
        $view['keyword'] = $keyword;
        $view['input']   = $input;
        $view['id']      = $this->member_miniapp_id;
        return view()->assign($view);
    }

    /**
     * 用户预览
     */
    public function review(int $uid){
        $view['user'] = SystemUser::where(['id' => $uid])->find();
        return view()->assign($view);
    }

    /**
     * 编辑用户
     * @access public
     */
    public function edit(){
        if($this->user->parent_id){
            $this->error('仅创始人有权限访问.');
        }
        if(request()->isAjax()){
            $data = [
                'id'           => Request::param('id/d'),
                'password'     => Request::param('password/s'),
                'safepassword' => Request::param('safepassword/s'),
            ];
            $updata = [];
            if ($data['safepassword']) {
                $validate = $this->validate($data, 'User.safepassword');
                if (true !== $validate) {
                    return json(['code'=>0,'msg'=>$validate]);
                }
                $updata['safe_password'] = password_hash(md5($data['safepassword']),PASSWORD_DEFAULT);
            }
            if($data['password']){
                $updata['password'] = password_hash(md5($data['password']),PASSWORD_DEFAULT);
            }
            if(empty($data['password']) && empty($data['password'])){
                return json(['code'=>0,'msg'=>'操作失败']);
            }
            $result  = SystemUser::edit($updata,$data['id']);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url' => url('passport.user/index')]);
            }
        }else{
            $id = $this->request->param('id/d');
            $view['info'] = SystemUser::where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id])->find();
            if(!$view['info']){
                return $this->error("404 NOT FOUND");
            } 
            return view('edit',$view);
        }
    }

    /**
     * 锁定
     * @param integer $id 用户ID
     */
    public function islock(int $id){
        if($this->user->parent_id){
            return json(['code'=>0,'msg'=>'仅创始人有权限访问.']);
        }
        $result = SystemUser::lock($this->member_miniapp_id,$id);
        if(!$result){
            return json(['code'=>0,'message'=>'操作失败']);
        }else{
            return json(['code'=>200,'message'=>'操作成功']);
        }
    }

    /**
     * 作废
     * @param integer $id 用户ID
     */
    public function delete(int $id){
        if($this->user->parent_id){
            return json(['code'=>0,'msg'=>'仅创始人有权限访问.']);
        }
        $result = SystemUser::isDelete($this->member_miniapp_id,$id);
        if(!$result){
            return json(['code'=>0,'message'=>'操作失败']);
        }else{
            return json(['code'=>200,'message'=>'操作成功']);
        }
    }    
}