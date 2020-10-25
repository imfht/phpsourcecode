<?php

namespace app\ucenter\Controller;

use think\Controller;
use think\Db;
use app\ucenter\controller\Base;

class Config extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $this->_assignSelf();
        $this->_haveOtherRole();
    }

    /**
     * 关联自己的信息
     */
    private function _assignSelf()
    {
        $self = query_user(array('avatar128', 'nickname', 'space_url', 'space_link','score','title'));
        $this->assign('self', $self);
    }

    /**
     * 是否拥有其他角色或可拥有角色
     */
    private function _haveOtherRole()
    {
        $have = 0;

        $register_type = modC('REGISTER_TYPE', 'normal', 'Invite');
        $register_type = explode(',', $register_type);
        if (!in_array('invite', $register_type)) {//开启邀请注册
            $map['status'] = 1;
            $map['invite'] = 0;
            if (Db::name('role')->where($map)->count() > 1) {
                $have = 1;
            } else {
                $map_user['uid'] = is_login();
                $map_user['role_id'] = array('neq', get_login_role());
                $map_user['status'] = array('egt', 0);
                $role_ids = Db::name('user_role')->where($map_user)->field('role_id')->select();
                if ($role_ids) {
                    $role_ids = array_column($role_ids, 'role_id');
                    $map_can['status'] = 1;
                    $map_can['id'] = ['in', $role_ids];
                    if (Db::name('role')->where($map_can)->count()) {
                        $have = 1;
                    }
                }
            }
        } else {
            $map['status'] = 1;
            if (Db::name('Role')->where($map)->count() > 1) {
                $have = 1;
            }
        }
        $this->assign('can_show_role', $have);
    }

    private function _setTab($name)
    {
        $this->assign('tab', $name);
    }
    /**
     * 修改密码
     * @return [type] [description]
     */
    public function password()
    {
        if(request()->isPost()){
            $old_password = input('post.old_password','','text');
            $new_password = input('post.new_password','','text');
            $confirm_password = input('post.confirm_password','','text');
            //调用接口
            $ucenterMemberModel = model('ucenterMember');
            $resCode = $ucenterMemberModel->changePassword($old_password, $new_password, $confirm_password);

            if ($resCode>0) {

                $this->success(lang('_SUCCESS_PASSWORD_ALTER_').lang('_PERIOD_'), Url('password'));
            } else {
                $this->error(model('member')->showRegError($resCode));
            }
        }else{
            $this->_setTab('password');
            return $this->fetch(); 
        }
    }
    /**
     * 我的积分
     * @return [type] [description]
     */
    public function score()
    {

        $scoreModel = model('ucenter/Score');

        $scores = $scoreModel->getTypeList(['status'=>1]);
        foreach ($scores as &$v) {
            $v['value'] = $scoreModel->getUserScore(is_login(), $v['id']);
        }
        unset($v);
        $this->assign('scores', $scores);


        $level = nl2br(modC('LEVEL', '
0:Lv1 '.lang('_PRACTICE_').'
50:Lv2 '.lang('_PROBATION_').'
100:Lv3 '.lang('_POSITIVE_').'
200:Lv4 '.lang('_AID_').'
400:Lv5 '.lang('_MANAGER_').'
800:Lv6 '.lang('_DIRECTOR__').'
1600:Lv7 '.lang('_CHAIRMAN__').'
        ', 'UserConfig'));
        $this->assign('level', $level);

        $self = query_user(array('score', 'title'));

        $this->assign('self', $self);
        $action = model('common/Action')->getAction(['status' => 1]);
        $action_module = [];
        
        foreach ($action as &$v) {
            $v['rule_array'] = unserialize($v['rule']);
            if(is_array($v['rule_array'])){
                foreach ($v['rule_array'] as &$o) {
                    if (is_numeric($o['rule'])) {
                        $o['rule'] = $o['rule'] > 0 ? '+' . intval($o['rule']) : $o['rule'];
                    }
                    $o['score'] = model('Score')->getType(['id' => $o['field']]);
                }
            }
            if ($v['rule_array'] != false) {
                $action_module[$v['module']]['action'][] = $v;
            }
        }
        unset($v);

        foreach ($action_module as $key => &$a) {
            if (empty($a['action'])) {
                unset($action_module[$key]);
            }
            $a['module'] = model('common/Module')->getModule($key);
        }
        unset($a);
        $this->assign('action_module', $action_module);
        $this->_assignSelf();
        $this->_setTab('score');
        return $this->fetch();
    }

    public function other()
    {

        $this->_setTab('other');
        return $this->fetch();
    }

    public function avatar()
    {

        $this->_setTab('avatar');
        return $this->fetch();
    }

    public function role()
    {
        $userRoleModel = Db::name('UserRole');
        if (Request()->isPost()) {
            $aShowRole = input('post.show_role', 0, 'intval');
            $map['role_id'] = $aShowRole;
            $map['uid'] = is_login();
            $map['status'] = array('egt', 1);
            if (!$userRoleModel->where($map)->count()) {
                $this->error(lang('_ERROR_PARAM_').lang('_EXCLAMATION_'));
            }
            $result = Db::name('Member')->where(array('uid' => is_login()))->setField('show_role', $aShowRole);
            if ($result) {
                clean_query_user_cache(is_login(), array('show_role'));
                $this->success(lang('_SUCCESS_SETTINGS_').lang('_EXCLAMATION_'));
            } else {
                $this->error(lang('_FAIL_SETTINGS_').lang('_EXCLAMATION_'));
            }
        } else {
            $role_id = get_login_role();//当前登录角色

            $already_role_list = Db::name('UserRole')->where(['uid' => is_login()])->field('role_id,status')->select();
            $already_role_ids = array_column($already_role_list, 'role_id');
            $already_role_list = array_combine($already_role_ids, $already_role_list);

            $map_already_roles['id'] = array('in', $already_role_ids);
            $map_already_roles['status'] = 1;
            $already_roles = Db::name('Role')->where($map_already_roles)->order('sort asc')->select();
            $already_group_ids = array_unique(array_column($already_roles, 'group_id'));

            foreach ($already_roles as &$val) {
                $val['user_status'] = $already_role_list[$val['id']]['status'] != 2 ? ($already_role_list[$val['id']]['status'] == 1) ? '<span style="color: green;">'.lang('_AUDITED_').'</span>' : '<span style="color: #ff0000;">'.lang('_DISABLED_').'<span style="color: 333">'.lang('_CONTACT_ADMIN_').'</span></span>' : '<span style="color: #0003FF;">'.lang('_AUDITING_').'</span>';;
                $val['can_login'] = $val['id'] == $role_id ? 0 : 1;
                $val['user_role_status'] = $already_role_list[$val['id']]['status'];
            }
            unset($val);

            $already_group_ids = array_diff($already_group_ids, array(0));//去除无分组角色组
            if (count($already_group_ids)) {
                $map_can_have_roles['group_id'] = array('not in', $already_group_ids);//同组内的角色不显示
            }
            $map_can_have_roles['id'] = ['not in', $already_role_ids];//去除已有角色
            $map_can_have_roles['invite'] = 0;//不需要邀请注册
            $map_can_have_roles['status'] = 1;
            $can_have_roles = Db::name('Role')->where($map_can_have_roles)->order('sort asc')->select();//可持有角色

            $register_type = modC('REGISTER_TYPE', 'normal', 'Invite');
            $register_type = explode(',', $register_type);
            if (in_array('invite', $register_type)) {//开启邀请注册
                $map_can_have_roles['invite'] = 1;
                $can_up_roles = Db::name('Role')->where($map_can_have_roles)->order('sort asc')->select();//可升级角色
                $this->assign('can_up_roles', $can_up_roles);
            }

            $show_role = query_user(array('show_role'));
            $this->assign('show_role', $show_role['show_role']);
            $this->assign('already_roles', $already_roles);
            $this->assign('can_have_roles', $can_have_roles);

            $this->_setTab('role');
            return $this->fetch();
        }

    }

    public function tag()
    {
        $userTagLinkModel = model('ucenter/UserTagLink');
        if (request()->isPost()) {
            $aTagIds = input('post.tag_ids', '', 'text');
            $result = $userTagLinkModel->editData($aTagIds);
            if ($result) {
                $this->success('');
            } else {
                $this->error(lang('_FAIL_OPERATE_').lang('_EXCLAMATION_'));
            }

        } else {
            $userTagModel = model('ucenter/UserTag');
            $map = getRoleConfigMap('user_tag', get_login_role());
            $ids = Db::name('RoleConfig')->where($map)->value('value');
            if ($ids) {
                $ids = explode(',', $ids);
                $tag_list = $userTagModel->getTreeListByIds($ids);
                $this->assign('tag_list', $tag_list);
            }

            $myTags = $userTagLinkModel->getUserTag(is_login());

            $this->assign('my_tag', $myTags);

            $my_tag_ids = '';
            if(is_array($myTags)){
                $my_tag_ids = array_column($myTags, 'id');
                $my_tag_ids = implode(',', $my_tag_ids); 
                
            }
            $this->assign('my_tag_ids', $my_tag_ids);
            $this->_setTab('tag');
            return $this->fetch();
        }
    }
    /**
     * 用户资料页
     * @return [type] [description]
     */
    public function index()
    {
        $aUid = input('get.uid', is_login(), 'intval');
        $aTab = input('get.tab', '', 'text');
        $aNickname = input('post.nickname', '', 'text');
        $aSex = input('post.sex', 0, 'intval');
        $aSignature = input('post.signature', '', 'text');
        $aCommunity = input('post.community', 0, 'intval');
        $aDistrict = input('post.district', 0, 'intval');
        $aCity = input('post.city', 0, 'intval');
        $aProvince = input('post.province', 0, 'intval');

        if (Request()->isPost()) {
            $this->checkNickname($aNickname);
            $this->checkSex($aSex);
            $this->checkSignature($aSignature);

            $user['pos_province'] = $aProvince;
            $user['pos_city'] = $aCity;
            $user['pos_district'] = $aDistrict;
            $user['pos_community'] = $aCommunity;

            $user['nickname'] = $aNickname;
            $user['sex'] = $aSex;
            $user['signature'] = $aSignature;
            //$user['uid'] = get_uid();

            $rs_member = Db::name('Member')->where(['uid'=>get_uid()])->update($user);

            //$rs_ucmember = Db::name('UCenterMember')->where(['id'=>get_uid()])->update($ucuser);
            clean_query_user_cache(get_uid(), ['nickname', 'sex', 'signature', 'email', 'pos_province', 'pos_city', 'pos_district', 'pos_community']);

            //TODO tox 清空缓存

            if ($rs_member) {
                $this->success(lang('_SUCCESS_SETTINGS_').lang('_PERIOD_'));

            } else {
                $this->error(lang('_DATA_UNMODIFIED_').lang('_PERIOD_'));
            }

        } else {
            //调用API获取基本信息
            //TODO tox 获取省市区数据
            $user = query_user(array('nickname', 'signature', 'email', 'mobile', 'avatar128', 'rank_link', 'sex', 'pos_province', 'pos_city', 'pos_district', 'pos_community'), $aUid);
            //显示页面
            $this->assign('user', $user);

            $this->_accountInfo();

            $this->assign('tab', $aTab);
            $this->_getExpandInfo();
            $this->_setTab('info');
            return $this->fetch();
        }

    }

    /**验证用户名
     * @param $nickname
     */
    private function checkNickname($nickname)
    {
        $length = mb_strlen($nickname, 'utf8');
        if ($length == 0) {
            $this->error(lang('_ERROR_NICKNAME_INPUT_').lang('_PERIOD_'));
        } else if ($length > modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')) {
            $this->error(lang('_ERROR_NICKNAME_1_'). modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').lang('_ERROR_NICKNAME_2_').lang('_PERIOD_'));
        } else if ($length < modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG')) {
            $this->error(lang('_ERROR_NICKNAME_LENGTH_1_').modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').lang('_ERROR_NICKNAME_2_').lang('_PERIOD_'));
        }
        $match = preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname);
        if (!$match) {
            $this->error(lang('_ERROR_NICKNAME_LIMIT_').lang('_PERIOD_'));
        }

        $map_nickname['nickname'] = $nickname;
        $map_nickname['uid'] = array('neq', is_login());
        $had_nickname = Db::name('Member')->where($map_nickname)->count();
        if ($had_nickname) {
            $this->error(lang('_ERROR_NICKNAME_USED_').lang('_PERIOD_'));
        }
        $denyName = Db::name("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->value('value');
        if ($denyName != '') {
            $denyName = explode(',', $denyName);
            foreach ($denyName as $val) {
                if (!is_bool(strpos($nickname, $val))) {
                    $this->error(lang('_ERROR_NICKNAME_FORBIDDEN_').lang('_PERIOD_'));
                }
            }
        }
    }


    /**验证签名
     * @param $signature
     * @auth 陈一枭
     */
    private function checkSignature($signature)
    {
        $length = mb_strlen($signature, 'utf8');
        if ($length >= 100) {
            $this->error(lang('_ERROR_SIGNATURE_LENGTH_'));
        }
    }

    /**
     * @param $sex
     * @return int
     */
    private function checkSex($sex)
    {
        if ($sex < 0 || $sex > 2) {
            $this->error(lang('_ERROR_SEX_').lang('_PERIOD_'));
            return $sex;
        }
        return $sex;
    }

    /**
     * @param $email
     */
    private function checkEmail($email)
    {
        $pattern = "/([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{2})?/i";
        if (!preg_match($pattern, $email)) {
            $this->error(lang('_ERROR_EMAIL_FORMAT_').lang('_PERIOD_'));
        }

        $map['email'] = $email;
        $map['id'] = array('neq', get_uid());
        $had = Db::name('ucenterMember')->where($map)->count();
        if ($had) {
            $this->error(lang('_ERROR_EMAIL_USED_').lang('_PERIOD_'));
        }
    }


    /**获取用户扩展信息
     * @param null $uid
     */
    public function _getExpandInfo($uid = null)
    {
        $profile_group_list = $this->_profile_group_list($uid);
        if ($profile_group_list) {
            $info_list = $this->_info_list($profile_group_list[0]['id'], $uid);
            $this->assign('info_list', $info_list);
            $this->assign('profile_group_id', $profile_group_list[0]['id']);
            //dump($info_list);exit;
        }
        foreach ($profile_group_list as &$v) {
            $v['fields'] = $this->_getExpandInfoByGid($v['id']);
        }

        $this->assign('profile_group_list', $profile_group_list);
    }


    /**显示某一扩展分组信息
     * @param null $profile_group_id
     * @param null $uid
     */
    public function _getExpandInfoByGid($profile_group_id = null)
    {
        $res = Db::name('field_group')->where(array('id' => $profile_group_id, 'status' => '1'))->find();
        if (!$res) {
            return array();
        }
        $info_list = $this->_info_list($profile_group_id);

        return $info_list;
        $this->assign('info_list', $info_list);
        $this->assign('profile_group_id', $profile_group_id);
        $this->assign('profile_group_list', $profile_group_list);
    }

    /**修改用户扩展信息
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function edit_expandinfo($profile_group_id)
    {
        $field_list = $this->getRoleFieldIds();
        if ($field_list) {
            $map_field['id'] = array('in', $field_list);
        } else {
            $this->error(lang('_ERROR_INFO_SAVE_NONE_').lang('_EXCLAMATION_'));
        }
        $map_field['profile_group_id'] = $profile_group_id;
        $map_field['status'] = 1;
        $field_setting_list = Db::name('field_setting')->where($map_field)->order('sort asc')->select();

        if (!$field_setting_list) {
            $this->error(lang('_ERROR_INFO_CHANGE_NONE_').lang('_EXCLAMATION_'));
        }

        $data = null;
        foreach ($field_setting_list as $key => $val) {
            $data[$key]['uid'] = is_login();
            $data[$key]['field_id'] = $val['id'];
            switch ($val['form_type']) {
                case 'input':
                    $val['value'] = text($_POST['expand_' . $val['id']]);
                    if (!$val['value'] || $val['value'] == '') {
                        if ($val['required'] == 1) {
                            $this->error($val['field_name'] . lang('_ERROR_CONTENT_NONE_').lang('_EXCLAMATION_'));
                        }
                    } else {
                        $val['submit'] = $this->_checkInput($val);
                        if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                            $this->error($val['submit']['msg']);
                        }
                    }
                    $data[$key]['field_data'] = $val['value'];
                    break;
                case 'radio':
                    $val['value'] = text($_POST['expand_' . $val['id']]);
                    $data[$key]['field_data'] = $val['value'];
                    break;
                case 'checkbox':
                    $val['value'] = $_POST['expand_' . $val['id']];
                    if (!is_array($val['value']) && $val['required'] == 1) {
                        $this->error(lang('_ERROR_AT_LIST_ONE_').lang('_COLON_') . $val['field_name']);
                    }
                    $data[$key]['field_data'] = is_array($val['value']) ? implode('|', $val['value']) : '';
                    break;
                case 'select':
                    $val['value'] = text($_POST['expand_' . $val['id']]);
                    $data[$key]['field_data'] = $val['value'];
                    break;
                case 'time':
                    $val['value'] = text($_POST['expand_' . $val['id']]);
                    $val['value'] = strtotime($val['value']);
                    $data[$key]['field_data'] = $val['value'];
                    break;
                case 'textarea':
                    $val['value'] = text($_POST['expand_' . $val['id']]);
                    if (!$val['value'] || $val['value'] == '') {
                        if ($val['required'] == 1) {
                            $this->error($val['field_name'] . lang('_ERROR_CONTENT_NONE_').lang('_EXCLAMATION_'));
                        }
                    } else {
                        $val['submit'] = $this->_checkInput($val);
                        if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                            $this->error($val['submit']['msg']);
                        }
                    }
                    $val['submit'] = $this->_checkInput($val);
                    if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                        $this->error($val['submit']['msg']);
                    }
                    $data[$key]['field_data'] = $val['value'];
                    break;
            }
        }
        $map['uid'] = is_login();
        $map['role_id'] = get_login_role();
        $is_success = false;
        foreach ($data as $dl) {
            $dl['role_id'] = $map['role_id'];

            $map['field_id'] = $dl['field_id'];
            $res = Db::name('field')->where($map)->find();
            if (!$res) {
                if ($dl['field_data'] != '' && $dl['field_data'] != null) {
                    $dl['createTime'] = $dl['changeTime'] = time();
                    if (!Db::name('field')->save($dl)) {
                        $this->error(lang('_ERROR_INFO_ADD_').lang('_EXCLAMATION_'));
                    }
                    $is_success = true;
                }
            } else {
                $dl['changeTime'] = time();
                if (!Db::name('field')->where('id=' . $res['id'])->save($dl)) {
                    $this->error(lang('_ERROR_INFO_CHANGE_').lang('_EXCLAMATION_'));
                }
                $is_success = true;
            }
            unset($map['field_id']);
        }
        clean_query_user_cache(is_login(), 'expand_info');
        if ($is_success) {
            $this->success(lang('_SUCCESS_SAVE_').lang('_EXCLAMATION_'));
        } else {
            $this->error(lang('_ERROR_SAVE_').lang('_EXCLAMATION_'));
        }
    }

    /**input类型验证
     * @param $data
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    function _checkInput($data)
    {
        if ($data['form_type'] == "textarea") {
            $validation = $this->_getValidation($data['validation']);
            if (($validation['min'] != 0 && mb_strlen($data['value'], "utf-8") < $validation['min']) || ($validation['max'] != 0 && mb_strlen($data['value'], "utf-8") > $validation['max'])) {
                if ($validation['max'] == 0) {
                    $validation['max'] = '';
                }
                $info['succ'] = 0;
                $info['msg'] = $data['field_name'] . lang('_INFO_LENGTH_1_') . $validation['min'] . "-" . $validation['max'] . lang('_INFO_LENGTH_2_');
            }
        } else {
            switch ($data['child_form_type']) {
                case 'string':
                    $validation = $this->_getValidation($data['validation']);
                    if (($validation['min'] != 0 && mb_strlen($data['value'], "utf-8") < $validation['min']) || ($validation['max'] != 0 && mb_strlen($data['value'], "utf-8") > $validation['max'])) {
                        if ($validation['max'] == 0) {
                            $validation['max'] = '';
                        }
                        $info['succ'] = 0;
                        $info['msg'] = $data['field_name'] .  lang('_INFO_LENGTH_1_') . $validation['min'] . "-" . $validation['max'] .  lang('_INFO_LENGTH_2_');
                    }
                    break;
                case 'number':
                    if (preg_match("/^\d*$/", $data['value'])) {
                        $validation = $this->_getValidation($data['validation']);
                        if (($validation['min'] != 0 && mb_strlen($data['value'], "utf-8") < $validation['min']) || ($validation['max'] != 0 && mb_strlen($data['value'], "utf-8") > $validation['max'])) {
                            if ($validation['max'] == 0) {
                                $validation['max'] = '';
                            }
                            $info['succ'] = 0;
                            $info['msg'] = $data['field_name'] .  lang('_INFO_LENGTH_1_') . $validation['min'] . "-" . $validation['max'] .  lang('_INFO_LENGTH_2_').lang('_COMMA_').lang('_INFO_AND_DIGITAL_');
                        }
                    } else {
                        $info['succ'] = 0;
                        $info['msg'] = $data['field_name'] . lang('_INFO_DIGITAL_');
                    }
                    break;
                case 'email':
                    if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $data['value'])) {
                        $info['succ'] = 0;
                        $info['msg'] = $data['field_name'] . lang('_INFO_FORMAT_EMAIL_');
                    }
                    break;
                case 'phone':
                    if (!preg_match("/^\d{11}$/", $data['value'])) {
                        $info['succ'] = 0;
                        $info['msg'] = $data['field_name'] . lang('_INFO_FORMAT_PHONE_');
                    }
                    break;
            }
        }
        return $info;
    }

    /**处理$validation
     * @param $validation
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    function _getValidation($validation)
    {
        $data['min'] = $data['max'] = 0;
        if ($validation != '') {
            $items = explode('&', $validation);
            foreach ($items as $val) {
                $item = explode('=', $val);
                if ($item[0] == 'min' && is_numeric($item[1]) && $item[1] > 0) {
                    $data['min'] = $item[1];
                }
                if ($item[0] == 'max' && is_numeric($item[1]) && $item[1] > 0) {
                    $data['max'] = $item[1];
                }
            }
        }
        return $data;
    }

    /**分组下的字段信息及相应内容
     * @param null $id 扩展分组id
     * @param null $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _info_list($id = null, $uid = null)
    {

        $fields_list = $this->getRoleFieldIds($uid);
        $info_list = null;

        if (isset($uid) && $uid != is_login()) {
            //查看别人的扩展信息
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1', 'visiable' => '1', 'id' => array('in', $fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = $uid;
        } else if (is_login()) {
            $field_setting_list = D('field_setting')->where(array('profile_group_id' => $id, 'status' => '1', 'id' => array('in', $fields_list)))->order('sort asc')->select();

            if (!$field_setting_list) {
                return null;
            }
            $map['uid'] = is_login();

        } else {
            $this->error(lang('_ERROR_PLEASE_LOGIN_').lang('_EXCLAMATION_'));
        }
        foreach ($field_setting_list as $val) {
            $map['field_id'] = $val['id'];
            $field = D('field')->where($map)->find();
            $val['field_content'] = $field;
            $info_list[$val['id']] = $val;
            unset($map['field_id']);
        }

        return $info_list;
    }

    private function getRoleFieldIds($uid = null)
    {
        $role_id = get_role_id($uid);
        $fields_list = cache('Role_Expend_Info_' . $role_id);
        if (!$fields_list) {
            $map_role_config = getRoleConfigMap('expend_field', $role_id);
            $fields_list = Db::name('RoleConfig')->where($map_role_config)->value('value');
            if ($fields_list) {
                $fields_list = explode(',', $fields_list);
                cache('Role_Expend_Info_' . $role_id, $fields_list, 600);
            }
        }
        return $fields_list;
    }


    /**扩展信息分组列表获取
     * @return mixed
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _profile_group_list($uid = null)
    {
        $profile_group_list = array();
        $fields_list = $this->getRoleFieldIds($uid);
        if ($fields_list) {
            $fields_group_ids = Db::name('FieldSetting')->where(array('id' => array('in', $fields_list), 'status' => '1'))->field('profile_group_id')->select();
            if ($fields_group_ids) {
                $fields_group_ids = array_unique(array_column($fields_group_ids, 'profile_group_id'));
                $map['id'] = array('in', $fields_group_ids);

                if (isset($uid) && $uid != is_login()) {
                    $map['visiable'] = 1;
                }
                $map['status'] = 1;
                $profile_group_list = Db::name('field_group')->where($map)->order('sort asc')->select();
            }
        }
        return $profile_group_list;
    }


    public function changeAvatar()
    {
        $this->defaultTabHash('change-avatar');
        return $this->fetch();
    }


    private function iframeReturn($result)
    {
        $json = json_encode($result);
        $json = htmlspecialchars($json);
        $html = "<textarea data-type=\"application/json\">$json</textarea>";
        echo $html;
        exit;
    }


    /**
     * accountInfo   账户信息
     */
    private function _accountInfo()
    {
        $info = Db::name('ucenterMember')->field('id,username,email,mobile,type')->find(is_login());
        $this->assign('accountInfo', $info);
    }

    /**
     * saveUsername  修改用户名
     * @author:xjw129xjt(肖骏涛) xjt@ourstu.com
     */
    public function saveUsername()
    {
        $aUsername = $cUsername = input('post.username', '', 'text');

        if (!check_reg_type('username')) {
            $this->error(lang('_ERROR_USERNAME_CONG_CLOSED_').lang('_EXCLAMATION_'));
        }


        //判断是否登录
        if (!is_login()) {
            $this->error(lang('_ERROR_AFTER_LOGIN_').lang('_EXCLAMATION_'));
        }
        //判断提交的用户名是否为空
        if (empty($aUsername)) {
            $this->error(lang('_USERNAME_NOT_EMPTY_').lang('_EXCLAMATION_'));
        }
        check_username($cUsername, $cEmail, $cMobile);
        if (empty($cUsername)) {
            !empty($cEmail) && $str = lang('_EMAIL_');
            !empty($cMobile) && $str = lang('_PHONE_');
            $this->error(lang('_USERNAME_NOT_') . $str);
        }

        //验证用户名是否是字母和数字
        preg_match("/^[a-zA-Z0-9_]{".modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').",".modC('USERNAME_MAX_LENGTH',32,'USERCONFIG')."}$/", $aUsername, $match);
        if (!$match) {
            $this->error(lang('_ERROR_USERNAME_LIMIT_1_').modC('USERNAME_MIN_LENGTH',2,'USERCONFIG').'-'.modC('USERNAME_MAX_LENGTH',32,'USERCONFIG').lang('_ERROR_USERNAME_LIMIT_2_').lang('_EXCLAMATION_'));

        }

        $uid = get_uid();
        
        //判断用户是否已设置用户名
        $username = Db::name('ucenterMember')->where(array('id' => $uid))->getField('username');
        if (empty($username)) {
            //判断修改的用户名是否已存在
            $id = $mucenter->where(array('username' => $aUsername))->getField('id');
            if ($id) {
                $this->error(lang('_ERROR_USERNAME_EXIST_').lang('_EXCLAMATION_'));
            } else {
                //修改用户名
                $rs = Db::name('ucenterMember')->where(array('id' => $uid))->save(array('username' => $aUsername));
                if (!$rs) {
                    $this->error(lang('_FAIL_SETTINGS_').lang('_EXCLAMATION_'));
                }
                $this->success(lang('_SUCCESS_SETTINGS_').lang('_EXCLAMATION_'), 'refresh');
            }
        }
        $this->error(lang('_ERROR_USERNAME_CANNOT_MODIFY_').lang('_EXCLAMATION_'));
    }

    /**
     * changeaccount  修改帐号信息
     */
    public function changeAccount()
    {
        $aTag = input('get.tag', '', 'text');
        $aTag = $aTag == 'mobile' ? 'mobile' : 'email';
        $this->assign('cName', $aTag == 'mobile' ? lang('_PHONE_') : lang('_EMAIL_'));
        $this->assign('type', $aTag);
        return $this->fetch();

    }
    /**
     * 发送验证信息至手机或邮箱
     * @param  [type] $account [description]
     * @param  [type] $verify  [description]
     * @param  [type] $type    [description]
     * @return [type]          [description]
     */
    public function doSendVerify($account, $verify, $type)
    {
        switch ($type) {
            case 'mobile':
                $content = modC('SMS_CONTENT', '{$verify}', 'CONFIG');
                //dump($content);exit;
                $content = str_replace('{$verify}', $verify, $content);
                $content = str_replace('{$account}', $account, $content);
                $res = sendSMS($account, $content);
                return $res;
                break;
            case 'email':
                //发送验证邮箱

                $content = modC('REG_EMAIL_VERIFY', '{$verify}', 'USERCONFIG');
                $content = str_replace('{$verify}', $verify, $content);
                $content = str_replace('{$account}', $account, $content);
                $res = send_mail($account, modC('WEB_SITE_NAME', lang('_MUUCMF_'), 'Config') . lang('_EMAIL_VERIFY_2_'), $content);

                return $res;
                break;
        }

    }

    /**
     * checkVerify  验证验证码
     */
    public function checkVerify()
    {

        $aAccount = input('account', '', 'text');
        $aType = input('type', '', 'text');
        $aVerify = input('verify', '', 'text');
        $aUid = input('uid', 0, 'intval');

        if (!is_login() || $aUid != is_login()) {
            $this->error(lang(''));
        }
        $aType = $aType == 'mobile' ? 'mobile' : 'email';
        $res = model('Verify')->checkVerify($aAccount, $aType, $aVerify, $aUid);
        if (!$res) {
            $this->error(lang('_FAIL_VERIFY_'));
        }
        Db::name('ucenterMember')->where(['id' => $aUid])->save(array($aType => $aAccount));
        $this->success(lang('_SUCCESS_VERIFY_'), U('ucenter/config/index'));

    }


    public function cleanRemember()
    {
        $uid = is_login();
        if ($uid) {
            Db::name('user_token')->where('uid=' . $uid)->delete();
            $this->success(lang('_SUCCESS_CLEAR_').lang('_EXCLAMATION_'));
        }
        $this->error(lang('_FAIL_CLEAR_').lang('_EXCLAMATION_'));
    }

}