<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use app\admin\model\AuthRule;
use app\admin\model\AuthGroup;
use think\Db;

/**
 * 权限管理控制器
 */
class AuthManager extends Admin
{
    /**
     * 权限管理首页
     */
    public function index()
    {   
        $map['module'] = 'admin';
        $map['status'] = ['>',-1];
        $list = Db::name('AuthGroup')->where($map)->order('id asc')->select();
        $list = int_to_string($list);

        $this->setTitle(lang('_PRIVILEGE_MANAGEMENT_'));
        $this->assign('_list', $list);
        $this->assign('_use_tip', true);
        
        
        return $this->fetch();
    }

    /**
     * 创建管理员用户组
     */
    public function createGroup()
    {
        if (empty($this->auth_group)) {
            $this->assign('auth_group', array('title' => null, 'id' => null, 'description' => null, 'rules' => null,));//排除notice信息
        }
        $this->meta_title = lang('_NEW_USER_GROUP_');
        return $this->fetch('edit_group');
    }

    /**
     * 编辑管理员用户组
     */
    public function editGroup()
    {
        $id = input('id',0,'intval');
        $auth_group = Db::name('AuthGroup')->where(['module' => 'admin', 'type' => AuthGroup::TYPE_ADMIN])
            ->find((int)$id);

        $this->assign('auth_group', $auth_group);
        $this->meta_title = lang('_EDIT_USER_GROUP_');
        return $this->fetch();
    }

    /**
     * 管理员用户组数据写入/更新
     */
    public function writeGroup()
    {
        $data = input();
        if (isset($data['rules']) && !empty($data['rules'])) {
            sort($data['rules']);
            $data['rules'] = implode(',', array_unique($data['rules']));
        }
        $data['module'] = 'admin';
        $data['type'] = AuthGroup::TYPE_ADMIN;
        $AuthGroup = model('admin/AuthGroup');
        
        if ($data) {
            empty($data['rules'])&&$data['rules'] = '';
            $group = Db::name('AuthGroup')->find($data['id']);
            $old_rules = array_filter(explode(',',$group['rules']));
            $data['rules'] = $this->getMergedRules($old_rules, explode(',', $data['rules']), 'eq');
            
            $r = $AuthGroup->editData($data);
            if ($r === false) {
                $this->error(lang('_FAIL_OPERATE_') . $AuthGroup->getError());
            } else {
                $back_url = url('AuthManager/index');
                if(!empty($data['back_url'])){
                    $back_url = $data['back_url'];
                }
                $this->success('操作成功!',$back_url);
            }
        } else {
            $this->error(lang('_FAIL_OPERATE_') . $AuthGroup->getError());
        }
    }

    /**
     * 状态修改
     */
    public function changeStatus($method = null)
    {

        if (empty(input('id/a'))) {
            $this->error(lang('_PLEASE_CHOOSE_TO_OPERATE_THE_DATA_'));
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
                $this->error($method . lang('_ILLEGAL_'));
        }
    }

    /**
     * 用户组授权用户列表
     * @author 大蒙 <59262424@qq.com>
     */
    public function user()
    {
        $group_id = input('group_id', 1, 'intval');
        if (empty($group_id)) {
            $this->error(lang('_PARAMETER_ERROR_'));
        }

        $auth_group = Db::name('AuthGroup')->where(array('status' => array('egt', '0'), 'module' => 'admin', 'type' => AuthGroup::TYPE_ADMIN))
            ->field('id,title,rules')->select();

        $prefix = config('database.prefix');
        $l_table = $prefix . (AuthGroup::MEMBER);
        $r_table = $prefix . (AuthGroup::AUTH_GROUP_ACCESS);
        $where = [
            'a.group_id'=>$group_id,
            'status'=>['>=',0]
        ];
        $list = Db::table($l_table . ' m')->join($r_table . ' a ',' m.uid=a.uid')->where($where)->order('m.uid desc')
            ->paginate(20,false,[
                
                'query'=> ['group_id'=>$group_id],

            ]);
        // 获取分页显示
        $page = $list->render();
        // 转数组
        $list = $list->toArray()['data'];
        int_to_string($list);

        $this->setTitle(lang('_MEMBER_AUTHORITY_'));
        
        $this->assign('_list', $list);
        $this->assign('page', $page);
        $this->assign('auth_group', $auth_group);
        $this->assign('group_id', $group_id);
        
        return $this->fetch();
    }



    public function tree($tree = null)
    {
        $this->assign('tree', $tree);
        return $this->fetch('tree');
    }

    /**
     * 将用户添加到用户组的编辑页面
     */
    public function group()
    {
        $uid = input('uid',0,'intval');
        $auth_groups = model('AuthGroup')->getGroups();
        $user_groups = AuthGroup::getUserGroup($uid);
        $ids = [];
        foreach ($user_groups as $value) {
            $ids[] = $value['group_id'];
        }
        $nickname = model('Member')->getNickName($uid);

        $this->assign('nickname', $nickname);
        $this->assign('auth_groups', $auth_groups);
        $this->assign('user_groups', implode(',', $ids));

        return $this->fetch();
    }

    /**
     * 将用户添加到用户组,入参uid,group_id
     */
    public function addToGroup()
    {
        $uid = input('post.uid');
        $gid = input('post.group_id/a');
        if (empty($uid)) {
            $this->error(lang('_PARAMETER_IS_INCORRECT_'));
        }
        $AuthGroup = model('AuthGroup');
        if (is_numeric($uid)) {
            if (is_administrator($uid)) {
                $this->error(lang('_THE_USER_IS_A_SUPER_ADMINISTRATOR_'));
            }
            if (!Db::name('Member')->where(array('uid' => $uid))->find()) {
                $this->error(lang('_ADMIN_USER_DOES_NOT_EXIST_'));
            }
        }

        if ($gid && !$AuthGroup->checkGroupId($gid)) {
            $this->error($AuthGroup->error);
        }
        if ($AuthGroup->addToGroup($uid, $gid)) {
            $this->success(lang('_SUCCESS_OPERATE_'));
        } else {
            $this->error($AuthGroup->getError());
        }
    }

    /**
     * 将用户从用户组中移除  入参:uid,group_id
     */
    public function removeFromGroup()
    {
        $uid = input('uid');
        $gid = input('group_id');
        if ($uid == is_login()) {
            $this->error(lang('_NOT_ALLOWED_TO_RELEASE_ITS_OWN_AUTHORITY_'));
        }
        if (empty($uid) || empty($gid)) {
            $this->error(lang('_PARAMETER_IS_INCORRECT_'));
        }
        $AuthGroup = model('AuthGroup');

        if (!$AuthGroup->find($gid)) {
            $this->error(lang('_USER_GROUP_DOES_NOT_EXIST_'));
        }
        if ($AuthGroup->removeFromGroup($uid, $gid)) {
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
            $this->error(lang('_PARAMETER_IS_INCORRECT_'));
        }
        $AuthGroup = model('AuthGroup');
        if (!$AuthGroup->find($gid)) {
            $this->error(lang('_USER_GROUP_DOES_NOT_EXIST_'));
        }
        if ($cid && !$AuthGroup->checkCategoryId($cid)) {
            $this->error($AuthGroup->error);
        }
        if ($AuthGroup->addToCategory($gid, $cid)) {
            $this->success(lang('_SUCCESS_OPERATE_'));
        } else {
            $this->error(lang('_FAIL_OPERATE_'));
        }
    }

    /**
     * 新增权限节点
     */
    public function addNode()
    {
        if (empty($this->auth_group)) {
            $this->assign('auth_group', array('title' => null, 'id' => null, 'description' => null, 'rules' => null,));//排除notice信息
        }
        
        if (request()->isPost()) {
            
            $data = input('post.');
            $Rule = Db::name('AuthRule');

            if ($data) {
                if (intval($data['id']) == 0) {
                    unset($data['id']);
                    $id = Db::name('AuthRule')->insert($data);
                } else {
                    $id = Db::name('AuthRule')->update($data);
                }

                if ($id) {
                    //记录行为
                    $this->success(lang('_SUCCESS_EDIT_'),url('AuthManager/accessUser?group_id='.input('get.group_id')));
                } else {
                    $this->error(lang('_EDIT_FAILED_'));
                }
            } else {
                $this->error($Rule->getError());
            }
        } else {
            $aId = input('id', 0, 'intval');
            if ($aId == 0) {
                $info['module']=input('module','','text');
            }else{
                $info = Db::name('AuthRule')->find($aId);
            }

            $this->assign('info', $info);

            $modules = model('common/Module')->getAll();
            $this->assign('Modules', $modules);
            $this->setTitle(lang('_NEW_FRONT_DESK_RIGHT_NODE_'));
            return $this->fetch();
        }

    }

    public function deleteNode(){
        $aId=input('id',0,'intval');
        if($aId>0){
            $result=   Db::name('AuthRule')->where(array('id'=>$aId))->delete();
            if($result){
                $this->success(lang('_DELETE_SUCCESS_'));
            }else{
                $this->error(lang('_DELETE_FAILED_'));
            }
        }else{
            $this->error(lang('_YOU_MUST_SELECT_THE_NODE_'));
        }
    }
    /**
     * 访问授权页面
     */
    public function access()
    {
        $group_id = input('group_id', 0, 'intval');
        $this->updateRules();

        $auth_group = Db::name('AuthGroup')->where(['status' => ['egt', '0'], 'module' => 'admin', 'type' => AuthGroup::TYPE_ADMIN])
            ->field('id,title,rules')->select();
        $node_list = $this->returnNodes();
        
        $map = ['module' => 'admin', 'type' => AuthRule::RULE_MAIN, 'status' => 1];
        $main_rules = Db::name('AuthRule')->where($map)->column('name,id');
        $map = ['module' => 'admin', 'type' => AuthRule::RULE_URL, 'status' => 1];
        $child_rules = Db::name('AuthRule')->where($map)->column('name,id');

        $this->assign('main_rules', $main_rules);
        $this->assign('auth_rules', $child_rules);

        $group = Db::name('AuthGroup')->find($group_id);
        $this->setTitle(lang('_ACCESS_AUTHORIZATION_'));

        $this->assign('node_list', $node_list);

        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $group);
        
        return $this->fetch('');
    }

    /**
     * 前台授权
     * @return [type] [description]
     */
    public function accessUser()
    {
        $group_id = input('group_id', 0, 'intval');

        if (request()->isPost()) {
            $group_id = input('group_id', 0, 'intval');
            $rules = input('post.rules/a', array());
            
            $group = Db::name('AuthGroup')->find($group_id);
            $old_rules = array_filter(explode(',',$group['rules']));
            $new_rules = $rules;
            $rules = $this->getMergedRules($old_rules,$new_rules);
            $group['rules'] = $rules;
            
            $result = Db::name('AuthGroup')->update($group);
            if ($result) {
                $this->success(lang('_RIGHT_TO_SAVE_SUCCESS_'),url('accessUser',['group_id'=>$group_id]));
            } else {
                $this->error(lang('_RIGHT_SAVE_FAILED_'));
            }
        }

        $this->updateRules();
        $auth_group = Db::name('AuthGroup')->where(['status' => ['egt', '0'], 'type' => AuthGroup::TYPE_ADMIN])
            ->field('id,id,title,rules')->select();

        $node_list = $this->getNodeListFromModule(model('common/Module')->getAll());

        $map = array('module' => array('neq', 'admin'), 'type' => AuthRule::RULE_MAIN, 'status' => 1);
        $main_rules = Db::name('AuthRule')->where($map)->field('name,id')->select();
        $map = array('module' => array('neq', 'admin'), 'type' => AuthRule::RULE_URL, 'status' => 1);
        $child_rules = Db::name('AuthRule')->where($map)->field('name,id')->select();

        $group = Db::name('AuthGroup')->find($group_id);

        $this->setTitle(lang('_USER_FRONT_DESK_AUTHORIZATION_'));
        $this->assign('main_rules', $main_rules);
        $this->assign('auth_rules', $child_rules);
        $this->assign('node_list', $node_list);
        $this->assign('auth_group', $auth_group);
        $this->assign('this_group', $group);

        return $this->fetch();
    }

    private function getMergedRules($oldRules = [], $rules = [], $isAdmin = 'neq')
    {
        $map = ['module' => [$isAdmin, 'admin'], 'status' => 1];
        $otherRules = Db::name('AuthRule')->where($map)->field('id')->select();
        
        $otherRulesArray = getSubByKey($otherRules, 'id');

        //1.删除全部非Admin模块下的权限，排除老的权限的影响
        //2.合并新的规则
        foreach ($otherRulesArray as $key => $v) {
            if (in_array($v, $oldRules)) {
                $key_search = array_search($v, $oldRules);
                if ($key_search !== false)
                    array_splice($oldRules, $key_search, 1);
            }
        }

        return str_replace(',,', ',', implode(',', array_unique(array_merge($oldRules, $rules))));
    }


    //预处理规则，去掉未安装的模块
    public function getNodeListFromModule($modules)
    {
        $node_list = array();
        foreach ($modules as $module) {
            if ($module['is_setup']) {

                $node = array('name' => $module['name'], 'alias' => $module['alias']);
                $map = array('module' => $module['name'], 'type' => AuthRule::RULE_URL, 'status' => 1);

                $node['child'] = Db::name('AuthRule')->where($map)->select();
                $node_list[] = $node;
            }

        }
        return $node_list;
    }

    /**
     * 后台节点配置的url作为规则存入auth_rule
     * 执行新节点的插入,已有节点的更新,无效规则的删除三项任务
     */
    public function updateRules()
    {
        //需要新增的节点必然位于$nodes
        $nodes = $this->returnNodes(false);

        $AuthRule = Db::name('AuthRule');
        $map = ['module' => 'admin', 'type' => ['in', '1,2']];//status全部取出,以进行更新
        //需要更新和删除的节点必然位于$rules
        $rules = $AuthRule->where($map)->order('name')->select();

        //构建insert数据
        $data = [];//保存需要插入和更新的新节点
        foreach ($nodes as $value) {
            $temp['name'] = $value['url'];
            $temp['title'] = $value['title'];
            $temp['module'] = 'admin';
            if ($value['pid'] > 0 || $value['pid']!=='0') {
                $temp['type'] = AuthRule::RULE_URL;
            } else {
                $temp['type'] = AuthRule::RULE_MAIN;
            }
            $temp['status'] = 1;
            $data[strtolower($temp['name'] . $temp['module'] . $temp['type'])] = $temp;//去除重复项
        }

        $update = [];//保存需要更新的节点
        $ids = [];//保存需要删除的节点的id
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
                    $AuthRule->where(['id' => $row['id']])->update($row);
                }
            }
        }
        if (count($ids)) {
            $AuthRule->where(['id' => ['IN', implode(',', $ids)]])->update(['status' => -1]);
            //删除规则是否需要从每个用户组的访问授权表中移除该规则?
        }
        if (count($data)) {
            $AuthRule->insertAll(array_values($data));
        }
        return true;
    }


    /**
     * 返回后台节点数据
     * @param boolean $tree 是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com> 大蒙<59262424@qq.com> 更新
     */
    protected function returnNodes($tree = true)
    {
        static $tree_nodes = array();
        if ($tree && !empty($tree_nodes[(int)$tree])) {
            return $tree_nodes[$tree];
        }
        if ($tree) {
            $list = Db::name('Menu')->field('id,pid,title,url,tip,hide,module')->order('sort asc')->select();
            foreach ($list as &$value) {
                $value = $this->check_url_re($value);
                unset($value['module']);
            }
            unset($value);
            //由于menu表id更改为字符串格式，root必须设置成字符串0
            $nodes = list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'operator', $root = '0');

            foreach ($nodes as $key => $value) {
                if (!empty($value['operator'])) {
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }

        } else {
            $nodes = Db::name('Menu')->field('title,url,tip,pid,module')->order('sort asc')->select();
            foreach ($nodes as &$value) {
                $value = $this->check_url_re($value);
                unset($value['module']);
            }
            unset($value);
        }

        $tree_nodes[(int)$tree] = $nodes;
        return $nodes;
    }

    public function check_url_re( $value = array() ){

        if(empty($value['module']) || $value['module'] == ''){
            if (stripos($value['url'], request()->module()) !== 0) {
                $value['url'] = request()->module() . '/' . $value['url'];
            }
        }

        return $value;
    }

}
