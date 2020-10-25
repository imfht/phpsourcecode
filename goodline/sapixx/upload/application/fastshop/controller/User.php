<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户管理
 */
namespace app\fastshop\controller;
use app\common\controller\Manage;
use app\common\model\SystemUserLevel;
use think\facade\Validate;

class User extends Manage
{

    public function initialize()
    {
        parent::initialize();
        if(!model('auth')->getAuth($this->user->id,4)){
            $this->error('无权限,你非【订单管理员】');
        }
        $this->assign('pathMaps', [['name'=>'用户管理','url'=>'javascript:;']]);
    }


   /**
     * 会员列表
     */
    public function index($types = 0){ 
        $keyword = trim(input('get.keyword','','htmlspecialchars'));
        $condition['is_lock'] = $types ? 1 : 0;
        $condition['member_miniapp_id'] = $this->member_miniapp_id;
        $search = [];
        if(!empty($keyword)){
            if(Validate::mobile($keyword)){
                $search['phone_uid'] = $keyword;   
            }else{
                $search[] = ["nickname","like","%{$keyword}%"]; 
            }  
        }
        $view['keyword'] = input('get.keyword');
        $view['list']    = model('SystemUser')->where($condition)->where($search)->order('id desc')->paginate(20,false,['query'=>['types'=>$types]]);
        $view['types']   = $types;
        return view()->assign($view);   
    }


   /**
     * 会员列表
     */
    public function select(){ 
        if(request()->isAjax()){
            $ids = input('post.ids/s');
            if(empty($ids)){
                return json(['code'=>0,'msg'=>'请选择要添加代理的用户']);
            }
            $result = model('Agent')->add($this->member_miniapp_id,ids($ids,true));
            if($result){
                return json(['code'=>302,'msg'=>'代理用户添加成功','data' =>[]]);
            }else{
                return json(['code'=>0,'msg'=>'代理用户添加操作失败']);
            }
        }else{
            $keyword = trim(input('get.keyword','','htmlspecialchars'));
            $condition['is_lock'] = 0;
            $condition['member_miniapp_id'] = $this->member_miniapp_id;
            if(!empty($keyword)){
                $condition['phone_uid'] = $keyword;     
            }
            $view['keyword']   = input('get.keyword');
            $view['list']      = model('Agent')->selects($condition);
            return view()->assign($view);   
        }
    }

   /**
     * 代理管理
     */
    public function agent(){ 
        $keyword = trim(input('get.keyword','','htmlspecialchars'));
        $condition['fastshop_agent.member_miniapp_id'] = $this->member_miniapp_id;
        if(!empty($keyword)){
            $condition['system_user.phone_uid'] = $keyword;
        }
        $view['keyword']   = input('get.keyword');
        $view['list']    = model('Agent')->lists($condition);
        return view()->assign($view);   
    }

    /**
     * 编辑用户
     * @access public
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'           => input('post.id/d'),
                'password'     => input('post.password/s'),
                'safepassword' => input('post.safepassword/s'),
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
            $result  = model('SystemUser')->edit($updata,$data['id']);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url' => url('user/index')]);
            }
        }else{
            $id = input('id/d');
            $view['info'] = model('SystemUser')->get(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id]);
            if(!$view['info']){
                return $this->error("404 NOT FOUND");
            } 
            return view('edit',$view);
        }
    }

    /**
     * 编辑用户
     * @access public
     */
    public function agentedit(){
        if(request()->isAjax()){
            $data = [
                'id'     => input('post.id/d'),
                'rebate' => input('post.rebate/d'),
            ];
            $result  = model('Agent')->where(['member_miniapp_id' => $this->member_miniapp_id,'id' => $data['id']])->update(['rebate' =>$data['rebate']]);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url' => url('user/agent')]);
            }
        }else{
            $id = input('id/d');
            $view['agent'] = model('Agent')->get(['member_miniapp_id' => $this->member_miniapp_id,'id' => $id]);
            if(!$view['agent']){
                return $this->error("404 NOT FOUND");
            } 
            $view['info'] = model('SystemUser')->get(['member_miniapp_id' => $this->member_miniapp_id,'id' =>  $view['agent']['user_id']]);
            $view['user_number'] = model('SystemUserLevel')->where(['parent_id' => $view['agent']['user_id']])->count();
            return view()->assign($view);
        }
    }

    /**
     * 锁定
     * @param integer $id 用户ID
     */
    public function islock(int $id){
        $result = model('SystemUser')->lock($id);
        if(!$result){
            return json(['code'=>0,'message'=>'操作失败']);
        }else{
            return json(['code'=>200,'message'=>'操作成功']);
        }
    }
 
    //删除
    public function agentdelete(){
        $id = input('get.id/d');
        $result = model('Agent')->where(['member_miniapp_id' => $this->member_miniapp_id,'id' =>$id])->delete();
        if($result){
            return json(['code'=>200,'msg'=>'操作成功']);
        }else{
            return json(['code'=>403,'msg'=>'删除失败']);
        } 
    }

   /**
     * 伞下
     */
    public function pyramid(int $id){
        $view['pathMaps']   = [['name'=>'用户管理','url'=>url('passport.user/index')],['name'=>'伞下用户','url'=>'javascript:;']];
        $view['uid']        = $id;
        $view['people_num'] = SystemUserLevel::where(['parent_id' => $id])->count();
        $config = model('Config')->where(['member_miniapp_id' => $this->member_miniapp_id])->find(); 
        $allmoney = 0;
        if($config->reward_types == 1){
            $uid = SystemUserLevel::where(['parent_id' => $id])->column('user_id');
            $uid[] = $id;
            $allmoney = Model('BankAll')->where(['uid' => $uid])->sum('account');
        }
        $view['config']   =  $config;
        $view['allmoney'] =  $allmoney;
        return view()->assign($view);
    }
}