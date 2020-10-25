<?php

namespace app\admin\controller;

use think\Loader;
use think\Config;
use think\Log;
use think\Url;
use think\Request;
use think\Db;

/**
 * Description of AuthManager
 * 权限管理
 * @author static7
 */
class AuthManager extends Admin {

    /**
     * 权限管理
     * @author staitc7 <static7@qq.com>
     */
    public function index() {
        $data = Loader::model('AuthGroup')->authManagerList();
        $value = ['list' => $data['data'] ?? null, 'page' => $data['page']];
        $this->view->metaTitle = '权限管理首页';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 单条数据状态修改
     * @param Request $request
     * @param int $value 状态
     * @param null $ids
     * @internal param ids $int 数据条件
     * @author staitc7 <static7@qq.com>
     */
    public function setStatus(Request $request, $value = null, $ids = null) {
        empty($ids) && $this->error('请选择要操作的数据');
        !is_numeric((int)$value) && $this->error('参数错误');
        $info = Loader::model('AuthGroup')->setStatus(['id' => ['in', $ids]], ['status' => $value]);
        return $info !== false ? $this->success($value == -1 ? '删除成功' : '更新成功') : $this->error($value == -1 ? '删除失败' : '更新失败');
    }

    /**
     * 批量数据更新
     * @param Request $request
     * @param int $value 状态
     * @author staitc7 <static7@qq.com>
     */
    public function batchUpdate(Request $request, $value = null) {
        $ids = $request->post();
        empty($ids['ids']) && $this->error('请选择要操作的数据');
        !is_numeric((int)$value) && $this->error('参数错误');
        $info = Loader::model('AuthGroup')->setStatus(['id' => ['in', $ids['ids']]], ['status' => $value]);
        return $info !== false ? $this->success($value == -1 ? '删除成功' : '更新成功') : $this->error($value == -1 ? '删除失败' : '更新失败');
    }

    /**
     * 用户组详情
     * @param int $id 用户组ID
     * @author staitc7 <static7@qq.com>
     */
    public function editGroup($id = 0) {
        if ((int)$id > 0) {
            $value['info'] = Loader::model('AuthGroup')->editGroup((int)$id);
        }
        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 用户组添加或者更新
     * @author staitc7 <static7@qq.com>
     */
    public function writeGroup() {
        $AuthGroup = Loader::model('AuthGroup');
        $info = $AuthGroup->renew();
        return $AuthGroup->getError() ? $this->error($info) : $this->success('操作成功', Url::build('AuthManager/index'));
    }

    /**
     * 访问授权页面
     * @author staitc7 <static7@qq.com>
     * @param int $group_id 组id
     * @return
     */
    public function access($group_id = 0) {
        (int)$group_id || $this->error('用户组ID错误');
        $this->updateRules();
        $auth_group = $this->authGroup();
        $node_list = Loader::controller('Menu')->returnNodes();
        $map['status'] = 1;
        $map['type'] = Config::get('auth_rule.rule_main');
        $AuthRule = Loader::model('AuthRule');
        $main_rules = $AuthRule->mapList($map, 'name,id');
        $map['type'] = Config::get('auth_rule.rule_url');
        $child_rules = $AuthRule->mapList($map, 'name,id');
        $value = ['main_rules' => one_dimensional($main_rules), 'auth_rules' => one_dimensional($child_rules), 'node_list' => $node_list, 'auth_group' => $auth_group, 'this_group' => $auth_group[$group_id], 'group_id' => $group_id,];
        $this->view->metaTitle = '访问授权';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 后台节点配置的url作为规则存入auth_rule
     * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function updateRules() {
        //需要新增的节点必然位于$nodes
        $nodes = Loader::controller('Menu')->returnNodes(false);
        $AuthRule = Loader::model('AuthRule');
        //需要更新和删除的节点必然位于$rules
        $rules = $AuthRule->ruleList();
        if (empty($rules)) {
            return $this->error('没有权限规则');
        }
        //构建insert数据
        $data = []; //保存需要插入和更新的新节点
        foreach ($nodes as $value) {
            $temp['name'] = $value['url'];
            $temp['title'] = $value['title'];
            $temp['module'] = 'admin';
            $temp['type'] = $value['pid'] > 0 ? Config::get('auth_rule.rule_url') : Config::get('auth_rule.rule_main');
            $temp['status'] = 1;
            $data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp; //去除重复项
        }
        $update = []; //保存需要更新的节点
        $ids = []; //保存需要删除的节点的id
        foreach ($rules as $index => $rule) {
            $key = strtolower($rule['name'] . $rule['module'] . $rule['type']);
            if (isset($data[$key])) {//如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
                $data[$key]['id'] = $rule['id']; //为需要更新的节点补充id值
                $update[] = $data[$key];
                unset($data[$key]);
                unset($rules[$index]);
                unset($rule['condition']);
                $diff[$rule['id']] = $rule;
            } elseif ($rule['status'] == 1) {
                $ids[] = $rule['id'];
            }
        }
        if (count($update)) {
            foreach ($update as $k => $row) {
                if ($row != $diff[$row['id']]) {
                    $AuthRule->arrayUpdate($row);
                }
            }
        }
        count($ids) > 0 && $AuthRule->arrayUpdate(['status' => -1], $ids); //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        if (count($data)) { //新添菜单
            foreach ($data as $k => $row) {
                $AuthRule->menuAdd($row);
            }
        }
        if ($AuthRule->getError()) {
            Log::record("[ 信息 ]：" . $AuthRule->getError());
            return false;
        } else {
            return true;
        }
    }

    /**
     * 管理员用户组数据写入/更新
     * @author staitc7 <static7@qq.com>
     */
    public function rulesArrayUpdate() {
        $id = Request::instance()->post('id');
        (int)$id || $this->error('参数错误');
        $rules = Request::instance()->post()['rules'];
        sort($rules);
        $data = ['rules' => implode(',', array_unique($rules)), 'module' => 'admin', 'id' => $id, 'type' => Config::get('auth_config.type_admin'),];
        $info = Db::name('AuthGroup')->update($data);
        return $info !== false ? $this->success('操作成功', Url::build('AuthManager/index')) : $this->error($info);
    }

    /**
     * 用户组授权用户列表
     * @author static7
     * @param int $group_id
     * @return
     */
    public function user($group_id = 0) {
        (int)$group_id || $this->error('用户组ID错误');
        $auth_group = $this->authGroup();
        $map = ['group_id' => $group_id, 'status' => ['egt', 0]];
        $member = Config::get('auth_config.auth_user');
        $auth_group_access = Config::get('auth_config.auth_group_access');
        $list = Db::view($member, 'uid,nickname,last_login_time,last_login_ip,status')->view($auth_group_access, 'group_id', "{$auth_group_access}.uid={$member}.uid")->where($map)->order('uid asc')->paginate(Config::get('list_rows') ?? 10);
        $value = ['list' => $list, 'page' => $list->render(), 'auth_group' => $auth_group, 'group_id' => $group_id];
        $this->view->metaTitle = '用户授权';
        return $this->view->assign($value)->fetch();
    }

    /**
     * 获取用户权限组
     * @author staitc7 <static7@qq.com>
     */
    protected function authGroup() {
        $map = ['status' => ['egt', '0'], 'module' => 'admin', 'type' => Config::get('auth_config.type_admin')];
        $field = 'id,title,rules';
        $auth_group_tmp = Loader::model('AuthGroup')->mapList($map, $field);
        $auth_group = null;
        if ($auth_group_tmp) {
            foreach ($auth_group_tmp as $k => $v) {
                $auth_group[$v['id']] = $v;
            }
            unset($auth_group_tmp);
        }
        return $auth_group;
    }

    /**
     * 解除用户授权访问
     * @param int $uid 用户id
     * @param int $group_id 组id
     * @author staitc7 <static7@qq.com>
     */
    public function removeFromGroup($uid = 0, $group_id = 0) {
        (int)$uid || $this->error('用户ID错误');
        (int)$group_id || $this->error('参数错误');
        $self = is_login();
        if ((int)$uid === (int)$self) {
            $this->error('不允许解除自身授权');
        }
        $group = Loader::model('AuthGroup')->checkGroupId($group_id);
        empty($group) && $this->error('用户组不存在');
        $info = Loader::model('AuthGroupAccess')->removeFromGroup($uid, $group_id);
        return $info ? $this->success('解除授权成功') : $this->error('解除授权失败');
    }

    /**
     * 将用户添加到用户组,入参uid,group_id
     * @param string $uid 用户ID
     * @param int $group_id 用户组ID
     * @author staitc7 <static7@qq.com>
     */
    public function addToGroup($uid = null, $group_id = 0) {
        (int)$group_id || $this->error('参数错误');
        empty($uid) && $this->error('用户组ID不能为空');
        $user_id = array_filter(explode(',', $uid));
        foreach ($user_id as $v) {
            is_administrator((int)$v) && $this->error("编号 {$v} 该用户为超级管理员");
            empty(Loader::model('Member')->userId((int)$v)) && $this->error("编号 {$v} 用户不存在");
        }
        //检查用户组
        $AuthGroup = Loader::model('AuthGroup');
        $auth_group = $AuthGroup->checkGroupId($group_id);
        empty($auth_group) && $this->error('该用户组不存在');

        $AuthGroupAccess = Loader::model('AuthGroupAccess');
        $info = $AuthGroupAccess->addToGroup($user_id, $group_id);
        return $info['status'] ? $this->success($info['info']) : $this->error($info['info']);
    }

    /**
     * 用户授权
     * @param int $id 用户ID
     * @author staitc7 <static7@qq.com>
     */
    public function group($id = 0) {
        (int)$id || $this->error('参数错误');
        $AuthGroup = Loader::model('AuthGroup');
        $map = ['status' => 1, 'type' => Config::get('auth_config.type_admin'), 'module' => 'admin'];
        $auth_groups = $AuthGroup->mapList($map, 'id,title');
        $auth_group_access = Config::get('auth_config.auth_group_access');
        $auth_group = Config::get('auth_config.auth_group');
        $user_group = Db::view($auth_group_access, 'uid,group_id')->view($auth_group, 'id', "{$auth_group_access}.group_id={$auth_group}.id")->where(['uid' => $id, 'status' => 1])->select();
        $user_groups = $user_group ? array_column($user_group, 'group_id') : null;
        $value = ['user_id' => $id, 'auth_groups' => $auth_groups, 'user_groups' => $user_groups ? implode(',', $user_groups) : null];

        return $this->view->assign($value ?? null)->fetch();
    }

    /**
     * 用户添加到用户组
     * @param int $group_id 用户组ID
     * @param int|string $uid 用户ID
     * @author staitc7 <static7@qq.com>
     */
    public function userToGroup($group_id = 0, $uid = 0) {
        (int)$uid || $this->error('用户ID错误');
        is_administrator((int)$uid) && $this->error("该用户为超级管理员");
        $group_ids = [];
        if (!empty($group_id)) {
            $group_ids = array_filter($group_id);
            foreach ($group_ids as $v) {
                empty(Loader::model('AuthGroup')->checkGroupId((int)$v)) && $this->error("编号 {$v} 用户组不存在");
            }
        }
        $AuthGroupAccess = Loader::model('AuthGroupAccess');
        $info = $AuthGroupAccess->userToGroup($uid, $group_ids);
        return $info['status'] ? $this->success($info['info']) : $this->error($info['info']);
    }

}
