<?php

namespace app\common\api;

use think\{
    Log, Model, Request, Validate, Config
};

/**
 * Description of UcenterMember
 * 会员模型
 * @author static7
 */
class UcenterMember extends Model
{

    protected $resultSetType = 'collection';
    protected $rule = [
        'username' => 'alphaDash|require|length:6,30|unique:ucenter_member,username',
        'password' => 'require|min:6',
        'repassword' => 'require|confirm:password',
        'email' => "unique:ucenter_member,email|email"
    ];
    protected $msg = [
        'username.requier' => '用户名不能为空',
        'username.unique' => '用户名已经被注册',
        'username.length' => '用户名在6-20个字符之间',
        'username.alphaDash' => '用户名为字母和数字，下划线"_"及破折号"-"',
        'password.require' => '密码不能为空',
        'password.min' => '密码最低6个字符',
        'repassword.require' => '确认密码不能为空',
        'repassword.confirm' => '两次密码不相符',
        'email' => '邮箱格式错误',
        'email.unique' => '邮箱已经被注册过',
    ];
    protected $insert = ['status' => 1, 'username', 'reg_ip'];
    protected $autoWriteTimestamp = true;
    protected $createTime = 'reg_time';
    protected $update = ['last_login_time', 'last_login_ip'];

    /**
     * 用户登录认证
     * @param  string  $username 用户名
     * @param  string  $password 用户密码
     * @param  integer $type     用户名类型 （1-用户名，2-邮箱，3-手机，4-UID）
     * @return integer           登录成功-用户ID，登录失败-错误编号
     */
    public function login($username, $password, $type = 1) {
        $map = [];
        switch ($type) {
            case 1:$map['username'] = $username;
                break;
            case 2:$map['email'] = $username;
                break;
            case 3:$map['mobile'] = $username;
                break;
            case 4:$map['id'] = $username;
                break;
            default:return 0; //参数错误
        }
        /* 获取用户数据 */
        $user = $this::get(function($q)use($map) {
                    $q->where($map)->field('id,status,password');
                });
        if (empty($user) || (int) $user->status !== 1) {
            return -1; //用户不存在或被禁用
        }
        /* 验证用户密码 */
//        Log::record(ucenter_md5($password));
        if (ucenter_md5($password) !== $user['password']) {
            return -2; //密码错误
        }
        $this->updateLogin($user->id); //更新用户登录信息
        return $user['id']; //登录成功，返回用户ID
    }

    /**
     * 更新用户登录信息
     * @param  integer $uid 用户ID
     */
    protected function updateLogin($uid) {
        $this::update(['id' => $uid]);
    }

    /**
     * 注册一个新用户
     * @return int 注册成功-用户信息，注册失败-错误编号
     * @internal param array $data 用户注册信息
     */
    public function register() {
        $data = Request::instance()->post();
        $validate = Validate::make($this->rule, $this->msg);
        if (!$validate->check($data)) {
            return $this->error= $validate->getError(); // 验证失败 输出错误信息
        }
        unset($data['repassword']);
        $data['password'] = ucenter_md5($data['password']);//系统加密
        /* 添加用户 */
        $object = $this::create($data);
        return $object ? $object->toArray() : '未知错误';
    }

    /**
     * 更新用户信息
     * @param int $uid 用户id
     * @param string $password 密码，用来验证
     * @param array $tmp_data 修改的字段临时数组
     * @return true 修改成功，false 修改失败
     * @author huajie <banhuajie@163.com>
     */
    public function updateUserFields(int $uid, string $password, array $tmp_data = []) {
        if (empty($uid) || empty($password) || empty($tmp_data)) {
            return $this->error = '参数错误！';
        }

        //更新前检查用户密码
        if (!$this->verifyUser($uid, $password)) {
            return $this->error = '验证出错：密码不正确！';
        }
        //更新用户信息
        $validate = Validate::make($this->rule, $this->msg);
        $validate->scene('edit', ['password']);
        if (!$validate->scene('edit')->check($tmp_data)) {
            return $validate->getError();
        }
        $data['password'] = ucenter_md5($tmp_data['password']); //系统加密
        return $this::where(['id' => $uid])->update($data);
       
    }

    /**
     * 验证用户密码
     * @param int $uid 用户id
     * @param string $password_in 密码
     * @return true 验证成功，false 验证失败
     * @author huajie <banhuajie@163.com>
     */
    protected function verifyUser(int $uid, string $password_in) {
        $password = $this::where('id', $uid)->value('password');
        if (ucenter_md5($password_in) === $password) {
            return true;
        }
        return false;
    }

    /**
     * 用户名转为小写
     * @author staitc7 <static7@qq.com>
     * @return string
     */
    protected function setUsernameAttr($value) {
        return strtolower($value);
    }

    /**
     * 获取ip
     * @author staitc7 <static7@qq.com>
     */
    protected function setRegIpAttr() {
        return Request::instance()->ip(1);
    }


    /**
     * 最后登录ip
     * @author staitc7 <static7@qq.com>
     */
    protected function setLastLoginIpAttr() {
        return Request::instance()->ip(1);
    }

    /**
     * 最后登录时间
     * @author staitc7 <static7@qq.com>
     */
    protected function setLastLoginTimeAttr() {
        return Request::instance()->time();
    }

}
