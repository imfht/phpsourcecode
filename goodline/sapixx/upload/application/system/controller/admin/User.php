<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 管理员
 */
namespace app\system\controller\admin;
use app\common\controller\Admin;
use app\common\model\SystemAdmin;
use app\common\event\Admin as AdminUser;
use think\facade\Request;

class User extends Admin{

    protected $login;
    
    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps',[['name'=>'管理员','url'=>url("system/admin.user/index")]]);
        $this->login = AdminUser::getLoginSession();
    }

    /**
     * 列表
     * @access public
     */
    public function index(){
        $view['list']  = SystemAdmin::order('id desc')->paginate(10,true);
        return view()->assign($view);
    }

    /**
     * 添加
     * @access public
     */
    public function add(){
        if(request()->isAjax()){
            $data = [
                'token'            => $this->request->param('__token__/s'),
                'username'         => $this->request->param('username/s'),
                'password'         => $this->request->param('password/s'),
                'password_confirm' => $this->request->param('repassword/s'),
                'about'            => $this->request->param('about/s'),
            ];
            $validate = $this->validate($data,'Admin.add');
            if(true !== $validate){
                return json(['code'=>1,'msg'=>$validate]);
            }
            $result  = SystemAdmin::updateUser($data);
            if(!$result){
                return json(['code'=>1,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/admin.user/index')]);
            }
        }else{
            return view();
        }
    }

    /**
     * 编辑用户
     * @access public
     */
    public function edit(){
        if(request()->isAjax()){
            $data = [
                'id'               => $this->request->param('id/d'),
                'username'         => $this->request->param('username/s'),
                'password'         => $this->request->param('password/s'),
                'password_confirm' => $this->request->param('repassword/s'),
                'about'            => $this->request->param('about/s'),
            ];
            $validate = $this->validate($data,'Admin.edit');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemAdmin::updateUser($data);
            if(!$result){
                return json(['code'=>0,'msg'=>'操作失败']);
            }else{
                return json(['code'=>200,'msg'=>'操作成功','url'=>url('system/admin.user/index')]);
            }
        }else{
            $id = Request::param('id/d');
            $view['info'] = SystemAdmin::where(['id' => $id])->find();;
            if(empty($view['info'])){
                return $this->error("404 NOT FOUND");
            }

            return view()->assign($view);
        }
    }

    /**
     * [删除]
     * @access public
     * @return bool
     */
    public function delete(){
        $id    = $this->request->param('id/d');
        if($id == $this->login['admin_id']){
            return json(['code' => 0,'msg' => lang('lock_user')]);
        }
        $result  = SystemAdmin::destroy($id);
        if(!$result){
            return json(['code' => 0,'msg'=>'操作失败']);
        }else{
            return json(['code' =>200,'msg'=>'操作成功']);
        }
    }

    /**
     * 修改密码
     * @access public
     */
    public function password(){
        if(request()->isAjax()){
            $data = [
                'password'         => $this->request->param('password/s'),
                'password_confirm' => $this->request->param('repassword/s'),
                'about'            => $this->request->param('about/s'),
                'login'            => $this->login
            ];
            $validate = $this->validate($data,'Admin.password');
            if(true !== $validate){
                return json(['code'=>0,'msg'=>$validate]);
            }
            $result  = SystemAdmin::upDatePasspowrd($data);
            if(!$result){
                return json(['code'=>1,'msg'=>'操作失败']);
            }else{
                return json(['code'=>0,'msg'=>'操作成功','url'=>url('system/admin.index/logout')]);
            }
        }else{
            $view['info'] = SystemAdmin::where(['id' => $this->login['admin_id']])->find();
            return view()->assign($view);
        }
    } 

    /**
     * 用户重复
     * @param integer $id
     * @return void
     */
    public function isPass(){
        $condition[] = ['username','=',$this->request->param('param/s')];
        $condition[] = ['id','<>',$this->request->param('id/d',0)];
        $result = SystemAdmin::where($condition)->count();
        if($result){
            return json(['status'=>'n','info'=>'用户名重复']);
        }else{
            return json(['status'=>'y','info'=>'可以使用']);
        }
    }
}