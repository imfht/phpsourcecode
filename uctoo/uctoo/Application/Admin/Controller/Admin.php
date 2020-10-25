<?php
// +----------------------------------------------------------------------
// | UCToo [ Universal Convergence Technology ]
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2016 http://uctoo.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Patrick <contact@uctoo.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use think\Controller;
use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;
use app\common\model\Config;
use app\common\util\Auth;
use app\admin\model\Menu;
use Vendor\requester;
use think\Lang;

/**
 * 后台首页控制器
 * @author Patrick <contact@uctoo.com>
 */
class Admin extends Controller
{

    /**
     * 后台控制器初始化
     */
    protected function _initialize()
    {
        //自动加载语言文件
        $langSet = Lang::detect();
        Lang::load(APP_PATH . 'admin'.DS.'lang'.DS.$langSet.'.php');
        // 获取当前用户ID
        define('UID', is_login());
        trace('UIDinfo','info');
        trace(UID,'info');
        if (!UID) {// 还没登录 跳转到登录页面
            $this->redirect('admin/Base/login');
        }
        /* 读取数据库中的配置 */
        $config = cache('DB_CONFIG_DATA');
        if (!$config) {
            $config	= new Config();
            $configData = $config ->lists();
            cache('DB_CONFIG_DATA',$configData);
        }
        config($config); //添加配置

        // 是否是超级管理员
        define('IS_ROOT', is_administrator());
        if (!IS_ROOT && config('ADMIN_ALLOW_IP')) {
            // 检查IP地址访问
            if (!in_array(get_client_ip(), explode(',', config('ADMIN_ALLOW_IP')))) {
                $this->error(lang('_FORBID_403_'));
            }
        }
        // 检测访问权限
        $access = $this->accessControl();
        if ($access === false) {
            $this->error(lang('_FORBID_403_'));
        } elseif ($access === null) {
            $dynamic = $this->checkDynamic();//检测分类栏目有关的各项动态权限
            if ($dynamic === null) {
                //检测非动态权限
                $rule = strtolower($this->request->module() . '/' . $this->request->controller() . '/' . $this->request->action());
                if (!$this->checkRule($rule, array('in', '1,2'))) {
                    $this->error(lang('_VISIT_NOT_AUTH_'));
                }
            } elseif ($dynamic === false) {
                $this->error(lang('_VISIT_NOT_AUTH_'));
            }
        }
        $this->assign('__MANAGE_COULD__', $this->checkRule('admin/module/lists', array('in', '1,2')));

        $this->assign('__MENU__', $this->getMenus(request()->controller()));
        $this->assign('__MODULE_MENU__', $this->getModules());

        //$this->checkUpdate();
        //$this->getReport();
    }

    /**
     * 权限检测
     * @param string $rule 检测的规则
     * @param string $mode check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function checkRule($rule, $type = AuthRule::RULE_URL, $mode = 'url')
    {
        if (IS_ROOT) {
            return true;//管理员允许访问任何页面
        }
        static $Auth = null;
        if (!$Auth) {
            $Auth = new Auth();
        }
        if (!$Auth->check($rule, UID, $type, $mode)) {
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则会进入checkRule根据节点授权判断权限
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic()
    {
        if (IS_ROOT) {
            return true;//管理员允许访问任何页面
        }
        return null;//不明,需checkRule
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
    final protected function accessControl()
    {
        if (IS_ROOT) {
            return true;//管理员允许访问任何页面
        }
        $allow = config('ALLOW_VISIT');
        $deny = config('DENY_VISIT');
        $check = strtolower($this->request->controller() . '/' . $this->request->action());
        if (!empty($deny) && in_array_case($check, $deny)) {
            return false;//非超管禁止访问deny中的方法
        }
        if (!empty($allow) && in_array_case($check, $allow)) {
            return true;
        }
        return null;//需要检测节点权限
    }




    /**获取模块列表，用于显示在左侧
     * @auth 陈一枭 <yixiao2020@qq.com>
     */
    public function getModules()
    {
        $tag = 'ADMIN_MODULES_' . is_login();
        $modules = cache($tag);
        if ($modules === false) {
            $modules = model('Module')->all();
            foreach ($modules as $key => &$v) {
                if($v['menu_hide']==1){
                    unset($modules[$key]);
                    continue;
                }
                $rule = strtolower($v['admin_entry']);
                  /*if (!$this->checkRule($rule, array('in', '1,2'))) {
                      unset($modules[$key]);
                  }*/
                if ($rule) {
                    $menu = db('Menu')->where(array('module' => $v['name'], 'pid' => 0))->find();
                    if ($menu) {
                        $v['children'] = $this->getSubMenus($menu['id']);
                    }
                }
            }
            cache($tag, $modules);
        }
        return $modules;
    }

    public function getSubMenus($pid)
    {
        $menus = array();
        //生成child树
        $groups = db('Menu')->where("pid = {$pid}")->distinct(true)->field("`group`,`sort`")->order('sort asc')
            ->select();

        if ($groups) {
            $groups = array_column($groups, 'group');
        } else {
            $groups = array();
        }
        //获取二级分类的合法url
        $where = array();
        $where['pid'] = $pid;
        $where['hide'] = 0;
        if (!cache('DEVELOP_MODE')) { // 是否开发者模式
            $where['is_dev'] = 0;
        }
        $second_urls = db('Menu')->field('id,url')->where($where)->select();

        if (!IS_ROOT) {
            // 检测菜单权限
            $to_check_urls = array();
            foreach ($second_urls as $key => $to_check_url) {
                if (stripos($to_check_url, $this->request->module()) !== 0) {
                    $rule = $this->request->module() . '/' . $to_check_url;
                } else {
                    $rule = $to_check_url;
                }
                if ($this->checkRule($rule, AuthRule::RULE_URL, null))
                    $to_check_urls[] = $to_check_url;
            }
        }
        // 按照分组生成子菜单树
        foreach ($groups as $g) {
            $map = array('group' => $g);
            if (isset($to_check_urls)) {
                if (empty($to_check_urls)) {
                    // 没有任何权限
                    continue;
                } else {
                    $map['url'] = array('in', $to_check_urls);
                }
            }
            $map['pid'] = $pid;
            $map['hide'] = 0;
            if (!cache('DEVELOP_MODE')) { // 是否开发者模式
                $map['is_dev'] = 0;
            }
            $menuList = db('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
            $menus[$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $pid);
            if (empty($menus[$g])) {
                unset($menus[$g]);
            }
        }
        return $menus;

    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus($controller = 'index')
    {
        $tag = 'ADMIN_MENU_LIST' . is_login() . $controller;
        $menus = cache($tag);
        if ($menus === false) {
            // 获取主菜单
            $where['pid'] = 0;
            
            if (!config('DEVELOP_MODE')) { // 是否开发者模式
                $where['is_dev'] = 0;
            }
            $menus['main'] = db('Menu')->where($where)->order('sort asc')->select();

            foreach ($menus['main'] as &$v) {
                $v['children'] = $this->getSubMenus($v['id']);
                if ($v['url'] == 'Cloud/index') {
                    extra_addons_menu($v);
                }
            }
            unset($v);
            $menus['child'] = array(); //设置子节点

            //高亮主菜单
            //$current = M('Menu')->where("url like '%{$controller}/" . $this->request->action() . "%'")->field('id')->find();
            $current = db('Menu')->where("url like '{$controller}/" . $this->request->action() . "%' OR url like '%/{$controller}/" . $this->request->action() . "%'  ")->field('id')->find();
            if ($current) {
                $menuModel = new Menu();
                $nav = $menuModel->getPath($current['id']);
                $nav_first_title = $nav[0]['title'];

                foreach ($menus['main'] as $key => $item) {
                    if (!is_array($item) || empty($item['title']) || empty($item['url'])) {
                        $this->error(lang('_CLASS_CONTROLLER_ERROR_PARAM_', array('menus' => $menus)));
                    }
                    if (stripos($item['url'], $this->request->module()) !== 0) {
                        $item['url'] = $this->request->module() . '/' . $item['url'];
                    }
                    // 判断主菜单权限
                    if (!IS_ROOT && !$this->checkRule($item['url'], AuthRule::RULE_MAIN, null)) {
                        unset($menus['main'][$key]);
                        continue;//继续循环
                    }
                    // 获取当前主菜单的子菜单项
                    if ($item['title'] == $nav_first_title) {
                        $menus['main'][$key]['class'] = 'active';
                        //生成child树
                        $groups = db('Menu')->where("pid = {$item['id']}")->distinct(true)->field("`group`,`sort`")
                            ->order('sort asc')->select();

                        if ($groups) {
                            $groups = array_column($groups, 'group');
                        } else {
                            $groups = array();
                        }

                        //获取二级分类的合法url
                        $where = array();
                        $where['pid'] = $item['id'];
                        $where['hide'] = 0;
                        if (!config('DEVELOP_MODE')) { // 是否开发者模式
                            $where['is_dev'] = 0;
                        }
                        $second_urls = db('Menu')->field('id,url')->where($where)->select();

                        if (!IS_ROOT) {
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key => $to_check_url) {
                                if (stripos($to_check_url, $this->request->module()) !== 0) {
                                    $rule = $this->request->module() . '/' . $to_check_url;
                                } else {
                                    $rule = $to_check_url;
                                }
                                if ($this->checkRule($rule, AuthRule::RULE_URL, null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group' => $g);
                            if (isset($to_check_urls)) {
                                if (empty($to_check_urls)) {
                                    // 没有任何权限
                                    continue;
                                } else {
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid'] = $item['id'];
                            $map['hide'] = 0;
                            if (!config('DEVELOP_MODE')) { // 是否开发者模式
                                $map['is_dev'] = 0;
                            }
                            $menuList = db('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                        if ($menus['child'] === array()) {
                            //$this->error('主菜单下缺少子菜单，请去系统=》后台菜单管理里添加');
                        }
                    }
                }
            }
            cache($tag, $menus);
        }
        return $menus;
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree 是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function returnNodes($tree = true)
    {
        static $tree_nodes = array();
        if ($tree && !empty($tree_nodes[(int)$tree])) {
            return $tree_nodes[$tree];
        }
        if ((int)$tree) {
            $list = db('Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if (stripos($value['url'], $this->request->module()) !== 0) {
                    $list[$key]['url'] = $this->request->module() . '/' . $value['url'];
                }
            }
            $nodes = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'operator', $root = 0);
            foreach ($nodes as $key => $value) {
                if (!empty($value['operator'])) {
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        } else {
            $nodes = db('Menu')->field('title,url,tip,pid')->order('sort asc')->select();
            foreach ($nodes as $key => $value) {
                if (stripos($value['url'], $this->request->module()) !== 0) {
                    $nodes[$key]['url'] = $this->request->module() . '/' . $value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree] = $nodes;
        return $nodes;
    }

    public function _empty()
    {
        $this->error(lang('_ERROR_404_2_'));
    }

    public function getReport()
    {

        $result = cache('os_report');
        if (!$result) {
            $url = '/index.php?s=/report/index/check.html';
            $result = $this->visitUrl($url);
            cache('os_report', $result, 60 * 60);
        }
        $report = json_decode($result[1], true);
        $ctime = filemtime("version.ini");
        $check_exists = file_exists('./Application/Admin/Data/' . $report['title'] . '.txt');
        if (!$check_exists) {
            $this_update = explode("\n", $report['this_update']);
            $future_update = explode("\n", $report['future_update']);
            $this->assign('this_update', $this_update);
            $this->assign('future_update', $future_update);
            $this->assign('report', $report);
        }

    }

    private function visitUrl($url, $data = '')
    {
        $host = 'http://demo.uctoo.cn';
        $url = $host . $url;
        $requester = new requester($url);
        $requester->charset = "utf-8";
        $requester->content_type = 'application/x-www-form-urlencoded';
        $requester->data = http_build_query($data);
        $requester->enableCookie = true;
        $requester->enableHeaderOutput = false;
        $requester->method = "post";
        $arr = $requester->request();
        return $arr;
    }
}
