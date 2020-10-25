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

use app\admin\builder\AdminConfigBuilder;
use app\admin\builder\AdminListBuilder;
use app\admin\builder\AdminSortBuilder;
use app\common\model\Member;
use app\ucenter\logic\UcenterMember;
use think\Loader;


/**
 * 后台用户控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class User extends Admin
{

    /**
     * 用户管理首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index()
    {
        $nickname = input('nickname', '', 'text');
        $aSeek = input('seek', 0, 'text');
//        $map['status'] = array('egt', 0);
        switch ($aSeek) {
            case '0':

                break;
            case '1':
                $map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
                break;
            case '2':
                $map['nickname'] = array('like', '%' . (string)$nickname . '%');
                break;
            case '3':
                $mapEmail['email'] = array('like', '%' . $nickname . '%');
                $temp = db('ucenter_member')->where($mapEmail)->field('id')->select();
                foreach($temp as $val) {
                    $temp1[] = implode(',', $val);
                }
                $map['uid'] = array('in', $temp1);
                break;
            case '4':
                $mapMobile['mobile'] = array('like', '%' . $nickname . '%');
                $temp = db('ucenter_member')->where($mapMobile)->field('id')->select();
                foreach($temp as $val) {
                    $temp1[] = implode(',', $val);
                }
                $map['uid'] = array('in', $temp1);
                break;
            default:
        }
        if (empty($map)) {
            $map = array();
        }
        $list = Member::where($map)->paginate(20);
        $page = $list->render();

        $this->assign('_list', $list);
        $this->assign('page', $page);
        $this->assign('seek', $aSeek);
        $this->meta_title = lang('_USER_INFO_');
        return $this->fetch();
    }

    /**
     * 重置用户密码
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function initPass()
    {
        $uids = input('id');
        !is_array($uids) && $uids = explode(',', $uids);
        foreach ($uids as $key => $val) {
            if (!query_user('uid', $val)) {
                unset($uids[$key]);
            }
        }
        if (!count($uids)) {
            $this->error(lang('_ERROR_USER_RESET_SELECT_').lang('_EXCLAMATION_'));
        }
        $ucModel = new UcenterMember();
        $res = $ucModel->where('id','in',$uids)->update(['password' => think_ucenter_md5('123456',UC_AUTH_KEY)]);
        if ($res) {
            $this->success(lang('_SUCCESS_PW_RESET_').lang('_EXCLAMATION_'));
        } else {
            $this->error(lang('_ERROR_PW_RESET_'));
        }
    }

    public function changeGroup()
    {

        if (input('do') == 1) {
            //清空group
            $aAll = input('post.all', 0, 'intval');
            $aUids = input('post.uid', array(), 'intval');
            $aGids = input('post.gid', array(), 'intval');

            if ($aAll) {//设置全部用户
                $prefix = config('table_prefix');
                db('')->execute("TRUNCATE TABLE {$prefix}auth_group_access");
                $aUids = UCenterMember()->getField('id', true);

            } else {
                db('AuthGroupAccess')->where(array('uid' => array('in', implode(',', $aUids))))->delete();
            }
            foreach ($aUids as $uid) {
                foreach ($aGids as $gid) {
                    db('AuthGroupAccess')->update(array('uid' => $uid, 'group_id' => $gid));
                }
            }
            $this->success(lang('_SUCCESS_'));
        } else {
            $aId = input('post.id', array(), 'intval');

            foreach ($aId as $uid) {
                $user[] = query_user(array('space_link', 'uid'), $uid);
            }
            $groups = db('AuthGroup')->where(array('status' => 1))->select();
            $this->assign('groups', $groups);
            $this->assign('users', $user);
            return $this->fetch();
        }
    }

    /**用户扩展资料信息页
     * @param null $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function expandinfo_select($page = 1, $r = 20)
    {
        $nickname = I('nickname');
        $map['status'] = array('egt', 0);
        if (is_numeric($nickname)) {
            $map['uid|nickname'] = array(intval($nickname), array('like', '%' . $nickname . '%'), '_multi' => true);
        } else {
            $map['nickname'] = array('like', '%' . (string)$nickname . '%');
        }
        $list = M('Member')->where($map)->order('last_login_time desc')->page($page, $r)->select();
        $totalCount = M('Member')->where($map)->count();
        int_to_string($list);
        //扩展信息查询
        $map_profile['status'] = 1;
        $field_group = D('field_group')->where($map_profile)->select();
        $field_group_ids = array_column($field_group, 'id');
        $map_profile['profile_group_id'] = array('in', $field_group_ids);
        $fields_list = D('field_setting')->where($map_profile)->getField('id,field_name,form_type');
        $fields_list = array_combine(array_column($fields_list, 'field_name'), $fields_list);
        $fields_list = array_slice($fields_list, 0, 8);//取出前8条，用户扩展资料默认显示8条
        foreach ($list as &$tkl) {
            $tkl['id'] = $tkl['uid'];
            $map_field['uid'] = $tkl['uid'];
            foreach ($fields_list as $key => $val) {
                $map_field['field_id'] = $val['id'];
                $field_data = D('field')->where($map_field)->getField('field_data');
                if ($field_data == null || $field_data == '') {
                    $tkl[$key] = '';
                } else {
                    $tkl[$key] = $field_data;
                }
            }
        }
        foreach ($list as &$l){
            $l['生日']=date("Y-m-d",$l['生日']);
        }
        unset($l);
        $builder = new AdminListBuilder();
        $builder->title(L('_USER_EXPAND_INFO_LIST_'));
        $builder->meta_title = L('_USER_EXPAND_INFO_LIST_');
        $builder->setSearchPostUrl(U('Admin/User/expandinfo_select'))->search(L('_SEARCH_'), 'nickname', 'text', L('_PLACEHOLDER_NICKNAME_ID_'));
        $builder->keyId()->keyLink('nickname', L('_NICKNAME_'), 'User/expandinfo_details?uid=###');
        foreach ($fields_list as $vt) {
                $builder->keyText($vt['field_name'], $vt['field_name']);
        }
        $builder->data($list);
        $builder->pagination($totalCount, $r);
        $builder->display();
    }


    /**用户扩展资料详情
     * @param string $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function expandinfo_details($uid = 0)
    {
        if (request()->isPost()) {
            /* 修改积分 xjw129xjt(肖骏涛)*/
            $data = input('post.');
            foreach ($data as $key => $val) {
                if (substr($key, 0, 5) == 'score') {
                    $data_score[$key] = $val;
                }
            }
            unset($key, $val);
            $res = model('Member')->where(array('uid' => $data['id']))->update($data_score);
            foreach ($data_score as $key => $val) {
                $value = query_user(array($key), $data['id']);
                if ($val == $value[$key]) {
                    continue;
                }
           // TODO    model('Ucenter/Score')->addScoreLog($data['id'], cut_str('score', $key, 'l'), 'to', $val, '', 0, get_nickname(is_login()) . lang('_BACKGROUND_ADJUSTMENT_'));
           // TODO    model('Ucenter/Score')->cleanUserCache($data['id'], cut_str('score', $key, 'l'));
            }
            unset($key, $val);
            /* 修改积分 end*/
            /*身份设置 zzl(郑钟良)*/
            $data_role = array();
            foreach ($data as $key => $val) {
                if ($key == 'role') {
                    $data_role = explode(',', $val);
                } else if (substr($key, 0, 4) == 'role') {
                    $data_role[] = $val;
                }
            }
            unset($key, $val);
            $this->_resetUserRole($uid, $data_role);
            /*身份设置 end*/
            //基础设置 路飞
            $field_list = $this->getRoleFieldIds();
            if ($field_list) {
                $map_field['id'] = array('in', $field_list);
            } else {
                $this->error(lang('_ERROR_INFO_SAVE_NONE_').lang('_EXCLAMATION_'));
            }

            $map_field['status'] = 1;
            $field_setting_list = db('field_setting')->where($map_field)->order('sort asc')->select();

            if (!$field_setting_list) {
                $this->error(lang('_ERROR_INFO_CHANGE_NONE_').lang('_EXCLAMATION_'));
            }

            $data = null;
            foreach ($field_setting_list as $key => $val) {
                $data[$key]['uid'] = $uid;
                $data[$key]['field_id'] = $val['id'];
                switch ($val['field_name']) {
                    case 'qq':
                        $val['value'] = op_t($_POST['qq']);
                        if (!$val['value'] || $val['value'] == '') {
                            if ($val['required'] == 1) {
                                $this->error($val['field_name'] . lang('_ERROR_CONTENT_NONE_').lang('_EXCLAMATION_'));
                            }
                        } else {
                            if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                                $this->error($val['submit']['msg']);
                            }
                        }
                        $data[$key]['field_data'] = $val['value'];
                        break;
                    case '生日':
                        $val['value'] = op_t($_POST['生日']);
                        $data[$key]['field_data'] = $val['value'];
                        break;
                    case '擅长语言':
                        $val['value'] = op_t($_POST['擅长语言']);
                        $data[$key]['field_data'] = $val['value'];
                        break;
                    case '承接项目':
                        $val['value'] = op_t($_POST['承接项目']);
                        $data[$key]['field_data'] = $val['value'];
                        break;
                    case '简介':
                        $val['value'] = op_t($_POST['简介']);
                        if (!$val['value'] || $val['value'] == '') {
                            if ($val['required'] == 1) {
                                $this->error($val['field_name'] . lang('_ERROR_CONTENT_NONE_').lang('_EXCLAMATION_'));
                            }
                        } else {
                            if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                                $this->error($val['submit']['msg']);
                            }
                        }
                        if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                            $this->error($val['submit']['msg']);
                        }
                        $data[$key]['field_data'] = $val['value'];
                        break;
                    case '其他技能':
                        $val['value'] = op_t($_POST['其他技能']);
                        $data[$key]['field_data'] = $val['value'];
                        break;
                    case '昵称':
                        $val['value'] = op_t($_POST['昵称']);
                        if (!$val['value'] || $val['value'] == '') {
                            if ($val['required'] == 1) {
                                $this->error($val['field_name'] . lang('_ERROR_CONTENT_NONE_').lang('_EXCLAMATION_'));
                            }
                        } else {
                            if ($val['submit'] != null && $val['submit']['succ'] == 0) {
                                $this->error($val['submit']['msg']);
                            }
                        }
                        $data[$key]['field_data'] = $val['value'];
                        break;
                }
            }
            $map['uid'] = $uid;
            $aNickname = input('post.nickname', '', 'text');
            $this->checkNickname($aNickname, $uid);
            $user['nickname'] = $aNickname;
            $rs_member = model('Member')->where($map)->update($user);

            //用户名、邮箱、手机变成可编辑内容
            $aUsername=input('post.username','','text');
            $aEmail=input('post.email','','text');
            $aMobile=input('post.mobile','','text');
            if($aUsername==''&&$aEmail==''&&$aMobile==''){
                $this->error('用户名、邮箱、手机号，至少填写一项！');
            }
            if($aEmail!=''){
                if (!preg_match("/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i", $aEmail)) {
                    $this->error('请正确填写邮箱！');
                }
            }
            if($aMobile!=''){
                if (!preg_match("/^\d{11}$/", $aMobile)) {
                    $this->error('请正确填写手机号！');
                }
            }
            $ucenterMemberData=array('id'=>$uid,'username'=>$aUsername,'email'=>$aEmail,'mobile'=>$aMobile);
            model('ucenter/UcenterMember')->save($ucenterMemberData);
            //用户名、邮箱、手机变成可编辑内容end

            $map['role_id'] = get_role_id();
            $is_success = false;
            foreach ($data as $dl) {
                $dl['role_id'] = $map['role_id'];

                $map['field_id'] = $dl['field_id'];
                $res = db('field')->where($map)->find();
                if (!$res) {
                    if ($dl['field_data'] != '' && $dl['field_data'] != null) {
                        $dl['createTime'] = $dl['changeTime'] = time();
                        if (!db('field')->insert($dl)) {
                            $this->error(lang('_ERROR_INFO_ADD_').lang('_EXCLAMATION_'));
                        }
                        $is_success = true;
                    }
                } else {
                    $dl['changeTime'] = time();
                    if (!db('field')->where('id=' . $res['id'])->update($dl)) {
                        $this->error(lang('_ERROR_INFO_CHANGE_').lang('_EXCLAMATION_'));
                    }
                    $is_success = true;
                }
                unset($map['field_id']);
            }
            clean_query_user_cache($uid, 'expand_info');
            if ($rs_member || $is_success) {
                $this->success(lang('_SUCCESS_SAVE_').lang('_EXCLAMATION_'));
            } else {
                $this->error(lang('_ERROR_SAVE_').lang('_EXCLAMATION_'));
            }
        } else {
            $map['uid'] = $uid;
            $map['status'] = array('egt', 0);
            $member = model('Member')->where($map)->find();
            $member['id'] = $member['uid'];
            $ucenterMember = model('ucenter/UcenterMember')->where(array('id' => $uid))->field('username,email,mobile')->find();
            $member['username']=$ucenterMember['username'];
            $member['email']=$ucenterMember['email'];
            $member['mobile']=$ucenterMember['mobile'];
            //扩展信息查询
            $map_profile['status'] = 1;
            $field_group = db('field_group')->where($map_profile)->select();
            $field_group_ids = array_column($field_group, 'id');
            $map_profile['profile_group_id'] = array('in', $field_group_ids);
            $fields_list = db('field_setting')->where($map_profile)->field('id,field_name,form_type')->select();
            $fields_list = array_combine(array_column($fields_list, 'field_name'), $fields_list);
            $map_field['uid'] = $member['uid'];
            foreach ($fields_list as $key => $val) {
                $map_field['field_id'] = $val['id'];
                $field_data = db('field')->where($map_field)->value('field_data');
                if ($field_data == null || $field_data == '') {
                    $member[$key] = '';
                } else {
                    $member[$key] = $field_data;
                }
                $member[$key] = $field_data;
            }
            $builder = new AdminConfigBuilder();
            $builder->title(lang('_USER_EXPAND_INFO_DETAIL_'));
            $builder->meta_title = lang('_USER_EXPAND_INFO_DETAIL_');
            $builder->keyId()->keyText('email','邮箱')->keyText('mobile','手机号')->keyText('username', lang('_USER_NAME_'))->keyText('nickname', lang('_NICKNAME_'));
            $field_key = array('id', 'username','email','mobile', 'nickname');
            foreach ($fields_list as $vt) {
                $field_key[] = $vt['field_name'];
            }

            /* 积分设置 xjw129xjt(肖骏涛)*/
            /*     $field = model('Ucenter/Score')->getTypeList(array('status' => 1));
                 $score_key = array();
                 foreach ($field as $vf) {
                     $score_key[] = 'score' . $vf['id'];
                     $builder->keyText('score' . $vf['id'], $vf['title']);
                 }
                 $score_data = model('Member')->where(array('uid' => $uid))->field(implode(',', $score_key))->find();
                 $member = array_merge($member, $score_data);*/
                 /*积分设置end*/
            $builder->data($member);

            /*身份设置 zzl(郑钟良)*/
            $already_role = db('UserRole')->where(array('uid' => $uid, 'status' => 1))->field('role_id')->select();
            if (count($already_role)) {
                $already_role = array_column($already_role, 'role_id');
            }
            $roleModel = model('Role');
            $role_key = array();
            $no_group_role = $roleModel->where(array('group_id' => 0, 'status' => 1))->select();
            if (count($no_group_role)) {
                $role_key[] = 'role';
                $no_group_role_options = $already_no_group_role = array();
                foreach ($no_group_role as $val) {
                    if (in_array($val['id'], $already_role)) {
                        $already_no_group_role[] = $val['id'];
                    }
                    $no_group_role_options[$val['id']] = $val['title'];
                }
                $builder->keyCheckBox('role', lang('_ROLE_GROUP_NONE_'), lang('_MULTI_OPTIONS_'), $no_group_role_options)->keyDefault('role', implode(',', $already_no_group_role));
            }
            $role_group = db('RoleGroup')->select();
            foreach ($role_group as $group) {
                $group_role = $roleModel->where(array('group_id' => $group['id'], 'status' => 1))->select();
                if (count($group_role)) {
                    $role_key[] = 'role' . $group['id'];
                    $group_role_options = $already_group_role = array();
                    foreach ($group_role as $val) {
                        if (in_array($val['id'], $already_role)) {
                            $already_group_role = $val['id'];
                        }
                        $group_role_options[$val['id']] = $val['title'];
                    }
                    $myJs = "$('.group_list').last().children().last().append('<a class=\"btn btn-default\" id=\"checkFalse\">".lang('_SELECTION_CANCEL_')."</a>');";
                    $myJs = $myJs."$('#checkFalse').click(";
                    $myJs = $myJs."function(){ $('input[type=\"radio\"]').attr(\"checked\",false)}";
                    $myJs = $myJs.");";

                    $builder->keyRadio('role' . $group['id'], lang('_ROLE_GROUP_',array('title'=>$group['title'])), lang('_ROLE_GROUP_VICE_'), $group_role_options)->keyDefault('role' . $group['id'], $already_group_role)->addCustomJs($myJs);
                }
            }
            /*身份设置 end*/
            $builder->keyText($fields_list['qq']['field_name'], $fields_list['qq']['field_name'])
                ->keyTime($fields_list['生日']['field_name'], $fields_list['生日']['field_name'])
                ->keySelect($fields_list['擅长语言']['field_name'], $fields_list['擅长语言']['field_name'], '', array('java' => 'Java', 'C++' => 'C++', 'Python' => 'Python', 'php' => 'php', 'object c' => 'object c', 'ruby' => 'ruby'))
                ->keyRadio($fields_list['承接项目']['field_name'], $fields_list['承接项目']['field_name'], '', array('是' => '是', '否' => '否'))
                ->keyTextArea($fields_list['简介']['field_name'], $fields_list['简介']['field_name'])
                ->keyCheckBox($fields_list['其他技能']['field_name'], $fields_list['其他技能']['field_name'], '', array('PhotoShop' => 'PhotoShop', 'Flash' => 'Flash'))
                ->keyText($fields_list['昵称']['field_name'], $fields_list['昵称']['field_name'])
                ->group(lang('_BASIC_SETTINGS_'), implode(',', $field_key))
            //todo    ->group(lang('_SETTINGS_SCORE_'), implode(',', $score_key))
                ->group(lang('_SETTINGS_ROLE_'), implode(',', $role_key))
                ->buttonSubmit('', lang('_SAVE_'))
                ->buttonBack();
                return $builder->fetch();
        }

    }

    /**验证用户名
     * @param $nickname
     * @author 路飞<lf@ourstu.com>
     */
    private function checkNickname($nickname, $uid)
    {
        $length = mb_strlen($nickname, 'utf8');
        if ($length == 0) {
            $this->error(L('_ERROR_NICKNAME_INPUT_').L('_PERIOD_'));
        } else if ($length > modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG')) {
            $this->error(L('_ERROR_NICKNAME_1_'). modC('NICKNAME_MAX_LENGTH',32,'USERCONFIG').L('_ERROR_NICKNAME_2_').L('_PERIOD_'));
        } else if ($length < modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG')) {
            $this->error(L('_ERROR_NICKNAME_LENGTH_1_').modC('NICKNAME_MIN_LENGTH',2,'USERCONFIG').L('_ERROR_NICKNAME_2_').L('_PERIOD_'));
        }
        $match = preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $nickname);
        if (!$match) {
            $this->error(L('_ERROR_NICKNAME_LIMIT_').L('_PERIOD_'));
        }

        $map_nickname['nickname'] = $nickname;
        $map_nickname['uid'] = array('neq', $uid);
        $had_nickname = D('Member')->where($map_nickname)->count();
        if ($had_nickname) {
            $this->error(L('_ERROR_NICKNAME_USED_').L('_PERIOD_'));
        }
        $denyName = M("Config")->where(array('name' => 'USER_NAME_BAOLIU'))->getField('value');
        if ($denyName != '') {
            $denyName = explode(',', $denyName);
            foreach ($denyName as $val) {
                if (!is_bool(strpos($nickname, $val))) {
                    $this->error(L('_ERROR_NICKNAME_FORBIDDEN_').L('_PERIOD_'));
                }
            }
        }
    }

    private function getRoleFieldIds($uid = null)
    {
        $role_id = get_role_id($uid);
        $fields_list = S('Role_Expend_Info_' . $role_id);
        if (!$fields_list) {
            $map_role_config = getRoleConfigMap('expend_field', $role_id);
            $fields_list = D('RoleConfig')->where($map_role_config)->getField('value');
            if ($fields_list) {
                $fields_list = explode(',', $fields_list);
                S('Role_Expend_Info_' . $role_id, $fields_list, 600);
            }
        }
        return $fields_list;
    }

    /**
     * 重新设置某一用户拥有身份
     * @param int $uid
     * @param array $haveRole
     * @return bool
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _resetUserRole($uid = 0, $haveRole = array())
    {
        $userRoleModel = db('UserRole');
        $memberModel = model('Common/Member');
        $map['uid'] = $uid;
        foreach ($haveRole as $val) {
            $map['role_id'] = $val;
            $userRole = $userRoleModel->where($map)->find();
            if ($userRole) {
                if (!$userRole['init']) {
                    $memberModel->initUserRoleInfo($val, $uid);
                }
                if ($userRole['status'] != 1) {
                    $userRoleModel->where($map)->setField('status', 1);
                }
            } else {
                $data = $map;
                $data['status'] = 1;
                $data['step'] = 'start';
                $data['init'] = 1;
                $res = $userRoleModel->insert($data);
                if ($res) {
                    $memberModel->initUserRoleInfo($val, $uid);
                }
            }
        }
        $map_remove['uid'] = $uid;
        $map_remove['role_id'] = array('not in', $haveRole);
        $userRoleModel->where($map_remove)->setField('status', -1);
        $user_info = $memberModel->where(array('uid' => $uid))->find();
        if (!in_array($user_info['show_role'], $haveRole)) {
            $user_data['show_role'] = $haveRole[count($haveRole) - 1];
        }
        if (!in_array($user_info['last_login_role'], $haveRole)) {
            $user_data['last_login_role'] = $haveRole[count($haveRole) - 1];
        }
        $memberModel->where(array('uid' => $uid))->update($user_data);
        return true;
    }

    /**扩展用户信息分组列表
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function profile($page = 1, $r = 20)
    {
        $map['status'] = array('egt', 0);
        $profileList = db('field_group')->where($map)->order("sort asc")->page($page, $r)->select();
        $totalCount = db('field_group')->where($map)->count();
        $builder = new AdminListBuilder();
        $builder->title(lang('_GROUP_EXPAND_INFO_LIST_'));
        $builder->meta_title = lang('_GROUP_EXPAND_INFO_');
        $builder->buttonNew(url('editProfile', array('id' => '0')))->buttonDelete(url('changeProfileStatus', array('status' => '-1')))->setStatusUrl(url('changeProfileStatus'))->buttonSort(url('sortProfile'));
        $builder->search(lang('_SEARCH_'),'profile_name','text',lang('_PLACEHOLDER_PROFILE_NAME_'));
        $builder->keyId()->keyText('profile_name', lang('_GROUP_NAME_'))->keyText('sort', lang('_SORT_'))->keyTime("createTime", lang('_CREATE_TIME_'))->keyBool('visiable', lang('_PUBLIC_IF_'));
        $builder->keyStatus()->keyDoAction('User/field?id=###', lang('_FIELD_MANAGER_'))->keyDoAction('User/editProfile?id=###', lang('_EDIT_'));
        $builder->data($profileList);
        $builder->pagination($totalCount, $r);
        return $builder->fetch();
    }

    /**扩展分组排序
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function sortProfile($ids = null)
    {
        if (request()->isPost()) {
            $builder = new AdminSortBuilder();
            $builder->doSort('Field_group', $ids);
        } else {
            $map['status'] = array('egt', 0);
            $list = db('field_group')->where($map)->order("sort asc")->select();
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['profile_name'];
            }
            $builder = new AdminSortBuilder();
            $builder->meta_title = lang('_GROUPS_SORT_');
            $builder->data($list);
            $builder->buttonSubmit(url('sortProfile'))->buttonBack();
            return $builder->fetch();
        }
    }

    /**扩展字段列表
     * @param $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function field($id, $page = 1, $r = 20)
    {
        $profile = db('field_group')->where('id=' . $id)->find();
        $map['status'] = array('egt', 0);
        $map['profile_group_id'] = $id;
        $field_list = db('field_setting')->where($map)->order("sort asc")->page($page, $r)->select();
        $totalCount = db('field_setting')->where($map)->count();
        $type_default = array(
            'input' => lang('_ONE-WAY_TEXT_BOX_'),
            'radio' => lang('_RADIO_BUTTON_'),
            'checkbox' => lang('_CHECKBOX_'),
            'select' => lang('_DROP-DOWN_BOX_'),
            'time' => lang('_DATE_'),
            'textarea' => lang('_MULTI_LINE_TEXT_BOX_')
        );
        $child_type = array(
            'string' => lang('_STRING_'),
            'phone' => lang('_PHONE_NUMBER_'),
            'email' => lang('_MAILBOX_'),
            'number' => lang('_NUMBER_'),
            'join' => lang('_RELATED_FIELD_')
        );
        foreach ($field_list as &$val) {
            $val['form_type'] = $type_default[$val['form_type']];
            if (array_key_exists($val['child_form_type'], $child_type)) {
                $val['child_form_type'] = $child_type[$val['child_form_type']];
            }else{
                $val['child_form_type'] = null;
            }
        }
        $builder = new AdminListBuilder();
        $builder->title('【' . $profile['profile_name'] . '】 字段管理');
        $builder->meta_title = $profile['profile_name'] . lang('_FIELD_MANAGEMENT_');
        $builder->buttonNew(url('editFieldSetting', array('id' => '0', 'profile_group_id' => $id)))->buttonDelete(url('setFieldSettingStatus', array('status' => '-1')))->setStatusUrl(url('setFieldSettingStatus'))->buttonSort(url('sortField', array('id' => $id)))->button(lang('_RETURN_'), array('href' => url('profile')));
        $builder->keyId()->keyText('field_name', lang('_FIELD_NAME_'))->keyBool('visiable', lang('_OPEN_YE_OR_NO_'))->keyBool('required', lang('_WHETHER_THE_REQUIRED_'))->keyText('sort', lang('_SORT_'))->keyText('form_type', lang('_FORM_TYPE_'))->keyText('child_form_type', lang('_TWO_FORM_TYPE_'))->keyText('form_default_value', lang('_DEFAULT_'))->keyText('validation', lang('_FORM_VERIFICATION_MODE_'))->keyText('input_tips', lang('_USER_INPUT_PROMPT_'));
        $builder->keyTime("createTime", lang('_CREATE_TIME_'))->keyStatus()->keyDoAction('User/editFieldSetting?profile_group_id=' . $id . '&id=###', lang('_EDIT_'));
        $builder->data($field_list);
        $builder->pagination($totalCount, $r);
        return $builder->fetch();
    }

    /**分组排序
     * @param $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function sortField($id = '', $ids = null)
    {
        if (request()->isPost()) {
            $builder = new AdminSortBuilder();
            $builder->doSort('FieldSetting', $ids);
        } else {
            $profile = db('field_group')->where('id=' . $id)->find();
            $map['status'] = array('egt', 0);
            $map['profile_group_id'] = $id;
            $list = db('field_setting')->where($map)->order("sort asc")->select();
            foreach ($list as $key => $val) {
                $list[$key]['title'] = $val['field_name'];
            }
            $builder = new AdminSortBuilder();
            $builder->meta_title = $profile['profile_name'] . lang('_FIELD_SORT_');
            $builder->data($list);
            $builder->buttonSubmit(url('sortField'))->buttonBack();
            return $builder->fetch();
        }
    }

    /**添加、编辑字段信息
     * @param $id
     * @param $profile_group_id
     * @param $field_name
     * @param $child_form_type
     * @param $visiable
     * @param $required
     * @param $form_type
     * @param $form_default_value
     * @param $validation
     * @param $input_tips
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function editFieldSetting($id = 0, $profile_group_id = 0, $field_name = '', $child_form_type = 0, $visiable = 0, $required = 0, $form_type = 0, $form_default_value = '', $validation = 0, $input_tips = '')
    {
        if (request()->isPost()) {
            $data['field_name'] = $field_name;
            if ($data['field_name'] == '') {
                $this->error(lang('_FIELD_NAME_CANNOT_BE_EMPTY_'));
            }
            $data['profile_group_id'] = $profile_group_id;
            $data['visiable'] = $visiable;
            $data['required'] = $required;
            $data['form_type'] = $form_type;
            $data['form_default_value'] = $form_default_value;
            //当表单类型为以下三种是默认值不能为空判断@MingYang
            $form_types = array('radio', 'checkbox', 'select');
            if (in_array($data['form_type'], $form_types)) {
                if ($data['form_default_value'] == '') {
                    $this->error($data['form_type'] . lang('_THE_DEFAULT_VALUE_OF_THE_FORM_TYPE_CAN_NOT_BE_EMPTY_'));
                }
            }
            $data['input_tips'] = $input_tips;
            //增加当二级字段类型为join时也提交$child_form_type @MingYang
            if ($form_type == 'input') {
                $data['child_form_type'] = $child_form_type;
            } else {
                $data['child_form_type'] = '';
            }
            $data['validation'] = $validation;
            if ($id != '') {
                $res = db('field_setting')->where('id=' . $id)->update($data);
            } else {
                $map['field_name'] = $field_name;
                $map['status'] = array('egt', 0);
                $map['profile_group_id'] = $profile_group_id;
                if (db('field_setting')->where($map)->count() > 0) {
                    $this->error(lang('_THIS_GROUP_ALREADY_HAS_THE_SAME_NAME_FIELD_PLEASE_USE_ANOTHER_NAME_'));
                }
                $data['status'] = 1;
                $data['createTime'] = time();
                $data['sort'] = 0;
                $res = db('field_setting')->insert($data);
            }
            $role_ids = input('post.role_ids', array());
            $this->_setFieldRole($role_ids, $res, $id);
            $this->success($id == '' ? lang('_ADD_FIELD_SUCCESS_') : lang('_EDIT_FIELD_SUCCESS_'), url('field', array('id' => $profile_group_id)));
        } else {
            $roleOptions = db('Role')->field('id,title')->where(array('status' => array('gt', -1)))->order('id asc')->select();

            $builder = new AdminConfigBuilder();
            if ($id != 0) {
                $field_setting = db('field_setting')->where('id=' . $id)->find();

                //所属身份
                $roleConfigModel = db('RoleConfig');
                $map = getRoleConfigMap('expend_field', 0);
                unset($map['role_id']);
                $already_role_id = $roleConfigModel->where($map)->where('value',['like','%,'.$id.',%'],['like',$id.',%'],['like','%,'.$id],['like',$id],'or')
                   ->field('role_id')->select();
                $already_role_id = array_column($already_role_id, 'role_id');
                $field_setting['role_ids'] = $already_role_id;
                //所属身份 end

                $builder->title(lang('_MODIFY_FIELD_INFORMATION_'));
                $builder->meta_title = lang('_MODIFY_FIELD_INFORMATION_');
            } else {
                $builder->title(lang('_ADD_FIELD_'));
                $builder->meta_title = lang('_NEW_FIELD_');
                $field_setting['profile_group_id'] = $profile_group_id;
                $field_setting['visiable'] = 1;
                $field_setting['required'] = 1;
            }
            $type_default = array(
                'input' => lang('_ONE-WAY_TEXT_BOX_'),
                'radio' => lang('_RADIO_BUTTON_'),
                'checkbox' => lang('_CHECKBOX_'),
                'select' => lang('_DROP-DOWN_BOX_'),
                'time' => lang('_DATE_'),
                'textarea' => lang('_MULTI_LINE_TEXT_BOX_')
            );
            $child_type = array(
                'string' => lang('_STRING_'),
                'phone' => lang('_PHONE_NUMBER_'),
                'email' => lang('_MAILBOX_'),
                //增加可选择关联字段类型 @MingYang
                'join' => lang('_RELATED_FIELD_'),
                'number' => lang('_NUMBER_')
            );
            $builder->keyReadOnly("id", lang('_LOGO_'))->keyReadOnly('profile_group_id', lang('_GROUP_ID_'))->keyText('field_name', lang('_FIELD_NAME_'))->keyChosen('role_ids', lang('_POSSESSION_OF_THE_FIELD_'), lang('_DETAIL_COME_TO_'), $roleOptions)->keySelect('form_type', lang('_FORM_TYPE_'), '', $type_default)->keySelect('child_form_type', lang('_TWO_FORM_TYPE_'), '', $child_type)->keyTextArea('form_default_value', "多个值用'|'分割开,格式【字符串：男|女，数组：1:男|2:女，关联数据表：字段名|表名】开")
                ->keyText('validation', lang('_FORM_VALIDATION_RULES_'), '例：min=5&max=10')->keyText('input_tips', lang('_USER_INPUT_PROMPT_'), lang('_PROMPTS_THE_USER_TO_ENTER_THE_FIELD_INFORMATION_'))->keyBool('visiable', lang('_OPEN_YE_OR_NO_'))->keyBool('required', lang('_WHETHER_THE_REQUIRED_'));
            $builder->data($field_setting);
            $builder->buttonSubmit(url('editFieldSetting'), $id == 0 ? lang('_ADD_') : lang('_MODIFY_'))->buttonBack();
            return $builder->fetch();
        }

    }

    /**设置字段状态：删除=-1，禁用=0，启用=1
     * @param $ids
     * @param $status
     * @author Patrick <contact@uctoo.com>
     */
    public function setFieldSettingStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('field_setting', $ids, $status);
    }

    /**设置分组状态：删除=-1，禁用=0，启用=1
     * @param $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function changeProfileStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('field_group', $ids, $status);
    }

    /**添加、编辑分组信息
     * @param $id
     * * @param $profile_name
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function editProfile($id = 0, $profile_name = '', $visiable = 1)
    {
        if (request()->isPost()) {
            $data['profile_name'] = $profile_name;
            $data['visiable'] = $visiable;
            if ($data['profile_name'] == '') {
                $this->error(lang('_GROUP_NAME_CANNOT_BE_EMPTY_'));
            }
            if ($id != '') {
                $res = db('field_group')->where('id=' . $id)->update($data);
            } else {
                $map['profile_name'] = $profile_name;
                $map['status'] = array('egt', 0);
                if (db('field_group')->where($map)->count() > 0) {
                    $this->error(lang('_ALREADY_HAS_THE_SAME_NAME_GROUP_PLEASE_USE_THE_OTHER_GROUP_NAME_'));
                }
                $data['status'] = 1;
                $data['createTime'] = time();
                $res = db('field_group')->insert($data);
            }
            if ($res) {
                $this->success($id == '' ? lang('_ADD_GROUP_SUCCESS_') : lang('_EDIT_GROUP_SUCCESS_'), url('profile'));
            } else {
                $this->error($id == '' ? lang('_ADD_GROUP_FAILURE_') : lang('_EDIT_GROUP_FAILED_'));
            }
        } else {
            $builder = new AdminConfigBuilder();
            if ($id != 0) {
                $profile = db('field_group')->where('id=' . $id)->find();
                $builder->title(lang('_MODIFIED_GROUP_INFORMATION_'));
                $builder->meta_title = lang('_MODIFIED_GROUP_INFORMATION_');
            } else {
                $profile = null;
                $builder->title(lang('_ADD_EXTENDED_INFORMATION_PACKET_'));
                $builder->meta_title = lang('_NEW_GROUP_');
            }
            $builder->keyReadOnly("id", lang('_LOGO_'))->keyText('profile_name', lang('_GROUP_NAME_'))->keyBool('visiable', lang('_OPEN_YE_OR_NO_'));
            $builder->data($profile);
            $builder->buttonSubmit(url('editProfile'), $id == 0 ? lang('_ADD_') : lang('_MODIFY_'))->buttonBack();
            return $builder->fetch();
        }

    }

    /**
     * 修改昵称初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updateNickname()
    {
        $user = model('Member')->get(UID);
        $nickname = $user->nickname;
        $this->assign('user', $user);
        $this->assign('nickname', $nickname);
        $this->meta_title = lang('_MODIFY_NICKNAME_');
        return $this->fetch();
    }

    /**
     * 修改昵称提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitNickname()
    {
        //获取参数
        $nickname = input('post.nickname');
        $password = input('post.password');
        empty($nickname) && $this->error(lang('_PLEASE_ENTER_A_NICKNAME_'));
        empty($password) && $this->error(lang('_PLEASE_ENTER_THE_PASSWORD_'));

        //密码验证
        $User = new UcenterMember();
        $uid = $User->login(UID, $password, 4);
        ($uid == -2) && $this->error(lang('_INCORRECT_PASSWORD_'));

        $Member = model('Member');
        $Member->nickname = $nickname;

        $data = ['nickname'=>$nickname ];

        $validate = Loader::validate('Member');

        if(!$validate->check($data)){
            return ['status'=>false,'msg'=>$validate->getError(),'url'=>url('updateNickname')];
        }else{
            $res = $Member->where(array('uid' => $uid))->update($data);

        if ($res) {
            $user = session('user_auth');
            $user['username'] = $data['nickname'];
            session('user_auth', $user);
            session('user_auth_sign', data_auth_sign($user));
            $this->success(lang('_MODIFY_NICKNAME_SUCCESS_'));
        } else {
            $this->error(lang('_MODIFY_NICKNAME_FAILURE_'));
        }
        }
    }

    /**
     * 修改密码初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updatePassword()
    {
        $this->meta_title = lang('_CHANGE_PASSWORD_');
        return $this->fetch();
    }

    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitPassword()
    {
        //获取参数
        $password = input('post.old');
        empty($password) && $this->error(lang('_PLEASE_ENTER_THE_ORIGINAL_PASSWORD_'));
        $data['password'] = input('post.password');
        empty($data['password']) && $this->error(lang('_PLEASE_ENTER_A_NEW_PASSWORD_'));
        $repassword = input('post.repassword');
        empty($repassword) && $this->error(lang('_PLEASE_ENTER_THE_CONFIRMATION_PASSWORD_'));

        if ($data['password'] !== $repassword) {
            $this->error(lang('_YOUR_NEW_PASSWORD_IS_NOT_CONSISTENT_WITH_THE_CONFIRMATION_PASSWORD_'));
        }

        //密码验证
        $User = new UcenterMember();
        $uid = $User->login(UID, $password, 4);
        ($uid == -2) && $this->error(lang('_INCORRECT_PASSWORD_'));

        $res = $User->updateUserFieldsPwd(UID, $password, $data);
        if (!$res) {
            $this->error(lang('_CHANGE_PASSWORD_FAIL_'));
        } else {
            $this->success(lang('_CHANGE_PASSWORD_SUCCESS_'));
        }
    }

    /**
     * 修改用户名
     * @author patrick <contact@uctoo.com>
     */
    public function updateUsername()
    {
        $this->meta_title = lang('_MODIFY_USERNAME_');
        $model = model('ucenter/UcenterMember');
        if (request()->isPost()) {   //提交表单
            //获取参数
            $username = input('post.username');
            $mobile = input('post.mobile');
            $password = input('post.password');
            empty($username) && $this->error(lang('_PLEASE_ENTER_A_USERNAME_'));
            empty($mobile) && $this->error(lang('_PLEASE_ENTER_A_MOBILE_'));
            empty($password) && $this->error(lang('_PLEASE_ENTER_THE_PASSWORD_'));

            //密码验证
            $User = new UcenterMember();
            $uid = $User->login(UID, $password, 4);
            ($uid == -2) && $this->error(lang('_INCORRECT_PASSWORD_'));

            $UserData =  model('ucenter/UcenterMember')->get($uid);

            $validate = Loader::validate('ucenter/UcenterMember');
            $res = 0;
            if($UserData['username'] != $username){
                $data = [ 'username'=>$username];

                if(!$validate->scene('updateUsername')->check($data)){
                    return ['status'=>false,'message'=>$validate->getError(),'url'=>url('updateUsername')];
                }else{
                    $res = $User->where(array('id' => $uid))->update($data);
                }
            }

            if($UserData['mobile'] != $mobile){
                $data = [ 'mobile'=>$mobile];
                if(!$validate->scene('updateMobile')->check($data)){
                    return ['status'=>false,'message'=>$validate->getError(),'url'=>url('updateUsername')];
                }else{
                    $res = $User->where(array('id' => $uid))->update($data);
                }
            }
            if ($res) {
                $this->success(lang('_MODIFY_USERNAME_SUCCESS_'));
            } else {
                $this->error(lang('_MODIFY_USERNAME_FAILURE_'));
            }

        }else{   //显示表单
            //读取帐号信息
            if (UID != 0) {  //编辑
                $user = $model->where(array('id' => UID))->find();
            } else {  //编辑错误
                $this->error(lang('_MODIFY_USERNAME_FAILURE_'));
            }

            //显示页面
            $builder = new AdminConfigBuilder();

            $builder->keyPassword('password', '密码', '验证密码')
                ->keyText('username', '后台用户名', '后台登录用户名，真实用户名')
                ->keyText('mobile', '前台用户名', '即手机号码，前台仅可通过手机号码或社交帐号登录')
                ->data($user->toArray())        //model查询返回的是对象，转成数组才可用
                ->buttonSubmit(url('updateUsername'))->buttonBack();
            return $builder->fetch();
        }
    }

    /**
     * 用户行为列表
     * @author huajie <banhuajie@163.com>
     */
    public function action()
    {
        // $aModule = I('post.module', '-1', 'text');
        $aModule = $this->parseSearchKey('module');

        is_null($aModule) && $aModule = -1;
        if ($aModule != -1) {
            $map['module'] = $aModule;
        }
        unset($_REQUEST['module']);
        $this->assign('current_module', $aModule);
        $map['status'] = array('gt', -1);
        //获取列表数据
        $Action = M('Action')->where(array('status' => array('gt', -1)));

        $list = $this->lists($Action, $map);
        lists_plus($list);
        int_to_string($list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('_list', $list);
        $module = D('Common/Module')->getAll();
        foreach ($module as $key => $v) {
            if ($v['is_setup'] == false) {
                unset($module[$key]);
            }
        }
        $module = array_merge(array(array('name' => '', 'alias' => L('_SYSTEM_'))), $module);
        $this->assign('module', $module);

        $this->meta_title = L('_USER_BEHAVIOR_');
        $this->display();
    }

    protected function parseSearchKey($key = null)
    {
        $action = MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME;
        $post = I('post.');
        if (empty($post)) {
            $keywords = cookie($action);
        } else {
            $keywords = $post;
            cookie($action, $post);
            $_GET['page'] = 1;
        }

        if (!$_GET['page']) {
            cookie($action, null);
            $keywords = null;
        }
        return $key ? $keywords[$key] : $keywords;
    }

    /**
     * 新增行为
     * @author huajie <banhuajie@163.com>
     */
    public function addAction()
    {
        $this->meta_title = L('_NEW_BEHAVIOR_');


        $module = D('Module')->getAll();
        $this->assign('module', $module);
        $this->assign('data', null);
        $this->display('editaction');
    }

    /**
     * 编辑行为
     * @author huajie <banhuajie@163.com>
     */
    public function editAction()
    {
        $id = I('get.id');
        empty($id) && $this->error(L('_PARAMETERS_CANT_BE_EMPTY_'));
        $data = M('Action')->field(true)->find($id);

        $module = D('Module')->getAll();
        $this->assign('module', $module);
        $this->assign('data', $data);
        $this->meta_title = L('_EDITING_BEHAVIOR_');
        $this->display();
    }

    /**
     * 更新行为
     * @author huajie <banhuajie@163.com>
     */
    public function saveAction()
    {
        $res = D('Action')->update();
        if (!$res) {
            $this->error(D('Action')->getError());
        } else {
            $this->success($res['id'] ? L('_UPDATE_SUCCESS_') : L('_NEW_SUCCESS_'), Cookie('__forward__'));
        }
    }

    /**
     * 会员状态修改
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function changeStatus($method = null)
    {
        $id = array_unique((array)input('id', 0));
        if (count(array_intersect(explode(',', config('USER_ADMINISTRATOR')), $id)) > 0) {
            $this->error(lang('_DO_NOT_ALLOW_THE_SUPER_ADMINISTRATOR_TO_PERFORM_THE_OPERATION_'));
        }
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error(lang('_PLEASE_CHOOSE_TO_OPERATE_THE_DATA_'));
        }
        $map['uid'] = array('in', $id);
        $map1['id'] = array('in', $id);
        switch (strtolower($method)) {
            case 'forbiduser':
                $data = array('status' => 0);
                db('ucenter_member')->where($map1)->update($data);
                db('Member')->where($map)->update($data);
                break;
            case 'resumeuser':
                $data = array('status' => 1);
                db('ucenter_member')->where($map1)->update($data);
                db('Member')->where($map)->update($data);
                break;
            case 'deleteuser':
                $data = array('status' => -1);
                db('ucenter_member')->where($map1)->update($data);
                db('Member')->where($map)->update($data);
                break;
            default:
                $this->error( '参数非法');
        }
    }


    /**
     * 获取用户注册错误信息
     * @param  integer $code 错误编码
     * @return string        错误信息
     */
    private function showRegError($code = 0)
    {
        switch ($code) {
            case -1:
                $error = L('_USER_NAME_MUST_BE_IN_LENGTH_') . modC('USERNAME_MIN_LENGTH', 2, 'USERCONFIG') . '-' . modC('USERNAME_MAX_LENGTH', 32, 'USERCONFIG') . L('_BETWEEN_CHARACTERS_');
                break;
            case -2:
                $error = L('_USER_NAME_IS_FORBIDDEN_TO_REGISTER_');
                break;
            case -3:
                $error = L('_USER_NAME_IS_OCCUPIED_');
                break;
            case -4:
                $error = L('_PASSWORD_LENGTH_MUST_BE_BETWEEN_6-30_CHARACTERS_');
                break;
            case -5:
                $error = L('_MAILBOX_FORMAT_IS_NOT_CORRECT_');
                break;
            case -6:
                $error = L('_MAILBOX_LENGTH_MUST_BE_BETWEEN_1-32_CHARACTERS_');
                break;
            case -7:
                $error = L('_MAILBOX_IS_PROHIBITED_TO_REGISTER_');
                break;
            case -8:
                $error = L('_MAILBOX_IS_OCCUPIED_');
                break;
            case -9:
                $error = L('_MOBILE_PHONE_FORMAT_IS_NOT_CORRECT_');
                break;
            case -10:
                $error = L('_MOBILE_PHONES_ARE_PROHIBITED_FROM_REGISTERING_');
                break;
            case -11:
                $error = L('_PHONE_NUMBER_IS_OCCUPIED_');
                break;
            case -12:
                $error = L('_USER_NAME_MY_RULE_').L('_EXCLAMATION_');
                break;
            default:
                $error = L('_UNKNOWN_ERROR_');
        }
        return $error;
    }


    public function scoreList()
    {
        //读取数据
        $map = array('status' => array('GT', -1));
        $model = D('Ucenter/Score');
        $list = $model->getTypeList($map);

        //显示页面
        $builder = new AdminListBuilder();
        $builder
            ->title(L('_INTEGRAL_TYPE_'))
            ->suggest(L('_CANNOT_DELETE_ID_4_'))
            ->buttonNew(U('editScoreType'))
            ->setStatusUrl(U('setTypeStatus'))->buttonEnable()->buttonDisable()->button(L('_DELETE_'), array('class' => 'btn ajax-post tox-confirm', 'data-confirm' => '您确实要删除积分分类吗？（删除后对应的积分将会清空，不可恢复，请谨慎删除！）', 'url' => U('delType'), 'target-form' => 'ids'))
            ->button(L('_RECHARGE_'), array('href' => U('recharge')))
            ->keyId()->keyText('title', L('_NAME_'))
            ->keyText('unit', L('_UNIT_'))->keyStatus()->keyDoActionEdit('editScoreType?id=###')
            ->data($list)
            ->display();
    }

    public function recharge()
    {
        $scoreTypes = D('Ucenter/Score')->getTypeList(array('status' => 1));
        if (IS_POST) {
            $aUids = I('post.uid');
            foreach ($scoreTypes as $v) {
                $aAction = I('post.action_score' . $v['id'], '', 'op_t');
                $aValue = I('post.value_score' . $v['id'], 0, 'intval');
                D('Ucenter/Score')->setUserScore($aUids, $aValue, $v['id'], $aAction, '', 0, L('_BACKGROUND_ADMINISTRATOR_RECHARGE_PAGE_RECHARGE_'));
                D('Ucenter/Score')->cleanUserCache($aUids, $aValue);

            }
            $this->success(L('_SET_UP_'), 'refresh');
        } else {

            $this->assign('scoreTypes', $scoreTypes);
            $this->display();
        }
    }

    public function getNickname()
    {
        $uid = I('get.uid', 0, 'intval');
        if ($uid) {
            $user = query_user(null, $uid);
            $this->ajaxReturn($user);
        } else {
            $this->ajaxReturn(null);
        }

    }

    public function setTypeStatus($ids, $status)
    {
        $builder = new AdminListBuilder();
        $builder->doSetStatus('ucenter_score_type', $ids, $status);

    }

    public function delType($ids)
    {
        $model = D('Ucenter/Score');
        $res = $model->delType($ids);
        if ($res) {
            $this->success(L('_DELETE_SUCCESS_'));
        } else {
            $this->error(L('_DELETE_FAILED_'));
        }
    }

    public function editScoreType()
    {
        $aId = I('id', 0, 'intval');
        $model = D('Ucenter/Score');
        if (IS_POST) {
            $data['title'] = I('post.title', '', 'op_t');
            $data['status'] = I('post.status', 1, 'intval');
            $data['unit'] = I('post.unit', '', 'op_t');

            if ($aId != 0) {
                $data['id'] = $aId;
                $res = $model->editType($data);
            } else {
                $res = $model->addType($data);
            }
            if ($res) {
                $this->success(($aId == 0 ? L('_ADD_') : L('_EDIT_')) . L('_SUCCESS_'));
            } else {
                $this->error(($aId == 0 ? L('_ADD_') : L('_EDIT_')) . L('_FAILURE_'));
            }
        } else {
            $builder = new AdminConfigBuilder();
            if ($aId != 0) {
                $type = $model->getType(array('id' => $aId));
            } else {
                $type = array('status' => 1, 'sort' => 0);
            }
            $builder->title(($aId == 0 ? L('_NEW_') : L('_EDIT_')) . L('_INTEGRAL_CLASSIFICATION_'))->keyId()->keyText('title', L('_NAME_'))
                ->keyText('unit', L('_UNIT_'))
                ->keySelect('status', L('_STATUS_'), null, array(-1 => L('_DELETE_'), 0 => L('_DISABLE_'), 1 => L('_ENABLE_')))
                ->data($type)
                ->buttonSubmit(U('editScoreType'))->buttonBack()->display();
        }
    }

    /**
     * 重新设置拥有字段的身份
     * @param $role_ids 身份ids
     * @param $add_id 新增字段时字段id
     * @param $edit_id 编辑字段时字段id
     * @return bool
     * @author 郑钟良<zzl@ourstu.com>
     */
    private function _setFieldRole($role_ids, $add_id, $edit_id)
    {
        $type = 'expend_field';
        $roleConfigModel = D('RoleConfig');
        $map = getRoleConfigMap($type, 0);
        if ($edit_id) {//编辑字段
            unset($map['role_id']);
            $map['value'] = array('like', array('%,' . $edit_id . ',%', $edit_id . ',%', '%,' . $edit_id, $edit_id), 'or');
            $already_role_id = $roleConfigModel->where($map)->select();
            $already_role_id = array_column($already_role_id, 'role_id');

            unset($map['value']);
            if (count($role_ids) && count($already_role_id)) {
                $need_add_role_ids = array_diff($role_ids, $already_role_id);
                $need_del_role_ids = array_diff($already_role_id, $role_ids);
            } else if (count($role_ids)) {
                $need_add_role_ids = $role_ids;
            } else {
                $need_del_role_ids = $already_role_id;
            }

            foreach ($need_add_role_ids as $val) {
                $map['role_id'] = $val;
                $oldConfig = $roleConfigModel->where($map)->find();
                if (count($oldConfig)) {
                    $oldConfig['value'] = implode(',', array_merge(explode(',', $oldConfig['value']), array($edit_id)));
                    $roleConfigModel->saveData($map, $oldConfig);
                } else {
                    $data = $map;
                    $data['value'] = $edit_id;
                    $roleConfigModel->addData($data);
                }
            }

            foreach ($need_del_role_ids as $val) {
                $map['role_id'] = $val;
                $oldConfig = $roleConfigModel->where($map)->find();
                $oldConfig['value'] = array_diff(explode(',', $oldConfig['value']), array($edit_id));
                if (count($oldConfig['value'])) {
                    $oldConfig['value'] = implode(',', $oldConfig['value']);
                    $roleConfigModel->saveData($map, $oldConfig);
                } else {
                    $roleConfigModel->deleteData($map);
                }
            }

        } else {//新增字段
            foreach ($role_ids as $val) {
                $map['role_id'] = $val;
                $oldConfig = $roleConfigModel->where($map)->find();
                if (count($oldConfig)) {
                    $oldConfig['value'] = implode(',', array_unique(array_merge(explode(',', $oldConfig['value']), array($add_id))));
                    $roleConfigModel->saveData($map, $oldConfig);
                } else {
                    $data = $map;
                    $data['value'] = $add_id;
                    $roleConfigModel->addData($data);
                }
            }
        }
        return true;
    }

    /**
     * 修改第三方登录用户邮箱后缀
     * @author 王杰<wj@ourstu.com>
     */
    public function emailSuffix()
    {
        $id = array_unique((array)I('id', 0));
        if (count(array_intersect(explode(',', C('USER_ADMINISTRATOR')), $id)) > 0) {
            $this->error(L('_DO_NOT_ALLOW_THE_SUPER_ADMINISTRATOR_TO_PERFORM_THE_OPERATION_'));
        }
        $id = is_array($id) ? implode(',', $id) : $id;
        if (empty($id)) {
            $this->error(L('_PLEASE_CHOOSE_TO_OPERATE_THE_DATA_'));
        }
        $map['id'] = array('in', $id);
        $data = modC('SYNC_LOGIN_EMAIL_SUFFIX','@ourstu.com','USERCONFIG');
        $user = M('ucenter_member')->where($map)->select();
        foreach ($user as $v) {
            $email = preg_replace("/@(([0-9a-z]+)[.]){1,2}[a-z]{2,3}$/",$data,$v['email']);
            if ($email) {
                M('ucenter_member')->where(array('id'=>$v['id']))->save(array('email'=>$email));
                clean_query_user_cache($v['id'],'email');
            }
        }
        $this->success('已执行');
    }
}
