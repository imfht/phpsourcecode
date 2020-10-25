<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 朱亚杰 <zhuyajie@topthink.net>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;
use think\Db;

/**
 * 权限管理控制器
 * Class AuthManagerController
 * @author 朱亚杰 <zhuyajie@topthink.net>
 */
class AuthManager extends Admin
{


    /**
     * 权限管理首页
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function index()
    {
        //分页
        $list = AuthGroup::where(array('module' => 'admin'))->order("id asc")->paginate(10);
        $page = $list->render();
        //$list = int_to_string($list);
        $this->assign('_list', $list);
        $this->assign('page', $page);
        $this->assign('_use_tip', true);
        $this->meta_title = lang('_PRIVILEGE_MANAGEMENT_');
        return $this->fetch();
    }
    /**
     * 访问授权页面
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function access()
    {
        $this->updateRules();
        $auth_group = db('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' =>
            AuthGroup::TYPE_ADMIN))
            ->column('id,id,title,rules');
        $node_list = $this->returnNodes();
        $map = array('module' => 'admin', 'type' => AuthRule::RULE_MAIN, 'status' => 1);
        $main_rules = db('AuthRule')->where($map)->column('name,id');
        $map = array('module' => 'admin', 'type' => AuthRule::RULE_URL, 'status' => 1);
        $child_rules = db('AuthRule')->where($map)->column('name,id');

        $this->assign('main_rules', $main_rules);
        $this->assign('auth_rules', $child_rules);
        $this->assign('node_list', $node_list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)input('group_id')]);
        $this->meta_title = lang('_ACCESS_AUTHORIZATION_');
        return $this->fetch('');
    }

    /**
     * 后台节点配置的url作为规则存入auth_rule
     * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function updateRules()
    {
        //需要新增的节点必然位于$nodes
        $nodes = $this->returnNodes(false);

        $AuthRule = model('AuthRule');
        $map = array('module' => 'admin', 'type' => array('in', '1,2'));//status全部取出,以进行更新
        //需要更新和删除的节点必然位于$rules
        $rules = $AuthRule->where($map)->order('name')->select();

        //构建insert数据
        $data = array();//保存需要插入和更新的新节点
        foreach ($nodes as $value) {
            $temp['name'] = $value['url'];
            $temp['title'] = $value['title'];
            $temp['module'] = 'admin';
            if ($value['pid'] > 0) {
                $temp['type'] = AuthRule::RULE_URL;
            } else {
                $temp['type'] = AuthRule::RULE_MAIN;
            }
            $temp['status'] = 1;
            $data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp;//去除重复项
        }

        $update = array();//保存需要更新的节点
        $ids = array();//保存需要删除的节点的id
        foreach ($rules as $index => $rule) {
            $key = strtolower($rule['name'] . $rule['module'] . $rule['type']);
            if (isset($data[$key])) {//如果数据库中的规则与配置的节点匹配,说明是需要更新的节点
                $data[$key]['id'] = $rule['id'];//为需要更新的节点补充id值
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
                    $AuthRule->where(array('id' => $row['id']))->update($row);
                }
            }
        }
        if (count($ids)) {
            $AuthRule->where(array('id' => array('IN', implode(',', $ids))))->update(array('status' => -1));
            //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        }
        if (count($data)) {
            $AuthRule->saveAll(array_values($data));
        }
        if ($AuthRule->getError()) {
            trace('[' . __METHOD__ . ']:' . $AuthRule->getError());
            return false;
        } else {
            return true;
        }
    }



    /**
     * 创建管理员用户组
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function createGroup()
    {
        if (empty($this->auth_group)) {
            $this->assign('auth_group', array('title' => null, 'id' => null, 'description' => null, 'rules' => null,));//排除notice信息
        }
        $this->meta_title = L('_NEW_USER_GROUP_');
        $this->display('editgroup');
    }

    /**
     * 编辑管理员用户组
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function editGroup()
    {
        $auth_group = M('AuthGroup')->where(array('module' => 'admin', 'type' => AuthGroupModel::TYPE_ADMIN))
            ->find((int)$_GET['id']);
        $this->assign('auth_group', $auth_group);
        $this->meta_title = L('_EDIT_USER_GROUP_');
        $this->display();
    }




    /**
     * 管理员用户组数据写入/更新
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function writeGroup()
    {
        //获取提交数据
        $rules = array();//接受传过来的数组
        $rules = input('rules/a');
        if (isset($rules)) {
            sort($rules);
            $data['rules'] = implode(',', array_unique($rules));
        }
        $data['module'] = 'admin';
        $data['type'] = AuthGroup::TYPE_ADMIN;
        $AuthGroup = db('AuthGroup');
        //$data = $AuthGroup->create();
        if ($data) {
            $data['id'] = input('post.id');
            $oldGroup = $AuthGroup->find($data['id']);
            $data['rules'] = $this->getMergedRules($oldGroup['rules'], explode(',', $data['rules']), 'eq');
            if (empty($data['id'])) {
                $r = $AuthGroup->insert($data);
            } else {
                $r = $AuthGroup->where('id',$data['id'])->update($data);
            }
            if ($r === false) {
                return ['data'=>'','status'=>true,'info'=>lang('_FAIL_OPERATE_') . $AuthGroup->getError()];
            } else {
                return ['data'=>'','status'=>true,'info'=>lang('_SUCCESS_UPDATE_')];
            }
        } else {
            //$this->error(lang('_FAIL_OPERATE_') . $AuthGroup->getError());
            return ['data'=>'','status'=>true,'info'=>lang('_FAIL_OPERATE_') . $AuthGroup->getError()];
        }
    }

    /**
     * 状态修改
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function changeStatus($method = null)
    {
        if (empty($_REQUEST['id'])) {
            $this->error(L('_PLEASE_CHOOSE_TO_OPERATE_THE_DATA_'));
        }
        switch (strtolower($method)) {
            case 'forbidgroup':
                $this->forbid('AuthGroup');
                break;
            case 'resumegroup':
                $this->resume('AuthGroup');
                break;
            case 'deletegroup':
                $this->delete('AuthGroup');
                break;
            default:
                $this->error($method . L('_ILLEGAL_'));
        }
    }

    /**
     * 用户组授权用户列表
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function user($group_id)
    {
        if (empty($group_id)) {
            $this->error(lang('_PARAMETER_ERROR_'));
        }

        $auth_group = db('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' =>
            AuthGroup::TYPE_ADMIN))
            ->column('id,id,title,rules');
        $prefix = config('DB_PREFIX');
        $l_table = $prefix . (AuthGroup::MEMBER);
        $r_table = $prefix . (AuthGroup::AUTH_GROUP_ACCESS);
        //$model = Db::table('uctoo_member m')->join('uctoo_auth_group_access a' , 'm.uid=a.uid');
        $_REQUEST = array();
        //分页列表
        $list = Db::table('uctoo_member m')->join('uctoo_auth_group_access a' , 'm.uid=a.uid')->where(array('a.group_id' => $group_id, 'm.status' => array('egt', 0)))->order("m.uid asc")->paginate(10);
        $page = $list->render();
        //$list = $this->lists($model, array('a.group_id' => $group_id, 'm.status' => array('egt', 0)), 'm.uid asc',
        //null, 'm.uid,m.nickname,m.last_login_time,m.last_login_ip,m.status');
        //int_to_string($list);
        $this->assign('_list', $list);
        $this->assign('_page', $page);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $auth_group[(int)input('group_id')]);
        $this->meta_title = lang('_MEMBER_AUTHORITY_');
        return $this->fetch();
    }



    public function tree($tree = null)
    {
        $this->assign('tree', $tree);
        $this->display('tree');
    }

    /**
     * 将用户添加到用户组的编辑页面
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function group()
    {
        $uid = input('uid');
        $auth_groups = D('AuthGroup')->getGroups();
        $user_groups = AuthGroupModel::getUserGroup($uid);
        $ids = array();
        foreach ($user_groups as $value) {
            $ids[] = $value['group_id'];
        }
        $nickname = D('Member')->getNickName($uid);
        $this->assign('nickname', $nickname);
        $this->assign('auth_groups', $auth_groups);
        $this->assign('user_groups', implode(',', $ids));
        $this->display();
    }

    /**
     * 将用户添加到用户组,入参uid,group_id
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function addToGroup()
    {
        $uid = input('uid');
        $gid = input('group_id');
        if (empty($uid)) {
            $this->error(lang('_PARAMETER_IS_INCORRECT_'));
        }
        $AuthGroup = model('AuthGroup');
        if (is_numeric($uid)) {
            if (is_administrator($uid)) {
                $this->error(lang('_THE_USER_IS_A_SUPER_ADMINISTRATOR_'));
            }
            if (!db('Member')->where(array('uid' => $uid))->find()) {
                $this->error(lang('_ADMIN_USER_DOES_NOT_EXIST_'));
            }
        }

        if ($gid && !AuthGroup::checkGroupId($gid)) {
            $this->error($AuthGroup->error);
        }
        if ($this->addToGroup($uid, $gid)) {
            $this->success(lang('_SUCCESS_OPERATE_'));
        } else {
            $this->error($AuthGroup->getError());
        }
    }

    /**
     * 将用户从用户组中移除  入参:uid,group_id
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function removeFromGroup()
    {
        $uid = input('uid');
        $gid = input('group_id');
        if ($uid == UID) {
            $this->error(lang('_NOT_ALLOWED_TO_RELEASE_ITS_OWN_AUTHORITY_'));
        }
        if (empty($uid) || empty($gid)) {
            $this->error(lang('_PARAMETER_IS_INCORRECT_'));
        }
        $AuthGroup = db('AuthGroup');
        if (!$AuthGroup->find($gid)) {
            $this->error(lang('_USER_GROUP_DOES_NOT_EXIST_'));
        }
        if ($this->removeFromGroup($uid, $gid)) {
            $this->success(lang('_SUCCESS_OPERATE_'));
        } else {
            $this->error(lang('_FAIL_OPERATE_'));
        }
    }

    /**
     * 将分类添加到用户组  入参:cid,group_id
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function addToCategory()
    {
        $cid = input('cid');
        $gid = input('group_id');
        if (empty($gid)) {
            $this->error(L('_PARAMETER_IS_INCORRECT_'));
        }
        $AuthGroup = D('AuthGroup');
        if (!$AuthGroup->find($gid)) {
            $this->error(L('_USER_GROUP_DOES_NOT_EXIST_'));
        }
        if ($cid && !$AuthGroup->checkCategoryId($cid)) {
            $this->error($AuthGroup->error);
        }
        if ($AuthGroup->addToCategory($gid, $cid)) {
            $this->success(L('_SUCCESS_OPERATE_'));
        } else {
            $this->error(L('_FAIL_OPERATE_'));
        }
    }

    /**
     * 将模型添加到用户组  入参:mid,group_id
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    public function addToModel()
    {
        $mid = input('id');
        $gid = input('get.group_id');
        if (empty($gid)) {
            $this->error(L('_PARAMETER_IS_INCORRECT_'));
        }
        $AuthGroup = D('AuthGroup');
        if (!$AuthGroup->find($gid)) {
            $this->error(L('_USER_GROUP_DOES_NOT_EXIST_'));
        }
        if ($mid && !$AuthGroup->checkModelId($mid)) {
            $this->error($AuthGroup->error);
        }
        if ($AuthGroup->addToModel($gid, $mid)) {
            $this->success(L('_SUCCESS_OPERATE_'));
        } else {
            $this->error(L('_FAIL_OPERATE_'));
        }
    }

    //前台管理页面新增节点
    public function addNode()
    {
        if (empty($this->auth_group)) {
            $this->assign('auth_group', array('title' => null, 'id' => null, 'description' => null, 'rules' => null,));//排除notice信息
        }
        if (request()->isPost()) {
            $Rule = db('AuthRule');
            $data = input('post.');
            if ($data) {
                if (intval($data['id']) == 0) {
                    $id = $Rule->insert($data);
                } else {
                    $Rule->update($data);
                    $id = $data['id'];
                }

                if ($id) {
                    // S('DB_CONFIG_DATA',null);
                    //记录行为
                    return ['data'=>'','status'=>true,'info'=>lang('_SUCCESS_EDIT_')];
                } else {
                    return ['status'=>true,'info'=>lang('_EDIT_FAILED_')];
                }
            } else {
                $this->error($Rule->getError());
            }
        } else {
            $aId = input('id', 0, 'intval');//获取提交的节点ID，没有就为0
            if ($aId == 0) {
                $info['module']=input('module','','op_t');//获取节点名字
            }else{
                $info = db('AuthRule')->find($aId);
            }

            $this->assign('info', $info);
            //  $this->assign('info', array('pid' => input('pid')));
            $modules = model('Common/Module')->getAll();
            $this->assign('Modules', $modules);
            $this->meta_title = lang('_NEW_FRONT_DESK_RIGHT_NODE_');
            return $this->fetch();
        }

    }
    //删除节点
    public function deleteNode(){
        $aId=input('id',0,'intval');
        if($aId>0){
            $result=   db('AuthRule')->where(array('id'=>$aId))->delete();
            if($result){
                $this->success(lang('_DELETE_SUCCESS_'));
            }else{
                $this->error(lang('_DELETE_FAILED_'));
            }
        }else{
            $this->error(lang('_YOU_MUST_SELECT_THE_NODE_'));
        }
    }

//前台权限
    public function accessUser()
    {
        $aId = input('get.group_id', 0, 'intval');

        if (request()->isPost()) {
            $aId = input('id', 0, 'intval');
            $aOldRule = input('post.old_rules', '', 'text');
            $aRules = input('post.rules', array());
            $rules = $this->getMergedRules($aOldRule, $aRules);
            $authGroupModel = db('AuthGroup');
            $group = $authGroupModel->find($aId);
            $group['rules'] = $rules;
            $result = $authGroupModel->update($group);
            if ($result) {
                return ['data'=>'','status'=>true,'info'=>lang('_RIGHT_TO_SAVE_SUCCESS_')];
            } else {
                return ['data'=>'','status'=>true,'info'=>lang('_RIGHT_SAVE_FAILED_')];
            }

        }
        $this->updateRules();
        $auth_group = db('AuthGroup')->where(array('status' => array('egt', '0'), 'type' => AuthGroup::TYPE_ADMIN))
            ->column('id,id,title,rules');
        $node_list = $this->getNodeListFromModule(model('Common/Module')->getAll());

        $map = array('module' => array('neq', 'admin'), 'type' => AuthRule::RULE_MAIN, 'status' => 1);
        $main_rules = db('AuthRule')->where($map)->column('name,id');
        $map = array('module' => array('neq', 'admin'), 'type' => AuthRule::RULE_URL, 'status' => 1);
        $child_rules = db('AuthRule')->where($map)->column('name,id');

        $group = db('AuthGroup')->find($aId);
        $this->assign('main_rules', $main_rules);
        $this->assign('auth_rules', $child_rules);
        $this->assign('node_list', $node_list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $group);

        $this->meta_title = lang('_USER_FRONT_DESK_AUTHORIZATION_');
        return $this->fetch('');
    }

    private function getMergedRules($oldRules, $rules, $isAdmin = 'neq')
    {
        $map = array('module' => array($isAdmin, 'admin'), 'status' => 1);
        $otherRules = db('AuthRule')->where($map)->field('id')->select();
        $oldRulesArray = explode(',', $oldRules);
        $otherRulesArray = getSubByKey($otherRules, 'id');

        //1.删除全部非Admin模块下的权限，排除老的权限的影响
        //2.合并新的规则
        foreach ($otherRulesArray as $key => $v) {
            if (in_array($v, $oldRulesArray)) {
                $key_search = array_search($v, $oldRulesArray);
                if ($key_search !== false)
                    array_splice($oldRulesArray, $key_search, 1);
            }
        }

        return str_replace(',,', ',', implode(',', array_unique(array_merge($oldRulesArray, $rules))));


    }

    //预处理规则，去掉未安装的模块
    public function getNodeListFromModule($modules)
    {
        $node_list = array();
        foreach ($modules as $module) {
            if ($module['is_setup']) {

                $node = array('name' => $module['name'], 'alias' => $module['alias']);
                $map = array('module' => $module['name'], 'type' => AuthRule::RULE_URL, 'status' => 1);

                $node['child'] = db('AuthRule')->where($map)->select();
                $node_list[] = $node;
            }

        }
        return $node_list;
    }
}
