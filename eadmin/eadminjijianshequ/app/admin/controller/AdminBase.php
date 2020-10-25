<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\common\controller\ControllerBase;

use app\admin\logic\AdminBase as LogicAdminBase;
use app\admin\logic\Menu as LogicMenu;
use app\admin\logic\AuthGroupAccess as LogicAuthGroupAccess;

/**
 * Admin控制器基类
 */
class AdminBase extends ControllerBase
{

    // 后台基础逻辑
    protected $adminBaseLogic = null;

    // 菜单逻辑
    protected $menuLogic = null;

    // 授权逻辑
    protected $authGroupAccessLogic = null;

    // 授权过的菜单列表
    protected $authMenuList = [];

    // 授权过的菜单url列表
    protected $authMenuUrlList = [];

    // 授权过的菜单树
    protected $authMenuTree = [];

    // 菜单视图
    protected $menuView = '';

    // 面包屑视图
    protected $crumbsView = '';

    private static $menuSelect = [];

    /**
     * 构造方法
     */
    public function __construct(LogicAdminBase $adminBaseLogic, LogicMenu $menuLogic, LogicAuthGroupAccess $authGroupAccessLogic)
    {

        // 执行父类构造方法
        parent::__construct();

        // 注入后台逻辑
        $this->adminBaseLogic = $adminBaseLogic;

        // 注入菜单逻辑
        $this->menuLogic = $menuLogic;

        // 注入授权逻辑
        $this->authGroupAccessLogic = $authGroupAccessLogic;

        // 初始化后台模块常量
        $this->initAdminConst();
        // 初始化后台模块信息
        $this->initAdminInfo();


    }

    public function menuToSelect($menu_list = [], $level = 0, $name = 'name', $child = 'children')
    {

        foreach ($menu_list as $info) {

            $tmp_str = str_repeat("&nbsp;", $level * 4);

            $tmp_str .= "├";

            $info['level'] = $level;

            $info[$name] = empty($level) || empty($info['pid']) ? $info[$name] . "&nbsp;" : $tmp_str . $info[$name] . "&nbsp;";

            if (!array_key_exists($child, $info)) {

                array_push(self::$menuSelect, $info);
            } else {


                $tmp_ary = $info[$child];

                unset($info[$child]);

                array_push(self::$menuSelect, $info);

                $this->menuToSelect($tmp_ary, 1, $name, $child);
            }
        }

        return self::$menuSelect;
    }

    /**
     * 初始化后台模块信息
     */
    final private function initAdminInfo()
    {


        // 验证登录
        !MEMBER_ID && $this->redirect(es_url('Login/login'));

        // 获取授权菜单列表
        $this->authMenuList = $this->authGroupAccessLogic->getAuthMenuList(MEMBER_ID);


        // 获得权限菜单URL列表
        $this->authMenuUrlList = $this->authGroupAccessLogic->getAuthMenuUrlList($this->authMenuList);

        // 检查菜单权限
        list($jump_type, $message) = $this->adminBaseLogic->authCheck(URL_MODULE, $this->authMenuUrlList);

        // 权限验证不通过则跳转提示
        RESULT_SUCCESS == $jump_type ?: $this->jump($jump_type, $message);

        // 获取过滤后的菜单树
        $this->authMenuTree = $this->adminBaseLogic->getMenuTree($this->authMenuList, $this->authMenuUrlList);

        if (MEMBER_ID == SYS_ADMINISTRATOR_ID) {


            $AdminList = $this->adminBaseLogic->getAdminAddonList();

            if (!empty($AdminList)) {

                $this->authMenuTree[] = $AdminList;


            }

        }

        // 菜单转换为视图
        $this->menuView = json_encode($this->authMenuTree);


        // 菜单视图
        $this->assign('menu_view', $this->menuView);


        // 登录会员信息
        $this->assign('member_info', session('member_info'));
    }

    /**
     * 初始化后台模块常量
     */
    final private function initAdminConst()
    {

        // 会员ID
        defined('MEMBER_ID') or define('MEMBER_ID', is_login());

        defined('ADMIN_MEMBER_ID') or define('ADMIN_MEMBER_ID', is_login());
        // 是否为超级管理员
        defined('IS_ROOT') or define('IS_ROOT', is_administrator());
    }


    /**
     * 重写fetch方法支持权限过滤
     */
    public function fetch($template = '', $vars = [], $replace = [], $config = [])
    {


        $content = parent::fetchcontent($template);


        echo $this->adminBaseLogic->filter($content, $this->authMenuUrlList);
    }
}
