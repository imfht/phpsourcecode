<?php

namespace app\admin\controller;

use Auth\Auth;
use think\Loader;
use think\Request;
use think\Session;
use think\Db;
use think\Config;
use think\View;

/**
 * 后台公共控制类
 */
class Admin {

    //引入jump类
    use \traits\controller\Jump;

    //当前用户
    protected $uid;
    //超级管理员
    protected $root;
    //视图
    protected $view;

    public function __construct() {
        $this->uid = is_login();
        $this->root = is_administrator();
        $this->uid || $this->redirect('Login/index');
        $this->view = View::instance([], Config::get('replace_str'));
        $this->view->assign('systemMenus', $this->getMenus() ?? null);
    }

    /**
     * 空操作
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function _empty() {
        return $this->view->fetch('common/error');
    }

    /**
     * 通用单条数据状态修改
     * @param Request $Request
     * @param int $value 状态
     * @param null $ids
     * @internal param ids $int 数据条件
     * @author staitc7 <static7@qq.com>
     */
    public function setStatus(Request $Request, $value = null, $ids = null) {
        empty($ids) && $this->error('请选择要操作的数据');
        !is_numeric((int) $value) && $this->error('参数错误');
        $info = Loader::model($Request->controller())->setStatus(['id' => ['in', $ids]], ['status' => $value]);
        return $info !== FALSE ? $this->success($value == -1 ? '删除成功' : '更新成功') : $this->error($value == -1 ? '删除失败' : '更新失败');
    }

    /**
     * 通用批量数据更新
     * @param Request $Request
     * @param int $value 状态
     * @author staitc7 <static7@qq.com>
     */
    public function batchUpdate(Request $Request, $value = null) {
        $ids = $Request->post();
        empty($ids['ids']) && $this->error('请选择要操作的数据');
        !is_numeric((int) $value) && $this->error('参数错误');
        $info = Loader::model($Request->controller())->setStatus(['id' => ['in', $ids['ids']]], ['status' => $value]);
        return $info !== FALSE ? $this->success($value == -1 ? '删除成功' : '更新成功') : $this->error($value == -1 ? '删除失败' : '更新失败');
    }

    /**
     * 通用排序更新
     * @param Request $Request
     * @param int $id 菜单ID
     * @param int $sort 排序
     * @author staitc7 <static7@qq.com>
     */
    public function currentSort(Request $Request, $id = 0, $sort = null) {
        (int) $id || $this->error('参数错误');
        !is_numeric((int) $sort) && $this->error('排序非数字');
        $info = Loader::model($Request->controller())->setStatus(['id' => $id], ['sort' => (int) $sort]);
        return $info !== FALSE ? $this->success('排序更新成功') : $this->error('排序更新失败');
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus() {
        $Request = Request::instance();
        if ($Request->isAjax()) { //ajax 跳过
            return false;
        }
        $controller = $Request->controller();
        $menus = Session::get('admin_meun_list.' . $controller, 'menu');
        if ($menus) {
            return $menus;
        }
        $module = $Request->module();
        $action = $Request->action();
        $where = ['pid' => 0, 'hide' => 0, 'status' => ['neq', -1]];
        Config::get('develop_mode') ? $where['is_dev'] = 0 : false;
        $Menu = Db::name("menu");
        $menus['main'] = $Menu->where($where)->order('sort asc')->field('id,title,url')->select(); // 获取主菜单
        $menus['child'] = []; //设置子节点
        foreach ($menus['main'] as $key => $item) {
            // 判断主菜单权限
            if (!$this->root && !$this->checkRule(strtolower("{$module}/{$item['url']}"), Config::get('auth_rule.rule_main'), null)) {
                unset($menus['main'][$key]);
                continue; //继续循环
            }
            strtolower("{$controller}/{$action}") == strtolower($item['url']) ? $menus['main'][$key]['class'] = 'active' : null;
        }
        $map = ['pid' => ['neq', 0], 'url' => ['like', "%{$controller}/{$action}%"]]; // 查找当前子菜单
        $pid = Db::name("menu")->where($map)->value('pid');
        if ($pid) {
            $tmp_pid = $Menu->field('id,pid')->find($pid);
            $nav = $tmp_pid['pid'] ? $Menu->field('id,pid')->find($tmp_pid['pid']) : $tmp_pid; // 查找当前主菜单
            foreach ($menus['main'] as $key => $item) {
                if ($item['id'] == $nav['id']) {// 获取当前主菜单的子菜单项
                    $menus['main'][$key]['class'] = 'active';
                    $groups = $Menu->where(['group' => ['neq', ''], 'pid' => $item['id']])->distinct(true)->column("group"); //生成child树
                    $where['pid'] = $item['id'];
                    $second_urls = $Menu->where($where)->Field('id,url')->select() ?? []; //获取二级分类的合法url
                    $to_check_urls = $this->toCheckUrl($second_urls); // 检测菜单权限

                    foreach ($groups as $g) {// 按照分组生成子菜单树
                        $where['pid'] = $item['id'];
                        $where['group'] = $g;
                        if (isset($to_check_urls) && !empty($to_check_urls)) {
                            $where['url'] = ['in', $to_check_urls];
                        }
                        $menuList = $Menu->where($where)->field('id,pid,title,url,tip')->order('sort asc')->select();
                        $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                    }
                }
            }
        }
        
        Session::set('admin_meun_list.' . $controller, $menus, 'menu');
        return $menus;
    }

    /**
     * 权限检测
     * @param string $rule 检测的规则
     * @param null $type
     * @param string $mode check模式
     * @return bool
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final private function checkRule($rule, $type = null, $mode = 'url') {
        static $Auth_static = null;
        $Auth = $Auth_static ?? new Auth();
        $type = $type ? $type : Config::get('auth_rule.rule_url');
        if (!$Auth->check($rule, $this->uid, $type, $mode)) {
            return false;
        }
        return true;
    }

    /**
     * 非超级管理员的权限检测
     * @param array $second_urls
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    private function toCheckUrl(array $second_urls = []) {
        // 检测菜单权限
        if ($this->uid) {
            return null;
        }
        $module = Request::instance()->module();
        $to_check_urls = [];
        foreach ($second_urls as $key => $to_check_url) {
            if (stripos($to_check_url, $module) !== 0) {
                $rule = "{$module}/{$to_check_url}";
            } else {
                $rule = $to_check_url;
            }
            if ($this->checkRule($rule, Config::get('auth_rule.rule_url'), null)) {
                $to_check_urls[] = $to_check_url;
            }
        }
        return empty($to_check_urls) ? null : $to_check_urls;
    }

}
