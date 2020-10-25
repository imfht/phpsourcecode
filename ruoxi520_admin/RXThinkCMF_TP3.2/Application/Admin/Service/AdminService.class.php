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
 * 系统人员管理-服务类
 * 
 * @author 牧羊人
 * @date 2018-07-06
 */
namespace Admin\Service;
use Admin\Model\ServiceModel;
use Admin\Model\AdminModel;
use Admin\Model\AdminRmrModel;
use Admin\Model\AdminLogModel;
class AdminService extends ServiceModel {
    public function __construct() {
        parent::__construct();
        $this->mod = new AdminModel();
    }
    
    /**
     * 获取数据列表
     * 
     * @author 牧羊人
     * @date 2018-07-17
     * (non-PHPdoc)
     * @see \Admin\Model\BaseModel::getList()
     */
    function getList() {
        $param = I("request.");
        
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
     * @date 2018-07-19(non-PHPdoc)
     * @see \Admin\Model\BaseModel::edit()
     */
    function edit() {
        $data = I('post.', '', 'trim');
        $avatar = trim($data['avatar']);
        $username = trim($data['username']);
        $password = trim($data['password']);
        //字段验证
        if(!$data['id'] && !$avatar) {
            return message('请上传头像',false);
        }
        
        //数据处理
        if(strpos($avatar, "temp")) {
            $data['avatar'] = \Zeus::saveImage($avatar, 'admin');
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
     * @date 2018-07-19
     */
    function setRole() {
        $post = I('post.', '', 'trim');
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
     * 重置密码
     *
     * @author 牧羊人
     * @date 2018-04-01
     */
    function resetPwd($post) {
        $data = I('post.', '', 'trim');
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
     * 系统人员登陆
     * 
     * @author 牧羊人
     * @date 2018-07-06
     */
    public function login() {
        $post = I('post.', '', 'trim');
        
        $username = $post['username'];
        $password = $post['password'];
        $captcha = $post['captcha'];
        if(!$username){
            return message("请输入用户名", false, "username");
        }
        if(!$password){
            return message("请输入密码", false, "password");
        }
        if(!$captcha) {
            return message('验证码不能为空',false, "captcha");
        }else if(!$this->check_verify($captcha) && $captcha != 520){
            return message('验证码不正确',false, "captcha");
        }
        
        $info = $this->mod->where([
            'username'  =>$username,
            'mark'      =>1,
        ])->find();
        if(!$info){
            return message("您的用户名不正确", false, "username");
        }
        
        $password = $this->password($password , $username);
        if($password != $info['password']){
            return message("您的登录密码不正确", false, "password");
        }
        
        if($info['status'] != 1){
            return message("您的帐号已被禁言，请联系管理员", false);
        }
        
        //登录人ID存入SESSION
        $adminId = $info['id'];
        $_SESSION['adminId'] = $adminId;
        
        //更新用户表
        $data = [
            'id'=>$adminId,
            'login_time'=>time(),
            'login_ip'=>get_client_ip(),
            'login_num'=>$info['login_num']+1,
        ];
        $result = $this->mod->edit($data);
        if(!$result) {
            return message('登录失败',false);
        }
        
        //创建登录日志
        $log = [
            'title'=>"登录系统",
            'content'=>"您好【".$info['realname']."】,您于【".date('Y-m-d H:i:s',time())."】成功登录系统",
            'status'=>1,
            'login_ip'=>$_SERVER['REMOTE_ADDR'],
            'city_name'=>ip2city($data['login_ip']),
        ];
        $adminLogMod = new AdminLogModel();
        $adminLogMod->edit($log);
        
        return message("登录成功", true);
        
    }
    
    /**
     * 验证码校验
     *
     * @author 牧羊人
     * @date 2018-07-06
     */
    public function check_verify($code, $id = '') {
        $verify = new \Think\Verify();
        $res = $verify->check($code, $id);
        return $res;
    }
    
    /**
     * 获取组合密码
     * 
     * @author 牧羊人
     * @date 2018-07-07
     */
    private function password($password , $username) {
        $password = md5(md5($password . $username));
        return $password ;
    }
    
}
?>