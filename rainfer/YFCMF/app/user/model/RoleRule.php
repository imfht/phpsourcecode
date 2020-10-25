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

namespace app\user\model;

use app\user\controller\AdminAuth;
use think\Db;
use think\Model;

/**
 * 会员规则模型
 * @package app\admin\model
 */
class RoleRule extends Model
{
    protected $not_check_id  = [1];//不检测权限的会员id
    protected $not_check_url = [];//不检测权限的url

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
        $id = $id ? : $this->getUrlId();
        if (empty($id)) {
            return [];
        }
        $ids = cache('user_rule_parents_' . $id);
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
            cache('user_rule_parents_' . $id, $ids);
        }
        return $ids;
    }

    /**
     * 获取指定url的id(可能为显示状态或非显示状态)
     * @param string $url    为空获取当前操作的id
     * @return int -1表示不需要检测 0表示无后台菜单 其他表示当前url对应id
     */
    public function getUrlId($url = '')
    {
        $url = $url ?: request()->module() . '/' . request()->controller() . '/' . request()->action();
        $where   = [];
        $where[] = ['name', '=', $url];
        $where[] = ['module', '=', request()->module()];
        $menu_id = self::where($where)->value('id');
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
        $uid = session('user.id');
        if (in_array($uid, $this->not_check_id)) {
            return true;
        }
        $auth_ids_list = cache('user_auth_ids_list_' . $uid);
        if (empty($auth_ids_list)) {
            $auth          = new AdminAuth();
            $auth_ids_list = $auth->getAuthList($uid, 1, 'id');
            cache('user_auth_ids_list_' . $uid, $auth_ids_list);
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
     * 获取所有树形规则
     * @param string $module
     * @param int $status -1不限制 0禁用 1启用
     * @return array
     * @throws
     */
    public function getRuelsTree($module = '', $status = -1)
    {
        $rst = cache('user_rule_' . $module);
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
            cache('user_rule_' . $module, $rst);
        }
        return $rst;
    }

    /**
     * 获取子规则(直接子规则)
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
            $rst = cache('user_rule_childs_1_' . $pid);
            if (!$rst) {
                $map[] = ['pid', '=', $pid];
                if ($status !== -1) {
                    $map[] = ['status', '=', $status];
                }
                $rst = self::where($map)->order('module,sort')->select();
                cache('user_rule_childs_1_' . $pid, $rst);
            }
        } elseif ($type == 2) {
            $rst = cache('user_rule_childs_2_' . $pid);
            if (!$rst) {
                $data  = self::getChilds($pid, 1, $status);
                if ($data) {
                    $level = ($pid == 0) ? 0 : ($data ? ($data[0]['level'] - 1) : 0);
                    $rst   = tree_left($data, 'id', 'pid', '─', $pid, $level, $level * 20);
                    cache('user_rule_childs_2_' . $pid, $rst);
                }
            }
        }
        return $rst;
    }

    /**
     * 获取全部子规则
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
     * 获取全部规则
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
            $rst = cache('user_rule_childs_all_1_' . $module);
            if (!$rst) {
                $map = [];
                if ($module) {
                    $map[] =['module', '=', $module];
                }
                if ($status !== -1) {
                    $map[] =['status', '=', 1];
                }
                $rst = self::where($map)->order('module,sort')->column('*', 'id');
                cache('user_rule_childs_all_1_' .$module, $rst);
            }
        } elseif ($type == 2) {
            $rst = cache('user_rule_childs_all_2_' . $module);
            if (!$rst) {
                $rst = self::getAll(1, $module, $status);
                $rst = tree_left($rst);
                cache('user_rule_childs_all_2_' . $module, $rst);
            }
        } elseif ($type == 3) {
            $rst = cache('user_rule_childs_all_3_' . $module);
            if (!$rst) {
                $arrs = self::getAll(2, $module, $status);
                $rst  = [];
                foreach ($arrs as $arr) {
                    $rst[$arr['id']] = $arr['lefthtml'] . $arr['title'];
                }
                cache('user_rule_childs_all_3_' . $module, $rst);
            }
        }
        return $rst;
    }

    /**
     * 增加规则
     *
     * @param string $name
     * @param string $title
     * @param int    $pid
     * @param int    $notcheck
     * @param int    $sort
     * @param string $icon
     * @param string $module 仅当pid=0时生效
     * @param string $condition
     * @return int|string
     * @throws
     */
    public function add($name, $title, $pid = 0, $notcheck = 0, $sort = 10, $icon = '', $module = 'cms', $condition = '')
    {
        if (!$name || !$title) {
            return 'name、title参数不能为空';
        }
        if ($pid==0) {
            $level  = 1;
            $module = $module?:'cms';
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
            'condition'  => $condition,
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
    }

    /**
     * 修改规则
     *
     * @param int    $id
     * @param string $name
     * @param string $title
     * @param int    $pid
     * @param int    $status
     * @param int    $notcheck
     * @param int    $sort
     * @param string $icon
     * @param string $module 仅pid=0时生效
     * @param string $condition
     * @return bool|string
     * @throws
     */
    public function edit($id, $name, $title, $pid = 0, $status = 0, $notcheck = 0, $sort = 10, $icon = '', $module = 'cms', $condition = '')
    {
        if (!$id || !$name || !$title) {
            return 'id、name、title参数不能为空';
        }
        $rule = self::get($id);
        if (!$rule) {
            return '规则不存在';
        }
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
                $module = $module?:'cms';
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
            'status'   => $status,
            'level'    => $level,
            'notcheck' => $notcheck,
            'icon'     => $icon,
            'condition' => $condition,
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
    }

    /**
     * 删除规则
     *
     * @param int $id
     *
     * @return bool|string
     * @throws
     */
    public function del($id)
    {
        if (!$id) {
            return 'id参数不能为空';
        }
        $rule = self::get($id);
        if (!$rule) {
            return '规则不存在';
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
}
