<?php
/**----------------------------------------------------------------------
 * OpenCenter V3
 * Copyright 2014-2018 http://www.ocenter.cn All rights reserved.
 * ----------------------------------------------------------------------
 * Author: wdx(wdx@ourstu.com)
 * Date: 2018/9/12
 * Time: 10:09
 * ----------------------------------------------------------------------
 */

namespace app\admin\controller;

use think\response\Json;
use app\admin\model\AdminLog;

class Admin extends Base
{
    protected $admin;
    protected $adminAuthRule;
    protected $adminAuthGroup;
    protected $adminLog;

    public function initialize()
    {
        parent::initialize();
        $this->admin = model('admin/Admin');
        $this->adminAuthRule = model('admin/AdminAuthRule');
        $this->adminAuthGroup = model('admin/AdminAuthGroup');
        $this->adminLog = model('admin/AdminLog');
    }

    /**
     * 管理员列表
     * @return mixed
     * @author:wdx(wdx@ourstu.com)
     */
    public function adminList()
    {
        if ($this->request->isAjax()) {
            $page = input('get.page/d', 1);
            $limit = input('get.limit/d', 20);
            $map[] = ['status', '>=', 0];
            $adminList = $this->admin
                ->where($map)
                ->page($page, $limit)
                ->select()
                ->toArray();
            $group = $this->adminAuthGroup->where('status', 1)->column('id, title');
            $ids = array_keys($group);
            foreach ($adminList as &$val) {
                $groupIds = explode(',', $val['group_id']);
                foreach ($groupIds as $k => $v) {
                    if (in_array($v, $ids)) {
                        $groupIds[$k] = $group[$v];
                    } else {
                        unset($groupIds[$k]);
                    }
                }
                unset($v);
                $val['group_name'] = implode(',', $groupIds);
                to_status($val, 'status');
                to_time($val, 'last_login_time');
                to_ip($val, 'last_login_ip');
            }
            unset($val);
            $count = $this->admin->where($map)->count();
            $data = [
                'code' => 0,
                'msg' => '数据返回成功',
                'count' => $count,
                'data' => $adminList
            ];
            AdminLog::setTitle('获取管理员列表');
            return json($data);
        }
        AdminLog::setTitle('管理员列表');
        return $this->fetch();
    }

    /**
     * 删除管理员
     * @author:wdx(wdx@ourstu.com)
     */
    public function delAdmin()
    {
        $ids = array_unique(input('post.id/a', []));
        foreach ($ids as $val) {
            if ($val === get_aid()) {
                $this->error('禁止删除当前管理员');
            }
            if ($val === 1) {
                $this->error('禁止删除超级管理员');
            }
            $rs = $this->admin->where('id', $val)->setField('status', '-1');
        }
        unset($val);
        if ($rs) {
            AdminLog::setTitle('删除管理员');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 管理员表单
     * @author:wdx(wdx@ourstu.com)
     */
    public function adminForm()
    {
        if ($this->request->isPost()) {
            $data = input('');
            $this->_checkAdminData($data);
            if (isset($data['status'])) {
                $data['status'] = 1;
            } else {
                $data['status'] = 0;
            }
            if (trim($data['password'])) {
                $data['password'] = think_ucenter_md5($data['password']);
            } else {
                unset($data['password']);
            }

            $title = $data['id'] ? '编辑' : '新增';
            if ($data['id']) {     //编辑
                $rs = $this->admin->update($data);
                if ($data['id'] == get_aid()) {     //修改自己的资料
                    session('admin_auth', null);
                    session('admin_auth_sign', null);
                    $adminInfo = $this->admin->find($data['id']);
                    $loginController = new Login();
                    $loginController->doLogin($adminInfo);
                }
            } else {        //新增
                $rs = $this->admin->insert($data);
            }
            if ($rs) {
                AdminLog::setTitle($title . '管理员成功');
                $this->success($title . '管理员成功');
            } else {
                AdminLog::setTitle($title . '管理员成功');
                $this->error($title . '管理员失败');
            }
        } else {
            $aid = input('get.aid/d', 0);
            $admin = $this->admin->find($aid);
            $group = $this->adminAuthGroup->where('status', 1)->column('id, title');
            $groups = [];
            foreach ($group as $key => $val) {
                $temp['id'] = $key;
                $temp['name'] = $val;
                $groups[] = $temp;
            }
            $groupJson = json_encode($groups);
            $this->assign('admin', $admin);
            $this->assign('group', $group);
            $this->assign('groupJson', $groupJson);
            return $this->fetch();
        }
    }

    /**
     * 管理员信息验证
     * @param array $data
     * @return bool|Json
     * @author:wdx(wdx@ourstu.com)
     */
    private function _checkAdminData($data = [])
    {
        //用户名验证
        if (!$data['username']) {
            $this->error('用户名不能为空');
        }
        if (isset($data['id'])) {
            $id = $this->admin->where('username', $data['username'])->value('id');
            if (!$id) {
                return true;
            }
            if ($id != $data['id']) {
                $this->error('该用户名已存在');
            }
        } else {
            if ($this->admin->where('username', $data['username'])->value('id')) {
                $this->error('该用户名已存在');
            }
        }
        if (!$data['group_id']) {
            $this->error('请选择分组');
        }
        //密码验证
        if (trim($data['password']) && !preg_match('/[A-Za-z0-9]{6,}/', $data['password'])) {
            $this->error('密码不得少于六位');
        }
        //邮箱验证
        if (!preg_match(config('app.email_regexp'), $data['email'])) {
            $this->error('邮箱格式不正确');
        }
        //手机号验证
        if (!preg_match(config('app.mobile_regexp'), $data['mobile'])) {
            $this->error('手机号格式不正确');
        }
        return true;
    }

    /**
     * 管理员权限列表
     * @return mixed
     * @author:wdx(wdx@ourstu.com)
     */
    public function adminAuthList()
    {
        if ($this->request->isAjax()) {
            $page = input('get.page/d', 1);
            $limit = input('get.limit/d', 20);
            $map[] = ['status', '>=', 0];
            $adminAuthList = $this->adminAuthRule
                ->where($map)
                ->page($page, $limit)
                ->select()
                ->toArray();
            $adminAuthTitle = $this->adminAuthRule->where('status', '>=', 0)->column('id, title');
            foreach ($adminAuthList as &$val) {
                to_status($val, 'status');
                to_is_show($val);
                to_yes_no($val, 'is_menu');
                $val['ptitle'] = (($val['pid']) ? $adminAuthTitle[$val['pid']] . '[' . $val['pid'] . ']' : '-');
                $val['icon'] = "<i class=\"layui-icon " . $val['icon'] . "\"></i>";
            }
            unset($val);
            $count = $this->adminAuthRule->where($map)->count();
            $data = [
                'code' => 0,
                'msg' => '数据返回成功',
                'count' => $count,
                'data' => $adminAuthList
            ];
            AdminLog::setTitle('获取管理员权限列表');
            return json($data);
        }
        AdminLog::setTitle('管理员权限列表');
        return $this->fetch();
    }

    /**
     * 删除管理员权限
     * @author:wdx(wdx@ourstu.com)
     */
    public function delAdminAuth()
    {
        $ids = array_unique(input('post.id/a', []));
        foreach ($ids as $val) {
            if ($this->adminAuthRule->where('pid', $val)->where('status', 1)->find()) {
                $this->error('该权限下存在可用子权限，删除失败');
            }
            if (!$this->adminAuthRule->where('id', $val)->setField('status', '-1')) {
                $this->error('删除失败');
            }
        }
        AdminLog::setTitle('删除管理员权限');
        $this->success('删除成功');

    }

    /**
     * 管理员权限表单
     * @return mixed
     * @author:wdx(wdx@ourstu.com)
     */
    public function adminAuthForm()
    {
        if ($this->request->isPost()) {
            $data = input('');
            $this->_checkAdminAuthData($data);
            //转小写
            $data['module'] = strtolower($data['module']);
            $data['name'] = strtolower($data['name']);
            //状态
            $data['status'] = (isset($data['status']) ? '1' : '0');
            //是否菜单
            $data['is_menu'] = (isset($data['is_menu']) ? '1' : '0');
            //是否显示
            $data['is_show'] = (isset($data['is_show']) ? '1' : '0');
            $title = $data['id'] ? '编辑' : '新增';
            if ($data['id']) {     //编辑
                $rs = $this->adminAuthRule->update($data);
            } else {               //新增
                $data['create_time'] = time();
                $rs = $this->adminAuthRule->insert($data);
            }
            if ($rs) {
                cache('menu', null);
                AdminLog::setTitle($title . '管理员权限成功');
                $this->success($title . '管理员权限成功');
            } else {
                AdminLog::setTitle($title . '管理员权限失败');
                $this->error($title . '管理员权限失败');
            }
        } else {
            $id = input('get.id/d', 0);
            $adminAuthTree = $this->adminAuthRule->getTree('id, pid, title');
            $adminAuth = $this->adminAuthRule->find($id);
            $this->assign('adminAuthTree', $adminAuthTree);
            $this->assign('adminAuth', $adminAuth);
            AdminLog::setTitle('管理员权限表单');
            return $this->fetch();
        }
    }

    /**
     * 管理员权限信息验证
     * @param $data
     * @return bool
     * @author:wdx(wdx@ourstu.com)
     */
    private function _checkAdminAuthData($data)
    {
        //权限名验证
        if (!$data['title']) {
            $this->error('权限名称不能为空');
        }
        //URL验证
        if (!$data['name']) {
            $this->error('URL不能为空');
        }
        //唯一性校验
        if (isset($data['id'])) {
            if ($this->adminAuthRule->where('title', $data['title'])->value('id') != $data['id']) {
                $this->error('权限名称已存在');
            }
            if ($this->adminAuthRule->where('name', strtolower($data['name']))->value('id') != $data['id']) {
                $this->error('该URL已存在');
            }
        } else {
            if ($this->adminAuthRule->where('title', $data['name'])->value('id')) {
                $this->error('权限名称已存在');
            }
            if ($this->adminAuthRule->where('name', strtolower($data['name']))->value('id')) {
                $this->error('该URL已存在');
            }
        }
        //模块名验证
        if (!$data['module']) {
            $this->error('模块名称不能为空');
        }
        //父级权限验证
        if (!isset($data['pid'])) {
            $this->error('请选择父级权限');
        }
        return true;
    }

    /**
     * 管理员权限分组列表
     * @return mixed
     * @author:wdx(wdx@ourstu.com)
     */
    public function adminAuthGroupList()
    {
        if ($this->request->isAjax()) {
            $page = input('get.page/d', 1);
            $limit = input('get.limit/d', 20);
            $map[] = ['status', '>=', 0];
            $adminAuthGroupList = $this->adminAuthGroup
                ->where($map)
                ->page($page, $limit)
                ->select()
                ->toArray();
            $groupList = $this->adminAuthGroup
                ->where($map)
                ->column('title', 'id');
            $groupList[0] = '顶级分组';
            $adminGroupType = config('app.admin_group_type');
            foreach ($adminAuthGroupList as &$val) {
                $val['type'] = $adminGroupType[$val['type']];
                to_status($val);
                to_time($val, 'end_time');
                $val['pid'] = $groupList[$val['pid']] . '[' . $val['pid'] . ']';
            }
            unset($val);
            $count = $this->adminAuthGroup->where($map)->count();
            $data = [
                'code' => 0,
                'msg' => '数据返回成功',
                'count' => $count,
                'data' => $adminAuthGroupList
            ];
            AdminLog::setTitle('获取管理员权限分组列表');
            return json($data);
        }
        AdminLog::setTitle('管理员权限分组列表');
        return $this->fetch();
    }

    /**
     * 管理员权限分组表单
     * @return mixed
     * @author:wdx(wdx@ourstu.com)
     */
    public function adminAuthGroupForm()
    {
        if ($this->request->isPost()) {
            $data = input('');
            $this->_checkAdminAuthGroupData($data);
            //转小写
            $data['module'] = strtolower($data['module']);
            //状态
            $data['status'] = (isset($data['status']) ? '1' : '0');
            //有效期
            $data['end_time'] = strtotime($data['end_time']);
            $title = $data['id'] ? '编辑' : '新增';
            if ($data['id']) {     //编辑
                $rs = $this->adminAuthGroup->update($data);
            } else {               //新增
                $rs = $this->adminAuthGroup->insert($data);
            }
            if ($rs) {
                cache('menu', null);
                AdminLog::setTitle($title . '分组权限成功');
                $this->success($title . '分组权限成功');
            } else {
                AdminLog::setTitle($title . '分组权限失败');
                $this->error($title . '分组权限失败');
            }
        } else {
            $id = input('get.id/d', 0);
            $group = $this->adminAuthGroup->where('id', $id)->find();
            $groups = $this->adminAuthGroup->where('pid', 0)->where('status', 1)->column('title', 'id');
            to_time($group, 'end_time', 'Y-m-d');
            $groupType = config('app.admin_group_type');
            $this->assign('groupType', $groupType);
            $this->assign('group', $group);
            $this->assign('groups', $groups);
            AdminLog::setTitle('管理员权限分组表单');
            return $this->fetch();
        }
    }

    /**
     * 权限分组信息验证
     * @param array $data
     * @author:wdx(wdx@ourstu.com)
     */
    private function _checkAdminAuthGroupData($data = [])
    {
        if (!$data['title']) {
            $this->error('请输入分组名称');
        }
        //唯一性校验
        $id = $this->adminAuthGroup->where('title', $data['title'])->value('id');
        if (isset($data['id'])) {
            if ($id && $id != $data['id']) {
                $this->error('分组名称已存在');
            }
        } else {
            if ($id) {
                $this->error('分组名称已存在');
            }
        }
        if (!$data['module']) {
            $this->error('请输入模块名称');
        }
        if (!$data['end_time']) {
            $this->error('请选择有效期限');
        }
        if (!$data['rules']) {
            $this->error('请分配权限');
        }
    }

    /**
     * 获取权限节点树
     * @return Json
     * @author:wdx(wdx@ourstu.com)
     */
    public function getAdminAuthTree()
    {
        $id = input('id/d', 0);
        $rules = $this->adminAuthGroup->where('id', $id)->value('rules');
        if ($rules !== '*') {
            $rules = explode(',', $rules);
        }
        $map[] = ['status', '>=', 0];
        /* 获取所有分类 */
        $list = $this->adminAuthRule->field('id, pid, title')->where($map)->order('sort')->select()->toArray();
        foreach ($list as $key => &$val) {
            $val['name'] = $val['title'];
            unset($val['title']);
            $val['value'] = $val['id'];
            if ($rules === '*' || in_array($val['id'], $rules)) {
                $val['checked'] = true;
            } else {
                $val['checked'] = false;
            }
        }
        unset($val);
        $adminAuth = $this->adminAuthRule->getAuthTree($list);
        $trees = ['trees' => $adminAuth];
        $data = [
            'code' => 0,
            'msg' => '获取成功',
            'data' => $trees
        ];
        return json($data);
    }

    /**
     * 删除管理员分组
     * @author:wdx(wdx@ourstu.com)
     */
    public function delAdminAuthGroup()
    {
        $ids = array_unique(input('post.id/a', []));
        foreach ($ids as $val) {
            if ($val == 1) {
                $this->error('超级管理员分组禁止删除');
            }
            if ($val == $this->roleId) {
                $this->error('禁止删除当前管理员所在分组');
            }
            $rs = $this->adminAuthGroup->where('id', $val)->setField('status', '-1');
        }
        unset($val);
        if ($rs) {
            AdminLog::setTitle('删除管理员分组');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 管理日志
     * @return mixed|Json
     * @author:wdx(wdx@ourstu.com)
     */
    public function adminLog()
    {
        if ($this->request->isAjax()) {
            $page = input('get.page/d', 1);
            $limit = input('get.limit/d', 20);
            $adminLogList = $this->adminLog
                ->page($page, $limit)
                ->order('id desc')
                ->select()
                ->toArray();
            foreach ($adminLogList as &$val) {
                to_ip($val);
            }
            unset($val);
            $count = $this->adminLog->count();
            $data = [
                'code' => 0,
                'msg' => '数据返回成功',
                'count' => $count,
                'data' => $adminLogList
            ];
            AdminLog::setTitle('获取管理日志');
            return json($data);
        }
        AdminLog::setTitle('管理日志');
        return $this->fetch();
    }

    /**
     * 删除管理日志
     * @author:wdx(wdx@ourstu.com)
     */
    public function delAdminLog()
    {
        $ids = array_unique(input('post.id/a', []));
        $rs = $this->adminLog->whereIn('id', $ids)->delete();
        if ($rs) {
            AdminLog::setTitle('删除管理日志');
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}