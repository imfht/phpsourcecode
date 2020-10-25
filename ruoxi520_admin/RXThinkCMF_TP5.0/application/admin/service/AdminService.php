<?php
// +----------------------------------------------------------------------
// | RXThink框架 [ RXThink ]
// +----------------------------------------------------------------------
// | 版权所有 2017~2019 南京RXThink工作室
// +----------------------------------------------------------------------
// | 官方网站: http://www.rxthink.cn
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * 管理人员-服务类
 * 
 * @author 牧羊人
 * @date 2018-12-10
 */
namespace app\admin\service;
use app\admin\model\AdminServiceModel;
use app\admin\model\AdminModel;
use think\captcha\Captcha;
use app\admin\model\AdminRmrModel;
use app\admin\model\AdminLogModel;
class AdminService extends AdminServiceModel {
    
    /**
     * 初始化模型
     * 
     * @author 牧羊人
     * @date 2018-12-10
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::initialize()
     */
    public function initialize()
    {
        parent::initialize();
        $this->model = new AdminModel();
        
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2019-02-14
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::getList()
     */
    function getList()
    {
        $param = input("request.");
        
        $map = [];
        //查询条件
        $keywords = trim($param['keywords']);
        if($keywords) {
            $map['num'] = array('like',"%{$keywords}%");
            $map['realname'] = array('like',"%{$keywords}%");
            $map['mobile'] = array('like',"%{$keywords}%");
            $map['_logic'] = 'OR';
        }
        
        return parent::getList($map);
    }
    
    /**
     * 添加或编辑
     * 
     * @author 牧羊人
     * @date 2019-02-24
     * (non-PHPdoc)
     * @see \app\admin\model\AdminServiceModel::edit()
     */
    function edit()
    {
        $data = input('post.', '', 'trim');
        $avatar = trim($data['avatar']);
        $username = trim($data['username']);
        $password = trim($data['password']);
        //字段验证
        if(!$data['id'] && !$avatar) {
            return message('请上传头像',false);
        }
        
        //数据处理
        if(strpos($avatar, "temp")) {
            $data['avatar'] = \Common::saveImage($avatar, 'admin');
        }
        //密码加密处理
        if($password) {
            $password = $this->password($password, $username);
            $data['password'] = $password;
        }else{
            unset($data['password']);
        }
        $data['entry_date'] = isset($data['entry_date']) ? strtotime($data['entry_date']) : 0;
        $data['status'] = (isset($data['status']) && $data['status']=="on") ? 1 : 2;
        $data['is_admin'] = (isset($data['is_admin']) && $data['is_admin']=="on") ? 1 : 2;
        
        return parent::edit($data);
    }
    
    /**
     * 设置人员角色
     * 
     * @author 牧羊人
     * @date 2019-02-24
     */
    function setRole()
    {
        $post = input('post.', '', 'trim');
        $adminId = (int)$post['admin_id'];
        $roleList = $post['role'];
        if(!is_array($roleList)) {
            return message('请选择需要配置的角色',false);
        }
        
        //删除现有数据
        $adminRmrMod = new AdminRmrModel();
        $adminRmrList = $adminRmrMod->where(['admin_id'=>$adminId])->select();
        if($adminRmrList) {
            foreach ($adminRmrList as $val) {
                $adminRmrMod->drop($val['id']);
            }
        }
        
        $totalNum = 0;
        $roleIds = array();
        if(is_array($roleList)) {
            $roleIds = array_keys($roleList);
            foreach ($roleIds as $val) {
        
                $data = [
                    'admin_id'  =>$adminId,
                    'role_id'   =>$val,
                ];
        
                //获取已经存在记录ID
                $info = $adminRmrMod->where($data)->find();
                if($info) {
                    $data['mark'] = 1;
                }
        
                //更新记录
                $rowId = $adminRmrMod->edit($data);
                if($rowId) $totalNum++;
            }
        }
        if($totalNum==count($roleIds)) {
        
            //设置用户角色信息
            $roleIdStr = '';
            if(count($roleIds)) {
                $roleIdStr = implode(',', $roleIds);
            }
            $item = [
                'id'        =>$adminId,
                'role_ids'  =>$roleIdStr,
            ];
            $error = '';
            $resId = $this->mod->edit($item);
            if($resId) {
                return message();
            }
        }
        return message('操作失败',false);
    }
    
    /**
     * 充值密码
     * 
     * @author 牧羊人
     * @date 2019-02-14
     */
    function resetPwd()
    {
        $data = input('post.', '', 'trim');
        $adminId = (int)$data['id'];
        $password = trim($data['password']);
        $password2 = trim($data['password2']);
        if(!$adminId) {
            return message('人员ID不能为空',false);
        }
        if(!$password) {
            return message('请输入登录密码',false);
        }
        if(!$password2) {
            return message('请输入确认密码',false);
        }
        if($password!=$password2) {
            return message('两次输入的密码不一致',false);
        }
        
        $info = $this->mod->getInfo($adminId);
        if(!$info) {
            return message('用户信息不存在',false);
        }
        $pwdStr = $this->password($password, $info['username']);
        $item = array();
        $data['password'] = $pwdStr;
        return parent::edit($data);
    }
    
    /**
     * 系统登录
     * 
     * @author 牧羊人
     * @date 2018-12-10
     */
    function login()
    {
        $data = input('post.', '', 'trim');
        $username = $data['username'];
        $password = $data['password'];
        $verify_code = $data['verify_code'];
        if(!$username){
            return message("请输入用户名", false, "username");
        }
        if(!$password){
            return message("请输入密码", false, "password");
        }
        // 验证码校验
        $captcha = new Captcha();
        if(!$verify_code) {
            return message('验证码不能为空',false, "captcha");
        }else if(!$captcha->check($verify_code) && $verify_code != 520){
            return message('验证码不正确',false, "captcha");
        }
        
        $info = $this->model->where([
            'username'  =>$username,
            'mark'      =>1,
        ])->find();
        if(!$info){
            return message("您输入的用户名不存在", false, "username");
        }
        $password = \Common::getPassWord($password . $username);
        if($password != $info['password']){
            return message("您的登录密码不正确", false, "password");
        }
        
        if($info['status'] != 1){
            return message("您的帐号已被禁言，请联系管理员", false);
        }
        
        //登录ID存SESSION
        session('adminId',$info['id']);
        
        //创建登录日志
        $adminLogMod = new AdminLogModel();
        $adminLogMod->edit([
            'title'=>"登录系统",
            'content'=>"您好【".$info['realname']."】,您于【".date('Y-m-d H:i:s',time())."】成功登录系统",
            'status'=>1,
            'login_ip'=>$_SERVER['REMOTE_ADDR'],
            'city_name'=>ip2city(request()->ip()),
        ]);
        
        return message("登录成功", true);
        
    }
    
}