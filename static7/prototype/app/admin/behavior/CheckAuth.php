<?php

namespace app\admin\behavior;

use think\Request;
use think\Config;
use think\Log;
use think\Cache;

/**
 * Description of CheckAuth
 * 检测用户权限行为
 * @author static7
 */
class CheckAuth {

    use \traits\controller\Jump;

    private $uid; //登录用户
    private $admin; //超级管理员

    public function __construct() {
        $this->request = Request::instance();
        $this->uid = is_login();
        $this->admin = is_administrator();
    }

    /**
     * 检测用户行为
     */
    public function run() {
        if ($this->uid < 1) {//没有登录的用户自动返回登录页面
            return true;
        }
        if ($this->admin) {
            Log::record("[ 权限 ]：超级管理员跳过");
            return true;
        }
        $status = false;
        $access = $this->accessControl();
        if (false === $access) {
            return $this->error('403:禁止访问');
        } elseif (null === $access) {//检测访问权限
            $rule = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
            if ($this->checkRule($rule, ['in', '1,2'])) {
                $status = $this->checkDynamic(); // 检测分类及内容有关的各项动态权限
            }
        } else {
            return true;
        }
        Log::record("[ 权限 ]：已经检查" . $rule ?? null);
        return $status ? true : $this->error('未授权访问!');
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则表示权限不明
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic() {
        return true;
    }

    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function accessControl() {
        $allow = Config::get('allow_visit'); //不受限控制器方法
        $deny = Config::get('deny_visit'); //超管专限控制器方法
        $check = strtolower($this->request->controller() . '/' . $this->request->action());
        if ($deny && in_array_case($check, $deny)) {
            return false; //非超管禁止访问deny中的方法
        }
        if ($allow && in_array_case($check, $allow)) {
            return true;
        }
        return null; //需要检测节点权限
    }

    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function checkRule($rule, $type = null, $mode = 'url') {
        static $Auth_static = null;
        $Auth = $Auth_static ?? new \Auth\Auth();
        $type = $type ? $type : Config::get('auth_rule.rule_url');
        if (!$Auth->check($rule, $this->uid, $type, $mode)) {
            return false;
        }
        return true;
    }

}
