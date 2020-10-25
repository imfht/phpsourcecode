<?php

namespace app\admin\model;

use think\Model;
use think\Request;
use think\Session;
use think\Hook;
use think\Loader;
use think\Config;
use think\Validate;

class Member extends Model {

    protected $rule = [
        'nickname' => 'require|unique:member,nickname',
    ];
    protected $msg = [
        'nickname.require' => '昵称不能为空',
        'nickname.unique' => '昵称被占用，换一个吧',
    ];
    protected $autoWriteTimestamp = false;

    /**
     * 登录指定用户
     * @param  integer $user_id 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($user_id = 0) {
        /* 检测是否在当前应用注册 */
        $user = $this::get(function($query)use($user_id) {
                    $query->where('uid', $user_id);
                });
        if (!$user || $user->status != 1) {
            return $this->error = '用户不存在或已被禁用！'; //应用级别禁用
        }
        /* 登录用户 */
        $this->autoLogin($user->toArray());
        return true;
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user) {
        /* 更新登录信息 */
        $data = [
            'uid' => $user['uid'],
            'login' => ['exp', '`login`+1'],
            'last_login_time' => Request::instance()->time(),
            'last_login_ip' => Request::instance()->ip(1)
        ];
        $this::update($data);
        /* 记录登录SESSION和COOKIES */
        $auth = [
            'uid' => $user['uid'],
            'username' => $user['nickname'],
            'last_login_time' => $user['last_login_time'],
        ];
        Session::set('user_auth', $auth);
        Session::set('user_auth_sign', data_auth_sign($auth));
        //记录行为
        $param = ['action' => 'user_login', 'model' => 'member', 'record_id' => $user['uid']];
        Hook::listen('user_behavior', $param);
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logout() {
        Session::delete('user_auth', null);
        Session::delete('user_auth_sign', null);
    }

    /**
     * 用户列表
     * @author staitc7 <static7@qq.com>
     */

    public function userList() {
        $object = $this::where('status', 'neq', -1)->order('reg_time desc')->paginate(Config::get('list_rows') ?? 10);
        return $object ? array_merge($object->toArray(), ['page' => $object->render()]) : [];
    }

    /**
     * 条件查询权限用户
     * @param array $map 查询条件
     * @param boole|string $field 查询的字段
     * @author staitc7 <static7@qq.com>
     */

    public function oneUser(array $map = [], $field = true) {
        $object = $this::get(function($query)use($map, $field) {
                    $query->where($map)->field($field);
                });
        return $object ? $object->toArray() : null;
    }

    /**
     * 用户更新用户昵称
     * @author staitc7 <static7@qq.com>
     */

    public function renew(array $data = []) {
        $validate = Validate::make($this->rule, $this->msg);
        if (!$validate->check($data)) {
            // 验证失败 输出错误信息
            return $validate->getError();
        }
        $object = $this::update($data);
        return $object ? $object->toArray() : null;
    }

    /**
     * 修改状态
     * @param int|array $map 数据的ID或者ID组
     * @param array $data 要修改的数据
     * @author staitc7 <static7@qq.com>
     */

    public function setStatus($map = null, $data = null) {
        if (empty($map) || empty($data)) {
            return false;
        }
        return $this::where($map)->update($data);
    }

    /**
     * 添加用户
     * @author staitc7 <static7@qq.com>
     */

    public function userAdd() {
        $UcenterMember = Loader::model('UcenterMember', 'api');
        $register_data = $UcenterMember->register();
        if (!is_array($register_data)) {
            return $register_data;
        }
        $data = [
            'uid' => $register_data['id'],
            'nickname' => $register_data['username'],
            'reg_ip' => $register_data['reg_ip'],
            'reg_time' => $register_data['reg_time'],
            'status' => $register_data['status'],
        ];
        $object = $this::create($data);
        return $object ? $object->toArray() : null;
    }

    /**
     * 查询用户是否存在
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */

    public function userId(int $user_id = 0): array {
        $object = $this::get(function($query)use($user_id) {
                    $query->where('uid', $user_id);
                });
        return $object ? $object->toArray() : [];
    }

}
