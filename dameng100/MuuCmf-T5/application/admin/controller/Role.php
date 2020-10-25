<?php
namespace app\admin\controller;

use app\admin\controller\Admin;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminSortBuilder;
use app\admin\builder\AdminConfigBuilder;
use think\Db;

/**
 * 后台身份控制器
 */
class Role extends Admin
{
    protected $roleModel;
    protected $userRoleModel;
    protected $roleConfigModel;
    protected $roleGroupModel;

    public function _initialize()
    {
        parent::_initialize();
        $this->roleModel = model("admin/Role");
    }

    //身份基本信息及配置 start

    public function index($r = 20)
    {
        $map['status'] = array('egt', 0);

        list($roleList,$page) = $this->commonLists('Role', $map, 'sort acs');
        
        $roleList = $roleList->toArray()['data'];

        $map_group['id'] = ['in', array_column($roleList, 'group_id')];

        $group = Db::name('RoleGroup')->where($map_group)->field('id,title')->select();
        $group = array_combine(array_column($group, 'id'), $group);

        $authGroupList = Db::name('AuthGroup')->where(array('status' => 1))->field('id,title')->select();
        $authGroupList = array_combine(array_column($authGroupList, 'id'), array_column($authGroupList, 'title'));

        foreach ($roleList as &$val) {

            $user_groups = explode(',', $val['user_groups']);
            if($val['group_id'] && !empty($group)){
                $val['group'] = $group[$val['group_id']]['title'];
            }
            
            if(!empty($user_groups)){
                foreach ($user_groups as &$vl) {
                    $vl = $authGroupList[$vl];
                }
                unset($vl);
            }
            
            $val['user_groups'] = implode(',', $user_groups);
        }
        unset($val);


        $builder = new AdminListBuilder;
        $builder->title(lang('_IDENTITY_LIST_'));
        $builder
        ->buttonNew(url('Role/editRole'))
        ->setStatusUrl(url('DoSetStatus'))
        ->buttonEnable()
        ->buttonDisable()
        ->button(lang('_DELETE_'), array('class' => 'btn btn-danger ajax-post confirm', 'url' => Url('doSetStatus', array('status' => -1)), 'target-form' => 'ids', 'confirm-info' => "确认删除身份？删除后不可恢复！"))
        ->buttonSort(url('sort'));

        $builder->keyId()
            ->keyText('title', lang('_ROLE_NAME_'))
            ->keyText('name', lang('_ROLE_MARK_'))
            ->keyText('group', lang('_GROUP_'))
            ->keyText('description', lang('_DESCRIPTION_'))
            ->keyText('user_groups', lang('_DEFAULT_USER_GROUP_'))
            ->keytext('sort', lang('_SORT_'))
            ->keyYesNo('audit', lang('_REGISTRATION_WILL_NEED_TO_AUDIT_'))
            ->keyStatus()
            ->keyCreateTime()
            ->keyUpdateTime()
            ->keyDoActionEdit('Role/editRole?id=###')
            ->keyDoAction('Role/configScore?id=###', lang('_DEFAULT_INFORMATION_CONFIGURATION_'))
            ->data($roleList)
            ->page($page)
            ->display();
    }

    /**
     * 编辑身份
     */
    public function editRole()
    {
        $aId = input('id', 0, 'intval');
        $is_edit = $aId ? 1 : 0;
        $title = $is_edit ? lang('_EDIT_IDENTITY_') : lang('_NEW_IDENTITY_');
        if (request()->isPost()) {
            $data = input('');

            if(empty($data['title'])){
                $this->error('身份名不能为空');
            }

            if(empty($data['name'])){
                $this->error('英文标识不能为空');
            }

            if (!empty($data['user_groups'])) {
                $data['user_groups'] = implode(',', $data['user_groups']);
            }else{
                $this->error('默认权限组不能为空');
            }

            $result = $this->roleModel->editData($data);
            
            if ($result) {
                $this->success($title . lang('_SUCCESS_'), url('Role/index'));
            } else {
                $error_info = $this->roleModel->getError();
                $this->error($title . lang('_FAILURE!__') . $error_info);
            }
        } else {
            
            $data['status'] = 1;
            $data['invite'] = 0;
            $data['audit'] = 0;
            if ($is_edit) {

                $data = $this->roleModel->getByMap(array('id' => $aId));

                $data['user_groups']=explode(',',$data['user_groups']);
            }

            $authGroupList = Db::name('AuthGroup')->where(array('status' => 1))->field('id,title')->select(); //用户组列表

            $group = Db::name('RoleGroup')->field('id,title')->select();

            $group = array_combine(array_column($group, 'id'), array_column($group, 'title'));
            if (!$group) {
                $group = array(0 => lang('_NO_GROUP_'));
            } else {
                $group = array_merge(array(0 => lang('_NO_GROUP_')), $group);
            }
            $builder = new AdminConfigBuilder;

            $builder
                ->title($title)
                ->keyId()
                ->keyText('title', lang('_ROLE_NAME_'), lang('_CANT_REPEAT_'))
                ->keyText('name', lang('_ENGLISH_LOGO_'), lang('_COMPOSED_BY_ABC_'))
                ->keyTextArea('description', lang('_DESCRIPTION_'))
                ->keySelect('group_id', lang('_GROUP_'), '', $group)
                ->keyChosen('user_groups', lang('_DEFAULT_USER_GROUP_'), lang('_THE_DEFAULT_USER_REGISTRATION_WHERE_THE_USER_GROUP_CHOOSE_'), $authGroupList)

                ->keyRadio('invite', lang('_NEED_TO_BE_INVITED_TO_REGISTER_'), lang('_DEFAULT_IS_OFF_AFTER_OPENING_THE_USER_CAN_BE_INVITED_TO_REGISTER_'), [1 => lang('_OPEN_'), 0 => lang('_OFF_')])

                ->keyRadio('audit', lang('_NEED_TO_EXAMINE_'), lang('_DEFAULT_IS_CLOSED_AFTER_THE_USER_AUDIT_TO_HAVE_THE_IDENTITY_OF_THE_'), array(1 => lang('_OPEN_'), 0 => lang('_OFF_')))
                
                ->keyStatus()
                ->data($data)
                ->buttonSubmit(Url('editRole'))
                ->buttonBack()
                ->display();
        }
    }

    /**
     * 对身份进行排序
     */
    public function sort($ids = null)
    {
        if (request()->isPost()) {

            $builder = new AdminSortBuilder;
            $builder->doSort('Role', $ids);
        } else {
            $map['status'] = ['egt', 0];

            $list = $this->roleModel->selectByMap($map, 'sort asc', 'id,title,sort');
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['title'];
            }
            $builder = new AdminSortBuilder;
            $builder->title(lang('_IDENTITY_SORT_'));
            $builder->data($list);
            $builder
                ->buttonSubmit(Url('sort'))
                ->buttonBack();
            $builder->display();
        }
    }

    /**
     * 身份状态设置
     * @param mixed|string $ids
     * @param $status
     */
    public function doSetStatus($ids, $status)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        if(in_array(1,$ids)){
            $this->error(lang('_ID_1_PRIORITY_'));
        }
        if ($status == 1) {
            $builder = new AdminListBuilder;
            $builder->doSetStatus('Role', $ids, $status);
        } else if ($status == 0) {
            $result = $this->checkSingleRoleUser($ids);
            if ($result['status']) {
                $builder = new AdminListBuilder;
                $builder->doSetStatus('Role', $ids, $status);
            } else {
                $this->error(lang('_IDENTITY_') . $result['role']['name'] . '（' . $result["role"]["id"] . '）【' . $result["role"]["title"] . '】中存在单身份用户，移出单身份用户后才能禁用该身份！');
            }
        } else if ($status == -1) { //（真删除）
            $result = $this->checkSingleRoleUser($ids);
            if ($result['status']) {
                $result = Db::name('Role')->where(['id' => ['in', $ids]])->delete();
                if ($result) {
                    $userRoleList=Db::name('UserRole')->where(['role_id'=>['in',$ids]])->select();
                    foreach($userRoleList as $val){
                        $this->setDefaultShowRole($val['role_id'],$val['uid']);
                    }
                    unset($val);
                    Db::name('UserRole')->where(['role_id'=>['in',$ids]])->delete();
                    $this->success(lang('_DELETE_SUCCESS_'), Url('Role/index'));
                } else {
                    $this->error(lang('_DELETE_FAILED_'));
                }
            } else {
                $this->error(lang('_IDENTITY_') . $result['role']['name'] . '（' . $result["role"]["id"] . '）【' . $result["role"]["title"] . '】中存在单身份用户，移出单身份用户后才能删除该身份！');
            }
        }
    }
    

    /**
     * 检测要删除的身份中是否存在单身份用户
     * @param $ids 要删除的身份ids
     * @return mixed
     */
    private function checkSingleRoleUser($ids)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);

        $user_ids=Db::name('Member')->where(array('status'=>-1))->field('uid')->select();
        $user_ids=array_column($user_ids,'uid');

        $error_role_id = 0; //出错的身份id
        foreach ($ids as $role_id) {
            //获取拥有该身份的用户ids
            $uids = Db::name('UserRole')->where(['role_id' => $role_id])->field('uid')->select();
            $uids=array_column($uids,'uid');
            if(count($user_ids)){
                $uids=array_diff($uids,$user_ids);
            }
            if (count($uids) > 0) { //拥有该身份
                $uids = array_unique($uids);
                //获取拥有其他身份的用户ids
                $have_uids = Db::name('UserRole')->where(['role_id' => ['not in', $ids], 'uid' => ['in', $uids]])->field('uid')->select();
                if ($have_uids) {
                    $have_uids=array_column($have_uids,'uid');
                    $have_uids = array_unique($have_uids);

                    //获取不拥有其他身份的用户ids
                    $not_have = array_diff($uids, $have_uids);
                    if (count($not_have) > 0) {
                        $error_role_id = $role_id;
                        break;
                    }
                } else {
                    $error_role_id = $role_id;
                    break;
                }
            }
        }
        unset($role_id, $uids, $have_uids, $not_have);

        $result['status'] = 1;
        if ($error_role_id) {
            $result['role'] = $this->roleModel->where(array('id' => $error_role_id))->field('id,name,title')->find();
            $result['status'] = 0;
        }
        return $result;
    }

    //身份基本信息及配置 end

    //身份分组 start
    /**
     * 分组列表
     */
    public function group()
    {
        $group = Db::name('RoleGroup')->field('id,title,update_time')->select();

        foreach ($group as &$val) {
            $map['group_id'] = $val['id'];
            $roles = $this->roleModel->selectByMap($map, 'id asc', 'title');
            $val['roles'] = implode(',', array_column($roles, 'title'));
        }
        unset($roles, $val);

        $builder = new AdminListBuilder;
        $builder
            ->title(lang('_ROLE_GROUP_2_'))
            ->suggest(lang('_ROLE_EXCLUSION_ONE_GROUP_'))
            ->buttonNew(Url('Role/editGroup'))
            ->buttonDeleteTrue(Url('Role/deleteGroup'))
            ->keyId()
            ->keyText('title', lang('_TITLE_'))
            ->keyText('roles', lang('_GROUP_IDENTITY_'))
            ->keyUpdateTime()
            ->keyDoActionEdit('Role/editGroup?id=###')
            ->keyDoAction('Role/deleteGroup?ids=###', lang('_DELETE_'))
            ->data($group)
            ->display();
    }

    /**
     * 编辑分组
     */
    public function editGroup()
    {
        $aGroupId = input('id', 0, 'intval');
        $is_edit = $aGroupId ? 1 : 0;
        $title = $is_edit ? lang('_EDIT_GROUP_') : lang('_NEW_GROUP_');

        if (request()->isPost()) {
            $data['title'] = input('post.title', '', 'text');
            $data['update_time'] = time();
            $roles = input('post.roles/a');
            if ($is_edit) {
                $result = Db::name('RoleGroup')->where(['id' => $aGroupId])->update($data);
                if ($result) {
                    $result = $aGroupId;
                }
            } else {
                if (Db::name('RoleGroup')->where(['title' => $data['title']])->count()) {
                    $this->error("{$title}".lang('_FAIL_GROUP_EXIST_').lang('_EXCLAMATION_'));
                }
                $result = Db::name('RoleGroup')->insert($data);
            }
            if ($result) {

                Db::name('Role')->where(['group_id' => $result])->setField('group_id', 0); //所有该分组下的身份全部移出
                if (!is_null($roles)) {
                    Db::name('Role')->where(['id' => ['in', $roles]])->setField('group_id', $result); //选中的身份全部移入分组
                }
                $this->success("{$title}".lang('_SUCCESS_').lang('_EXCLAMATION_'), Url('Role/group'));
            } else {
                $this->error("{$title}".lang('_FAILURE_').lang('_EXCLAMATION_') . $this->roleGroupModel->getError());
            }
        } else {
            $data = array();
            if ($is_edit) {
                $data = Db::name('RoleGroup')->where(['id' => $aGroupId])->find();
                $map['group_id'] = $aGroupId;
                $roles = $this->roleModel->selectByMap($map, 'id asc', 'id');
                $data['roles'] = array_column($roles, 'id');
            }

            $roles = Db::name('Role')->field('id,group_id,title')->select();
            foreach ($roles as &$val) {
                $val['title'] = $val['group_id'] ? $val['title'] . lang('_ID_CURRENT_GROUP_').lang('_COLON_')."  {$val['group_id']})" : $val['title'];
            }
            unset($val);

            $selectArr = [];
            foreach ($roles as $val){
                $selectArr[$val['id']] = $val['title'];
            };

            $builder = new AdminConfigBuilder;
            $builder->title("{$title}");
            $builder->suggest(lang('_ROLE_EXCLUSION_ONE_GROUP_'));
            $builder
                ->keyId()
                ->keyText('title', lang('_TITLE_'))
                ->keyChosen('roles', lang('_GROUP_IDENTITY_SELECTION_'), lang('_AN_IDENTITY_CAN_ONLY_EXIST_IN_ONE_GROUP_AT_THE_SAME_TIME_'), 
                    $roles)
                ->buttonSubmit()
                ->buttonBack()
                ->data($data)
                ->display();
        }
    }

    /**
     * 删除分组（真删除）
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function deleteGroup()
    {
        $aGroupId = input('ids/a', 0, 'intval');
        if(!is_array($aGroupId)){
            $aGroupId= explode(',',$aGroupId);
        } 

        if (empty($aGroupId)) {
            $this->error(lang('_PARAMETER_ERROR_'));
        }
        Db::name('Role')->where(['group_id' => ['in',$aGroupId]])->setField('group_id', 0);

        $result = Db::name('RoleGroup')->where(['id' => ['in',$aGroupId]])->delete();

        if ($result) {
            $this->success(lang('_DELETE_SUCCESS_'));
        } else {
            $this->error(lang('_DELETE_FAILED_'));
        }
    }

    //身份分组end

    //身份其他配置 start

    /**
     * 身份默认积分配置
     */
    public function configScore()
    {
        $aRoleId = input('id', 0, 'intval');
        if (!$aRoleId) {
            $this->error(lang('_PLEASE_CHOOSE_YOUR_IDENTITY_'));
        }

        $map = getRoleConfigMap('score', $aRoleId);

        if (request()->isPost()) {
            $aPostKey = input('post.post_key', '', 'text');
            $post_key = explode(',', $aPostKey);
            $config_value = [];
            foreach ($post_key as $val) {
                if ($val != '') {
                    $config_value[$val] = input('post.' . $val, 0, 'intval');
                }
            }
            unset($val);
            $data['value'] = json_encode($config_value, true);

            $old = Db::name('RoleConfig')->where($map)->find();
            if ($old) {
                $map['id'] = $old['id'];
                $result = Db::name('RoleConfig')->where($map)->update($data);
            } else {
                $data = array_merge($map, $data);

                $result = Db::name('RoleConfig')->insert($data);
            }
            if ($result) {
                $this->success(lang('_OPERATION_SUCCESS_'), Url('Admin/Role/configScore', array('id' => $aRoleId)));
            } else {
                $this->error(lang('_OPERATION_FAILED_'));
            }
        } else {
            $mRole_list = Db::name('Role')->field('id,title')->select();
            //获取默认配置值
            $score = Db::name('RoleConfig')->where($map)->find();
            $score['value'] = json_decode($score['value'],true);
            //获取member表中积分字段$score_keys
            $score_keys = model('ucenter/Score')->getTypeList(['status' => ['GT', 0]]);

            $post_key = '';
            foreach ($score_keys as &$val) {
                $post_key .= ',score' . $val['id'];

                if(!empty($score['value']['score' . $val['id']])){
                    $val['value'] = $score['value']['score' . $val['id']];
                }else{
                    $val['value'] = 0;
                }
            }
            unset($val);

            $this->setTitle(lang('_IDENTITY_DEFAULT_INTEGRATION_'));
            $this->assign('score_keys', $score_keys);
            $this->assign('post_key', $post_key);
            $this->assign('role_list', $mRole_list);
            $this->assign('this_role', array('id' => $aRoleId));
            $this->assign('tab', 'score');
            return $this->fetch('score');
        }
    }

    /**
     * 身份默认头像配置
     */
    public function configAvatar()
    {
        $aRoleId = input('id', 0, 'intval');
        if (!$aRoleId) {
            $this->error(lang('_PLEASE_CHOOSE_YOUR_IDENTITY_'));
        }
        $map = getRoleConfigMap('avatar', $aRoleId);
        $data['data'] = '';
        if (request()->isPost()) {
            $data['value'] = input('post.avatar_id', 0, 'intval');
            $aSetNull = input('post.set_null', 0, 'intval');
            if (!$aSetNull) {
                if($data['value']==0){
                    $this->error(lang('_PLEASE_UPLOAD_YOUR_AVATAR_'));
                }
                if (Db::name('RoleConfig')->where($map)->find()) {
                    $result = Db::name('RoleConfig')->update($map, $data);
                } else {
                    $data = array_merge($map, $data);
                    $result = Db::name('RoleConfig')->insert($data);
                }
            } else {//使用系统默认头像
                if (Db::name('RoleConfig')->where($map)->find()) {
                    $result = Db::name('RoleConfig')->where($map)->delete();
                }else{
                    $this->success(lang('_THE_CURRENT_USE_OF_THE_SYSTEM_IS_THE_DEFAULT_AVATAR_'));
                }
            }
            if ($result) {
                clear_role_cache($aRoleId);
                $this->success(lang('_OPERATION_SUCCESS_'), Url('Admin/Role/configAvatar', array('id' => $aRoleId)));
            } else {
                $this->error(lang('_OPERATION_FAILED_') . Db::name('RoleConfig')->getError());
            }
        } else {
            $avatar_id = Db::name('RoleConfig')->where($map)->value('value');
            $mRole_list = $this->roleModel->field('id,title')->select();
            $this->assign('role_list', $mRole_list);
            $this->assign('this_role', ['id' => $aRoleId, 'avatar' => $avatar_id]);
            $this->assign('tab', 'avatar');
            return $this->fetch('avatar');
        }
    }

    /**
     * 用户可拥有标签配置
     */
    public function configUserTag()
    {
        $aRoleId = input('id', 0, 'intval');
        if (!$aRoleId) {
            $this->error(lang('_PLEASE_CHOOSE_YOUR_IDENTITY_'));
        }

        $map = getRoleConfigMap('user_tag', $aRoleId);

        if(request()->isPost()){
            $data['value'] = '';
            if (isset($_POST['tags'])) {
                sort($_POST['tags']);
                $data['value'] = implode(',', array_unique($_POST['tags']));
            }
            if (Db::name('RoleConfig')->where($map)->find()) {
                $result = Db::name('RoleConfig')->where($map)->update($data);
            } else {
                $data = array_merge($map, $data);
                $result = Db::name('RoleConfig')->insert($data);
            }
            if ($result === false) {
                $this->error(lang('_FAILED_') . Db::name('RoleConfig')->getError());
            } else {
                clear_role_cache($aRoleId);
                $this->success(lang('_OPERATION_SUCCESS_'));
            }
        }else{
            $mRole_list = $this->roleModel->field('id,title')->select();
            $fields = Db::name('RoleConfig')->where($map)->value('value');

            $tag_list=model('ucenter/UserTag')->getTreeList();

            $this->assign('tag_list',$tag_list);
            $this->assign('role_list', $mRole_list);
            $this->assign('this_role', array('id' => $aRoleId, 'fields' => $fields));
            $this->assign('tab', 'userTag');
            return $this->fetch('usertag');
        }

    }

    /**
     * 身份扩展资料配置 及 注册时要填写的资料配置
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function configField()
    {
        $aRoleId = input('id', 0, 'intval');
        if (!$aRoleId) {
            $this->error(lang('_PLEASE_CHOOSE_YOUR_IDENTITY_'));
        }
        $aType = input('type', 0, 'intval'); //扩展资料设置类型：1注册时要填写资料配置，0扩展资料字段设置

        if ($aType) { //注册时要填写资料配置
            $type = 'register_expend_field';
        } else { //扩展资料字段设置
            $type = 'expend_field';
        }
        $map = getRoleConfigMap($type, $aRoleId);

        if (request()->isPost()) {
            //注册时资料提交未处理，下一版完善
            $data['value'] = '';
            if (isset($_POST['fields'])) {
                sort($_POST['fields']);
                $data['value'] = implode(',', array_unique($_POST['fields']));
            }
            if (Db::name('RoleConfig')->where($map)->find()) {
                $result = Db::name('RoleConfig')->where($map)->update($data);
            } else {
                $data = array_merge($map, $data);
                $result = Db::name('RoleConfig')->insert($data);
            }
            if ($result === false) {
                $this->error(lang('_FAILED_') . Db::name('RoleConfig')->getError());
            } else {
                clear_role_cache($aRoleId);
                $this->success(lang('_OPERATION_SUCCESS_'));
            }
        } else {

            $aType = input('get.type', 0, 'intval'); //扩展资料设置类型：1注册时要填写资料配置，0扩展资料字段设置

            $mRole_list = $this->roleModel->field('id,title')->select();

            $fields = Db::name('RoleConfig')->where($map)->value('value');

            if ($aType == 1) { //注册时要填写资料配置
                $map_fields = getRoleConfigMap('expend_field', $aRoleId);
                $expend_fields = Db::name('RoleConfig')->where($map_fields)->value('value');
                $field_list = $expend_fields ? $this->getExpendField($expend_fields) : array();

                $this->setTitle(lang('_REGISTRATION_TO_FILL_IN_THE_DATA_CONFIGURATION_'));

                $tpl = 'fieldregister'; //模板地址
                $tab = 'fieldRegister';
            } else { //扩展资料字段设置

                $field_list = $this->getExpendField();
                $this->setTitle(lang('_EXTENDED_DATA_FIELD_SETTINGS_'));
                $tpl = 'field'; //模板地址
                $tab = 'field';
            }
            $this->assign('field_list', $field_list);
            $this->assign('role_list', $mRole_list);
            $this->assign('this_role', array('id' => $aRoleId, 'fields' => $fields));
            $this->assign('tab', $tab);
            return $this->fetch($tpl);
        }
    }

    //身份其他配置 end

    /**
     * 获取扩展字段列表
     * @param string $in
     * @return mixed
     */
    private function getExpendField($in = '')
    {
        if ($in != '') {
            $in = is_array($in) ? $in : explode(',', $in);
            $map_field['id'] = array('in', $in);
        }
        $map['status'] = ['egt', 0];
        $profileList = Db::name('field_group')->where($map)->order("sort asc")->select(); //获取扩展字段分组

        $type_default = array(
            'input' => lang('_ONE-WAY_TEXT_BOX_'),
            'radio' => lang('_RADIO_BUTTON_'),
            'checkbox' => lang('_CHECKBOX_'),
            'select' => lang('_DROP-DOWN_BOX_'),
            'time' => lang('_DATE_'),
            'textarea' => lang('_MULTI_LINE_TEXT_BOX_')
        );
        $map_field['status'] = array('egt', 0);
        foreach ($profileList as $key => &$val) {
            //获取分组下字段列表
            $map_field['profile_group_id'] = $val['id'];
            $field_list = Db::name('field_setting')->where($map_field)->order("sort asc")->select();
            foreach ($field_list as &$vl) {
                $vl['form_type'] = $type_default[$vl['form_type']];
            }
            unset($vl);
            if ($field_list) {
                $val['field_list'] = $field_list;
            } else {
                unset($profileList[$key]);
            }
        }
        unset($key, $val, $field_list);
        return $profileList;
    }

    /**
     * 上传图片（上传默认头像）
     */
    public function uploadPicture()
    {
        $files = request()->file();
        if (empty($files)) {
            $return['code'] = 0;
            $return['msg'] = 'No file upload or server upload limit exceeded';
            return json($return);
        }

        $arr = model('api/Upload')->upload($files,'picture');

        if(is_array($arr)){

            foreach($arr as &$v){
                $v['path256'] = getThumbImageById($v['id'], 256, 256);
                $v['path128'] = getThumbImageById($v['id'], 128, 128);
                $v['path64'] = getThumbImageById($v['id'], 64, 64);
                $v['path32'] = getThumbImageById($v['id'], 32, 32);
            }
            unset($v);
            
            $return['code'] = 1;
            $return['msg'] = 'Upload successful';
            $return['data'] = $arr;
        }else{
            $return['code'] = 1;
            $return['msg'] = model('api/Upload')->getError();
        }
        /* 返回JSON数据 */
        return json($return);
    }

} 