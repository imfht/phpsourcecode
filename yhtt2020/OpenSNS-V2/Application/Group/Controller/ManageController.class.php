<?php
namespace Group\Controller;
use Think\Controller;

class ManageController extends BaseController
{

    private $groupId = '';

    public function _initialize()
    {

        $aGroupId = I('group_id', 0, 'intval');
        $this->groupId = $aGroupId;
        parent::_initialize();
        //判断是否有权限编辑
        if(!is_login()){
            $this->error(L('_FIRST_LOGIN_'));
        }
        $this->checkAuth('Group/Manager/*',get_group_admin($this->groupId),L('_AUTHORITY_RUIN_'));
        $this->assignNotice($this->groupId);
        $this->assign('group_id', $this->groupId);
        unset($e);
        $myInfo = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), is_login());
        $this->assign('myInfo', $myInfo);
        //赋予贴吧列表

        $this->assign('current', 'mygroup');

    }


    public function index()
    {
        $this->assignGroup($this->groupId);
        $this->assignGroupAllType();
        $this->setTitle(L('_GROUP_MANAGE_EDIT_'));
        $this->display();
    }

    public function member()
    {
        $aPage = I('get.page', 1, 'intval');
        $aStatus = I('get.status', 1, 'intval');
        $this->assignGroup($this->groupId);
        $group_admin = M('Group')->where(array('id'=>$this->groupId))->field('uid')->find() ;
        $map['group_id'] = $this->groupId ;
        $map['status'] = $aStatus ;
        $map['uid'] = array('neq', $group_admin['uid']) ;
        $member = D('GroupMember')->where($map)->page($aPage, 10)->select();
        $totalCount = D('GroupMember')->where($map)->count();
        foreach ($member as &$v) {
            $v['user'] = query_user(array('avatar128', 'avatar64', 'nickname', 'uid', 'space_url'), $v['uid']);
        }
        $this->assign('member', $member);
        $this->assign('status', $aStatus);
        $this->assign('totalCount', $totalCount);
        $map['status'] = 1 ;
        $this->assign('sh_count', D('GroupMember')->where($map)->count());
        $map['status'] = 0 ;
        $this->assign('wsh_count', D('GroupMember')->where($map)->count());
        $this->setTitle(L("_GROUP_MANAGE_MEMBER_"));
        $this->display();
    }


    public function notice()
    {
        $this->assignGroup($this->groupId);
        if (IS_POST) {
            $aNotice = I('post.notice', '', 'text');
            $data['group_id'] = $this->groupId;
            $data['content'] = $aNotice;
            $res = D('GroupNotice')->addNotice($data);
            if ($res) {
                $this->success(L('_ADD_SUCCESS_'), 'refresh');
            } else {
                $this->error(L('_ADD_FAIL_'));
            }
        } else {

            $this->assign('group_id', $this->groupId);
            $this->setTitle(L('_GROUP_MANAGE_NOTICE_'));
            $this->display();
        }
    }

    public function category()
    {
        $this->assignGroup($this->groupId);
        $this->assignGroupTypes();
        $this->setTitle(L('_GROUP_MANAGE_CATEGORY_'));
        $cate = D('GroupPostCategory')->where(array('group_id' => $this->groupId, 'status' => 1))->select();
        $this->assign('cate', $cate);
        $this->display();
    }


    public function dismiss()
    {
        $this->checkAuth('Group/Manager/dismiss',get_group_creator($this->groupId),L('_LIMIT_DISMISS_'));
        $res = D('Group')->delGroup($this->groupId);
        $map=D('GroupPost')->where(array('group_id'=>$this->groupId,'status'=>1))->setField(array('status'=>-1));
        if ($res && $map) {
            $this->success(L('_DISMISS_SUCCESS_'), U('group/index/index'));
        } else {
            $this->error(L('_DISMISS_FAIL_'));
        }
    }


    public function receiveMember()
    {
        $aUid = I('post.uid', 0, 'intval');
        $res = D('GroupMember')->setStatus($aUid, $this->groupId, 1);
        $dynamic['group_id'] = $this->groupId;
        $dynamic['uid'] = $aUid;
        $dynamic['type'] = 'attend';
        $dynamic['create_time'] = time();
        D('GroupDynamic')->add($dynamic);
        if ($res) {
            $group = D('Group')->getGroup($this->groupId);
            D('Message')->sendMessage($aUid,L('_GROUP_AUDIT_SUCCESS_'), get_nickname(is_login()) . L('_TIP_JOIN_GROUP_1_')."【{$group['title']}】".L('_TIP_JOIN_GROUP_2_'),  'group/index/group', array('id' => $this->groupId), is_login());
            S('group_member_count_' . $group['id'],null);
            S('group_is_join_' . $group['id'] . '_' . $aUid, null);
            $this->success(L('_AUDIT_SUCCESS_'), 'refresh');
        } else {
            $this->error(L('_AUDIT_FAIL_'));
        }
    }


    public function removeGroupMember()
    {
        $aUid = I('post.uid', 0, 'intval');
        $group_admin = M('Group')->where(array('id'=>$this->groupId))->field('uid')->find() ;
        if($group_admin['uid'] == $aUid) {
            $this->error('无法移除群主~');
        }
        $res = D('GroupMember')->where(array('uid' => $aUid, 'group_id' => $this->groupId))->delete();
        $dynamic['group_id'] = $this->groupId;
        $dynamic['uid'] = $aUid;
        $dynamic['type'] = 'remove';
        $dynamic['create_time'] = time();
        D('GroupDynamic')->add($dynamic);
        if ($res) {
            $group = D('Group')->getGroup($this->groupId);
            D('Message')->sendMessage($aUid,L('_GROUP_REMOVE_'), get_nickname(is_login()) .L('_TIP_GROUP_REMOVED_')."【{$group['title']}】", 'group/index/group', array('id' => $this->groupId), is_login());
            S('group_member_count_' . $group['id'],null);
            S('group_is_join_' . $group['id'] . '_' . $aUid, null);
            $this->success(L('_DELETE_SUCCESS_'), 'refresh');
        } else {
            $this->error(L('_DELETE_FAIL_'));
        }
    }


    public function editCate()
    {
        $aCateId = I('post.cate_id', 0, 'intval');
        $aTitle = I('post.title', '', 'text');

        if (empty($aTitle)) {
            $this->error(L('_CATEGORY_NAME_CANNOT_EMPTY_'));
        }
        if ($aCateId == 0) {
            $res = D('GroupPostCategory')->add(array('group_id' => $this->groupId, 'title' => $aTitle, 'create_time' => time(), 'status' => 1, 'sort' => 0));
            if (!$res) {
                $this->error(L('_ADD_CATEGORY_FAIL_'));
            }
            $this->success(L('_ADD_CATEGORY_SUCCESS_'), 'refresh');
        } else {
            $res = D('GroupPostCategory')->where(array('id' => $aCateId))->save(array('title' => $aTitle));
            if (!$res) {
                $this->error(L('_EDIT_CATEGORY_SUCCESS_'));
            }
            $this->success(L('_EDIT_CATEGORY_SUCCESS_'), 'refresh');
        }
    }


    public function delCate()
    {
        $aCateId = I('post.cate_id', 0, 'intval');
        $res = D('GroupPostCategory')->where(array('id' => $aCateId))->setField('status', 0);
        if (!$res) {
            $this->error(L('_DELETE_CATEGORY_FAIL_'));
        }
        $this->success(L('_DELETE_CATEGORY_SUCCESS_'), 'refresh');
    }

}