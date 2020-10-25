<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\admin\controller\Auth;
use think\Db;
use think\Model;

/**
 * 后台菜单模型
 * @package app\admin\model
 */
class AuthRule extends Model
{
    protected $not_check_id  = [1];//不检测权限的管理员id
    protected $not_check_url = ['admin/Index/index', 'admin/Sys/clear', 'admin/Index/lang'];//不检测权限的url

    /**
     * 获取所有父节点id(含自身)
     *
     * @param int $id 节点id
     * @param int $status -1不限制 0禁用 1启用
     * @return array
     */
    public function getParents($id = 0, $status = -1)
    {
        //未指定节点,则自动取当前访问url的节点id
        $id = $id ? : $this->getUrlId('', 1);
        if (empty($id)) {
            return [];
        }
        $ids = cache('parents_' . $id);
        if (!$ids) {
            $map = [];
            if ($status !== -1) {
                $map[] =['status', '=', $status];
            }
            $lists = self::where($map)->order('level desc,sort')->column('pid', 'id');
            $ids   = [];
            while (isset($lists[$id]) && $lists[$id] != 0) {
                $ids[] = $id;
                $id    = $lists[$id];
            }
            if (isset($lists[$id]) && $lists[$id] == 0) {
                $ids[] = $id;
            }
            $ids = array_reverse($ids);
            cache('parents_' . $id, $ids);
        }
        return $ids;
    }

    /**
     * 获取当前节点及父节点下菜单(仅显示状态且启用)
     *
     * @param int $id 节点id
     *
     * @return array|mixed
     * @throws
     */
    public function getParentMenus(&$id)
    {
        if (!$id) {
            //未指定节点,则自动取当前访问url的节点id
            $id  = $this->getUrlId('', 1);
        }
        $menus = cache('parent_menus_' . $id);
        if (!$menus) {
            $pid = self::where('id', $id)->value('pid');
            //取$pid下子菜单
            $map = [
                ['display', '=', 1],
                ['status', '=', 1],
                ['pid', '=', $pid]
            ];
            $menus = self::where($map)->order('sort')->select();
            cache('parent_menus_' . $id, $menus);
        }
        return $menus;
    }

    /**
     * 获取指定url的id(可能为显示状态或非显示状态)
     *
     * @param string $url    为空获取当前操作的id
     * @param int    $status 1表示取显示状态,为空或为0则不限制
     *
     * @return int -1表示不需要检测 0表示无后台菜单 其他表示当前url对应id
     */
    public function getUrlId($url = '', $status = 0)
    {
        $url = $url ?: request()->module() . '/' . request()->controller() . '/' . request()->action();
        if ($url == '//') {
            $routeInfo = request()->routeInfo();
            //插件管理
            if ($routeInfo['route'] == '\app\common\controller\Base@execute') {
                $menu_id = self::where('name', 'admin/Addons/addonsIndex')->order('level desc,sort')->value('id');
                return $menu_id ?: 0;
            } else {
                return 0;
            }
        }
        $where   = [];
        $where[] = ['name', '=', $url];
        if ($status) {
            $where[] = ['status', '=', $status];
        }
        $menu_id = self::where($where)->order('level desc,sort')->value('id');
        $menu_id = $menu_id ?: 0;
        return $menu_id;
    }

    /**
     * 权限检测
     *
     * @param int $id 菜单id
     *
     * @return boolean
     */
    public function checkAuth($id = 0)
    {
        $id = $id ?: $this->getUrlId();
        if ($id == -1) {
            return true;
        }
        $uid = session('admin_auth.aid');
        if (in_array($uid, $this->not_check_id)) {
            return true;
        }
        $auth_ids_list = cache('auth_ids_list_' . $uid);
        if (empty($auth_ids_list)) {
            $auth          = new Auth();
            $auth_ids_list = $auth->getAuthList($uid, 1, 'id');
            cache('auth_ids_list_' . $uid, $auth_ids_list);
        }
        if (empty($auth_ids_list)) {
            return false;
        }
        if (in_array($id, $auth_ids_list)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 菜单检查是否有效
     *
     * @param string $name
     *
     * @return bool
     */
    public static function checkName($name)
    {
        return true;
        @list($module, $controller, $action) = explode('/', $name);
        if (!$module || !$controller || !$action) {
            return false;
        }
        //处理action
        $arr    = explode('?', $action);
        $action = (count($arr) == 1) ? $action : $arr[0];
        if (has_controller($module, $controller)) {
            if ($action == 'default') {
                return true;
            } elseif (has_action($module, $controller, $action) == 2) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * 获取模块权限菜单(排除admin/Index下的)
     *
     * @param int $uid 管理员id
     * @param string $module
     * @return array
     * @throws
     */
    public function getRules($uid = 0, $module = '')
    {
        $uid   = $uid ?: session('admin_auth.aid');
        $menus = cache('menus_admin_' . $module . '_' . $uid);
        if ($menus) {
            return $menus;
        }
        $where[] = ['status', '=', 1];
        $where[] = ['display', '=', 1];
        if ($module) {
            $where[] = ['module', '=', $module];
        }
        if (!in_array($uid, $this->not_check_id)) {
            $auth_ids_list = cache('auth_ids_list_' . $uid);
            if (empty($auth_ids_list)) {
                $auth          = new Auth();
                $auth_ids_list = $auth->getAuthList($uid, 1, 'id');
                cache('auth_ids_list_' . $uid, $auth_ids_list);
            }
            if (empty($auth_ids_list)) {
                return [];
            }
            $where[] = ['id', 'in', $auth_ids_list];
        }
        $data = self::where($where)->whereNotLike('name','admin/Index/%')->order('module,sort')->select();
        $tree = new \Tree();
        $tree->init($data, ['child' => '_child', 'parentid' => 'pid']);
        $menus = $tree->getArrayList($data);
        cache('menus_admin_' . $module . '_' . $uid, $menus);
        return $menus;
    }

    /**
     * 获取后台首页权限菜单
     *
     * @param int $uid 管理员id
     * @return array
     * @throws
     */
    public function getIndexRules($uid = 0)
    {
        $uid   = $uid ?: session('admin_auth.aid');
        $menus = cache('menus_admin_index_' . $uid);
        if ($menus) {
            return $menus;
        }
        $where[] = ['status', '=', 1];
        $where[] = ['display', '=', 1];
        $where[] = ['name', 'like', 'admin/Index/%'];
        if (!in_array($uid, $this->not_check_id)) {
            $auth_ids_list = cache('auth_ids_list_' . $uid);
            if (empty($auth_ids_list)) {
                $auth          = new Auth();
                $auth_ids_list = $auth->getAuthList($uid, 1, 'id');
                cache('auth_ids_list_' . $uid, $auth_ids_list);
            }
            if (empty($auth_ids_list)) {
                return [];
            }
            $where[] = ['id', 'in', $auth_ids_list];
        }
        $data = self::where($where)->order('sort')->select();
        $tree = new \Tree();
        $tree->init($data, ['child' => '_child', 'parentid' => 'pid']);
        $menus = $tree->getArrayList($data);
        cache('menus_admin_index_' . $uid, $menus);
        return $menus;
    }

    /**
     * 获取所有权限节点树
     * @param string $module
     * @param int $status -1不限制 0禁用 1启用
     * @return array
     * @throws
     */
    public function getRuelsTree($module = '', $status = -1)
    {
        $rst = cache('auth_rule_' . $module);
        if (!$rst) {
            $map = [];
            if ($module) {
                $map[] = ['module', '=', $module];
            }
            if ($status !== -1) {
                $map[] = ['status', '=', $status];
            }
            $data = self::where($map)->order('module,sort')->select();
            $tree = new \Tree();
            $tree->init($data, ['child' => 'sub', 'parentid' => 'pid']);
            $rst = $tree->getArrayList($data);
            cache('auth_rule_' . $module, $rst);
        }
        return $rst;
    }

    /**
     * 获取子菜单(直接子菜单)
     *
     * @param int $pid
     * @param int $type 1=返回正常数组 2=返回tree_left后的数组
     * @param int $status -1不限制 0禁用 1启用
     * @return array
     * @throws
     */
    public function getChilds($pid = 0, $type = 1, $status = -1)
    {
        $rst = [];
        if ($type == 1) {
            $rst = cache('auth_rule_childs_1_' . $pid);
            if (!$rst) {
                $map[] = ['pid', '=', $pid];
                if ($status !== -1) {
                    $map[] = ['status', '=', $status];
                }
                $rst = self::where($map)->order('module,sort')->select();
                cache('auth_rule_childs_1_' . $pid, $rst);
            }
        } elseif ($type == 2) {
            $rst = cache('auth_rule_childs_2_' . $pid);
            if (!$rst) {
                $data  = self::getChilds($pid, 1, $status);
                if ($data) {
                    $level = ($pid == 0) ? 0 : ($data ? ($data[0]['level'] - 1) : 0);
                    $rst   = tree_left($data, 'id', 'pid', '─', $pid, $level, $level * 20);
                    cache('auth_rule_childs_2_' . $pid, $rst);
                }
            }
        }
        return $rst;
    }

    /**
     * 获取全部子菜单
     *
     * @param  array $lists   数据集
     * @param  int   $pid     父级id
     * @param  bool  $only_id 是否只取id
     * @param  bool  $self    是否包含自身
     *
     * @return array
     */
    public static function getAllChilds($lists, $pid = 0, $only_id = false, $self = false)
    {
        $result=[];
        if (!$result) {
            if (is_array($lists) && $lists) {
                foreach ($lists as $id => $a) {
                    if ($a['pid'] == $pid) {
                        $result[] = $only_id ? $a['id'] : $a;
                        unset($lists[$id]);
                        $result = array_merge($result, self::getAllChilds($lists, $a['id'], $only_id, $self));
                    } elseif ($self && $a['id'] == $pid) {
                        $result[] = $only_id ? $a['id'] : $a;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 获取全部菜单
     *
     * @param int $type 1=返回正常数组 2=返回tree_left后的数组 3=返回select的数组
     * @param string $module
     * @param int $status -1不限制 0禁用 1启用
     * @return array
     */
    public function getAll($type = 1, $module = '', $status = -1)
    {
        $rst = [];
        if ($type == 1) {
            $rst = cache('auth_rule_childs_all_1_' . $module);
            if (!$rst) {
                $map = [];
                if ($module) {
                    $map[] =['module', '=', $module];
                }
                if ($status !== -1) {
                    $map[] =['status', '=', 1];
                }
                $rst = self::where($map)->order('module,sort')->column('*', 'id');
                cache('auth_rule_childs_all_1_' .$module, $rst);
            }
        } elseif ($type == 2) {
            $rst = cache('auth_rule_childs_all_2_' . $module);
            if (!$rst) {
                $rst = self::getAll(1, $module, $status);
                $rst = tree_left($rst);
                cache('auth_rule_childs_all_2_' . $module, $rst);
            }
        } elseif ($type == 3) {
            $rst = cache('auth_rule_childs_all_3_' . $module);
            if (!$rst) {
                $arrs = self::getAll(2, $module, $status);
                $rst  = [];
                foreach ($arrs as $arr) {
                    $rst[$arr['id']] = $arr['lefthtml'] . $arr['title'];
                }
                cache('auth_rule_childs_all_3_' . $module, $rst);
            }
        }
        return $rst;
    }

    /**
     * 增加菜单
     *
     * @param string $name
     * @param string $title
     * @param int    $pid
     * @param int    $display
     * @param int    $notcheck
     * @param int    $sort
     * @param string $icon
     * @param string $module 仅当pid=0时生效
     * @return mixed
     * @throws
     */
    public function add($name, $title, $pid = 0, $display = 0, $notcheck = 0, $sort = 10, $icon = '', $module = 'admin')
    {
        if (!$name || !$title) {
            return 'name、title参数不能为空';
        }
        if (self::checkName($name)) {
            if ($pid==0) {
                $level  = 1;
                $module = $module?:'admin';
            } else {
                $auth_rule = self::get($pid);
                $level = intval($auth_rule['level']) + 1;
                $module = $auth_rule['module'];
            }
            $data  = [
                'name'        => $name,
                'title'       => $title,
                'module'      => $module,
                'pid'         => $pid,
                'sort'        => $sort,
                'status'      => 1,
                'display'     => $display,
                'level'       => $level,
                'notcheck'    => $notcheck,
                'icon'        => $icon,
                'create_time' => time()
            ];
            $rst   = self::insertGetId($data);
            if ($rst) {
                return intval($rst);
            } else {
                return '添加失败';
            }
        } else {
            return 'name格式不正确';
        }
    }

    /**
     * 修改菜单
     *
     * @param int    $id
     * @param string $name
     * @param string $title
     * @param int    $pid
     * @param int    $display
     * @param int    $status
     * @param int    $notcheck
     * @param int    $sort
     * @param string $icon
     * @param string $module 仅pid=0时生效
     * @return mixed
     * @throws
     */
    public function edit($id, $name, $title, $pid = 0, $display = 0, $status = 0, $notcheck = 0, $sort = 10, $icon = '', $module = 'admin')
    {
        if (!$id || !$name || !$title) {
            return 'id、name、title参数不能为空';
        }
        $rule = self::get($id);
        if (!$rule) {
            return '菜单不存在';
        }
        if (self::checkName($name)) {
            if ($pid != $rule['pid']) {
                //改变pid,可能导致level module改变
                if ($pid==0) {
                    $level  = 1;
                    $module = $module?:'admin';
                } else {
                    $rule_new = self::get($pid);
                    $level = intval($rule_new['level']) + 1;
                    $module = $rule_new['module'];
                }
                $level_diff = ($level > $rule['level']) ? ($level - $rule['level']) : ($rule['level'] - $level);
            } else {
                if ($pid == 0) {
                    $module = $module?:'admin';
                }
                $level = $rule['level'];
            }
            $data = [
                'id'       => $id,
                'name'     => $name,
                'title'    => $title,
                'module'   => $module,
                'pid'      => $pid,
                'sort'     => $sort,
                'display' => $display,
                'status'   => $status,
                'level'    => $level,
                'notcheck' => $notcheck,
                'icon'     => $icon
            ];
            // 启动事务
            Db::startTrans();
            try {
                self::update($data);
                if ($pid != $rule['pid']) {
                    //更新子级
                    $lists = self::getAll(1);
                    $ids   = self::getAllChilds($lists, $id, true);
                    if ($level > $rule['level']) {
                        self::where('id', 'in', $ids)->setInc('level', $level_diff);
                    } else {
                        self::where('id', 'in', $ids)->setDec('level', $level_diff);
                    }
                } elseif ($module != $rule['module']) {
                    //更新子级
                    $lists = self::getAll(1);
                    $ids   = self::getAllChilds($lists, $id, true);
                    self::where('id', 'in', $ids)->setField('module', $module);
                }
                // 提交事务
                Db::commit();
            } catch (\Exception $e) {
                // 回滚事务
                Db::rollback();
                return '修改失败';
            }
            return 1;
        } else {
            return 'name格式不正确';
        }
    }

    /**
     * 删除菜单
     *
     * @param int $id
     *
     * @return bool
     * @throws
     */
    public function del($id)
    {
        if (!$id) {
            return 'id参数不能为空';
        }
        $rule = self::get($id);
        if (!$rule) {
            return '菜单不存在';
        }
        // 启动事务
        Db::startTrans();
        try {
            self::destroy($id);
            //删除子级
            $lists = self::getAll(1);
            $ids   = self::getAllChilds($lists, $id, true);
            self::where('id', 'in', $ids)->delete();
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
        return true;
    }
    /**
     * 批量增加菜单,模块菜单添加
     *
     * @param array $menus
     * @param int $pid 父级id
     * @param string $module 仅$pid==0时生效
     * @return mixed
     * @throws
     */
    public function addMenus($menus = [], $module = '', $pid = 0)
    {
        $menu = $this->find($pid);
        if ($menu) {
            $module = $menu['module'];
            $level  = $menu['level']+1;
        } else {
            $module = $module ? : 'admin';
            $level  = 1;
        }
        foreach ($menus as $menu) {
            $data = [
                'name'        => $menu['name'],
                'title'       => $menu['title'],
                'module'      => $module,
                'pid'         => $pid,
                'sort'        => $menu['sort'],
                'display'    => isset($menu['display']) ? $menu['display'] : 1,
                'status'      => isset($menu['status']) ? $menu['status'] :1,
                'level'       => $level,
                'notcheck'    => isset($menu['notcheck']) ? $menu['notcheck'] : 0,
                'icon'        => isset($menu['icon']) ? $menu['icon'] : '',
                'create_time' => time()
            ];
            $result = self::create($data);
            if (!$result) {
                return false;
            }
            if (isset($menu['child'])) {
                $this->addMenus($menu['child'], $module, $result['id']);
            }
        }
        return true;
    }
}
