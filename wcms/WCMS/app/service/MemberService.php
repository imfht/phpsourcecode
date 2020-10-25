<?php

/**
 * 简单的用户系统
 * 头像验证、用户组、实名认证
 * @author wolf
 * @since 2014-07-12
 *
 */
class MemberService
{

    const SUCCESS = 'success';

    const ERROR = 'error';

    const USERNAME = 'username';
    // 系统默认管理员 权限从上到下 默认是5
    public $_group = array(
            1 => "站长",
            2 => "管理员",
            3 => "实名用户",
            4 => "注册会员",
            5 => "游客",
            6 => "黑名单",
            0 => "继承分类权限"
    );
    // 分页数
    private $_num = 40;
    // 用来区分等级
    // 用户等级制度
    private $_sex = array(
            0 => "先生",
            1 => "女士"
    );

    public $_verify = array(
            "-1" => "不通过",
            "0" => "尚未认证",
            "1" => "实名认证"
    );

    public $_status = array(
            "0" => "活跃",
            "1" => "禁用",
            2 => "离职"
    );


    const OVERTIME = 30; // 登陆超时时间 天数
    /**
     * 保存用户
     */
    public function saveMemberByUid ($user)
    {
        $uid=$user['uid'];
        unset($user['uid'],$user['password']);
        MemberModel::instance()->saveMemberByUid($user, $uid);
    }



    /**
     * 增加用户
     */
    public function register ($userInfo)
    {
        if (strlen($userInfo['mobile_phone']) != 11) {
            return array(
                    'status' => false,
                    'message' => "手机号码为11位数"
            );
        }
        if (strlen($userInfo['password']) < 5) {
            return array(
                    'status' => false,
                    'message' => "密码至少为5位数"
            );
        }

        if ($this->filter($userInfo['username'])) {
            return array(
                    'status' => false,
                    'message' => "用户名中包含了标点符号"
            );
        }
        $moblie = MemberModel::instance()->getMemberByMobile($userInfo['mobile_phone']);
        if (! empty($moblie)) {
            return array(
                    'status' => false,
                    'message' => "手机号码重复了!"
            );
        }


        $salt=$this->getRandNum(6);
        $userInfo['salt']=$salt;
        $userInfo['password']= md5(md5( $userInfo['password']) . $salt);
        $ret=MemberModel::instance()->addMember($userInfo);
        if ($ret > 0) {
            return array(
                'status' => true,
                'message' => "注册成功",
                'data' => null
            );
        }
        return array(
            'status' => false,
            'message' => "注册失败",
            'data' => null
        );
    }




    public function login ($user)
    {

        $rs=  $this->isLogin($user['mobile_phone'], $user['password']);

        if(!$rs['status']){
            return $rs;
        }

        $encryptSer=new EncryptService();
        $code = "wcms#" . $rs['data']['uid'] . "#wcms";
        $openid=$encryptSer->jiami($code);
        setcookie("openid", $openid, time() +86400, "/");
        return array('status'=>true,'data'=>array('openid'=>$openid),true);
    }

    /**
     * 用户账户状态
     *
     * @param string $ids            
     * @param string $type            
     */
    public function saveStatus ($status, $uid)
    {
        $rs = $this->_baseObj->setStatusByUid($status, $uid);
        if ($rs > 0) {
            return array(
                    'status' => true,
                    'data' => $this->_status[$status],
                    'message' => "更新成功"
            );
        } else {
            return array(
                    'status' => false,
                    'data' => $this->_status[$status],
                    'message' => "更新失败"
            );
        }
    }


    /**
     * 删除会员
     *
     * @param int $uid            
     */
    public function removeMemberByUid ($uid)
    {
     return MemberModel::instance()->removeMemberByUid($uid);
    }


    /**
     * 用户修改密码
     *
     * @param int $uid            
     * @param String $password            
     * @param String $newPassword            
     */
    public function rePassword ($uid, $password, $newPassword)
    {
        $base = new MemberBaseModule();
        $user = $base->getCon($uid);
        $oldPassword = md5(md5($password) . $user['salt']);
        if ($this->filter($newPassword)) {
            return "密码中请勿包含标点符号等";
        }
        if ($newPassword == "123456") {
            return "密码过于简单";
        }
        if ($newPassword == $password) {
            return "新的密码和旧密码不能相同";
        }
        if (strlen($newPassword) < 5) {
            return "密码至少为6位";
        }
        if ($user['password'] != $oldPassword) {
            return "原密码错误";
        } else {
            $this->_baseObj->setPasswordByUid(trim($newPassword), $uid);
            return "修改成功,下次登录时生效";
        }
    }


    public function getMemberByUid($uid){
        return MemberModel::instance()->getMemberByUid($uid);
    }

    // 邮件修改密码
    public function mailPassword ($mobile)
    {
        $user = $this->getMemberByMobile($mobile);
        if (empty($user)) {
            return array(
                    'status' => false,
                    'message' => "账号不存在"
            );
        }
        
        if (empty($user['email'])) {
            return array(
                    'status' => false,
                    'message' => "没有绑定邮箱,请联系管理员!"
            );
        }
        $newPassword = $this->getRandNum(6);
        $this->_baseObj->setPasswordByUid(trim($newPassword), $user['uid']);
        $email = new EmailService();
        $sys = new SysService();
        $config = $sys->getConfig();
        $mailcontent = "恭喜你，获取了新的密码:" . $newPassword . "点击重新登录<a href=\"http://" .
                 $config['website'] . "\">" . $config['website'] . "</a>";
        $email->send($user['email'], "密码找回", $mailcontent);
    }

    private function getRandNum ($num)
    {
        $str = 'ABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
        $pd = "";
        for ($i = 0; $i < $num; $i ++) {
            $sj = rand(0, strlen($str) - 1);
            $pd .= $str{$sj};
        }
        return $pd;
    }


    public function getAllMember(){
       return  MemberModel::instance()->getAllMemeber();
    }

    /**
     * 判断是否登录
     *
     * @param String $username            
     * @param String $passsword            
     * @return Array 用户信息
     */
    public function isLogin ($username, $passsword)
    {
        // 下面调用服务
        // 查询用户是否存在
        $userInfo = MemberModel::instance()->getMemberByMobile($username);
        // 简单验证
        if (empty($userInfo)) {
            return array(
                    'status' => false,
                    'message' => "手机号码不存在"
            );
        }
        if (strlen($username) < 4 || strlen($passsword) < 4) {
            return array(
                    'status' => false,
                    'message' => "用户名或密码不能小与6位!"
            );
        }
        // 判断密码是否正确
        if ($userInfo['password'] != md5(md5($passsword) . $userInfo['salt'])) {
            return array(
                    'status' => false,
                    'message' => "密码不正确"
            );
        }
        // 检查账号是否被禁用
        if ($userInfo['status'] > 0) {
            return array(
                    'status' => false,
                    'message' => "账号已被禁用!"
            );
        }
        return array(
                'status' => true,
                'data' => $userInfo
        );
    }

    /**
     * 检测验证码是否正确
     */
    private function checkValidate ($codeimg)
    {
        $captcha = new Captcha();
        return $captcha->check($codeimg);
    }

    /**
     * 检测是否包含非法字符
     *
     * @return boolean true or false
     */
    private function filter ($str)
    {
        $pattern = "#[\*\.\/\?\-\%\!]+#i";
        
        return preg_match($pattern, $str);
    }

    /**
     * 分页
     *
     * @return void
     */
    private function page ($p, $total)
    {
        $pageid = isset($p) ? $p : 1;
        $start = ($pageid - 1) * $this->_num;
        $pagenum = ceil($total / $this->_num);
        return array(
                'start' => $start,
                'num' => $this->_num,
                'current' => $pageid,
                'page' => $pagenum
        );
    }
}

