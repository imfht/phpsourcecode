<?php
/**
 * Created by PhpStorm.
 * User: jswei
 * Date: 2018/5/8
 * Time: 17:15
 */

namespace app\admin\controller;

use app\admin\validate\Admin as validateAdmin;

class User extends Base {
    public function __construct(){
        parent::__construct();
    }
    protected $sinkMethods=['index'];

    /**
     * @route('admin/login','any')
     *  ->allowCrossDomain()
     * @return \think\Response
     * @throws \Firebase\Token\TokenException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index(){
        $admin = new validateAdmin;
        $data = request()->param();
        unset($data['pass']);
        if(!$admin->scene('login')->check($data)){
             $this->__e('',[
                'status'=>0,
                'msg'=>$admin->getError()
            ]);
        }
        $admin = new \app\admin\model\Admin;
        $_admin = $admin::where('username','eq',$data['username'])->find();
        if(!$_admin){
             $this->__e('',['status'=>0,'msg'=>'账号不存在']);
        }
        $flag = $this->_password($data['password'],1)==$_admin['password'];
        if(!$flag){
            $this->__e('',['status'=>0,'msg'=>'密码错误']);
        }
        if($_admin['status']!=0){
            $this->__e('',['status'=>0,'msg'=>'账号已被锁定']);
        }
        $token = $this->_token($_admin['id']);
        unset($_admin['password']);
        unset($_admin['hash']);
        unset($_admin['update_time']);
        $location = \itbdw\Ip\IpLocation::getLocation(request()->ip());
        $address = "";
        if($location['country']){
            $address .= ",{$location['country']}";
        }
        if($location['province']){
            $address .= ",{$location['province']}";
        }
        if($location['city']){
            $address .= ",{$location['city']}";
        }
        if($location['area']){
            $address .= ",{$location['area']}";
        }
        if($location['isp']){
            $address .= ",{$location['isp']}";
        }
        $_admin->last_ip = $location['ip'];
        $_admin->last_date = time();
        $_admin->last_address=substr($address,1);
        $_admin->save();
        $_admin['access_token'] = $token ? $token['access_token'] : '';
        $_admin['expires_in'] = $token ? $token['expires_in'] : '';
        unset($_admin['code']);
        return $this->__s('',$_admin);
    }

    /**
     * 获取信息
     *@route('admin/info','get')
     *  ->allowCrossDomain()
     * @return \think\Response
     * @throws \think\exception\DbException
     */
    public function info(){
        $admin = $this->getUser();
        if($admin['gid']==-1){
            $admin['roles'] = ['admin'];
        }else{
            $admin['roles'] = ['editor'];
        }
        return $this->__s('',$admin);
    }

    /**
     * @route('admin/save','post')
     *  ->allowCrossDomain()
     */
    public function save(){
        $admin = new validateAdmin;
        $data = request()->post();
        if(!$admin->scene('save')->check($data)){
            return [
                'status'=>0,
                'msg'=>$admin->getError()
            ];
        }
        $admin = db('admin')
            ->field('id')
            ->where('username','eq',$data['username'])
            ->find();
        if(!$admin){
            return [
                'status'=>0,
                'msg'=>'管理员不存在'
            ];
        }
        $pwd = $this->_password($data['password'],1);
        if(!db('admin')->update([
            'id'=>$admin['id'],
            'password'=>$pwd,
            'dates'=>time()
        ])){
            return [
                'status'=>0,
                'msg'=>'修改失败'
            ];
        }
        return [
            'status'=>1,
            'msg'=>'修改成功'
        ];
    }

    /**
     * 退出
     * @route('admin/logout','post')->allowCrossDomain()
     */
    public function logout(){
       $this->_removeToken();
       $this->__s('退出成功');
    }

}